<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\RecuperacaoSenhaController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\DesativarRememberMe;
use App\Http\Middleware\RestringirSessaoTemporaria;
use App\Mail\SenhaTemporaria;
use App\Models\User;
use App\Services\LimitesRecuperacao;
use App\Services\RecuperacaoSenhaService;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Mail\MailManager;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\DatabaseSessionHandler;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Timebox;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class RecuperacaoSenhaTest extends TestCase
{
    private RecuperacaoSenhaService $service;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Never resolve any application connection. Explicit isolated connection only.
        config(['database.default' => 'recovery_test', 'database.connections' => ['recovery_test' => [
            'driver' => 'pgsql', 'host' => '127.0.0.1', 'port' => 55438,
            'database' => 'postgres', 'username' => 'siapra_test', 'password' => '',
            'charset' => 'utf8', 'prefix' => '', 'search_path' => 'pg_temp', 'sslmode' => 'disable',
        ]], 'cache.default' => 'array', 'session.driver' => 'array',
            'recuperacao.floor_ms' => 0, 'session.connection' => null]);
        if (getenv('SIAPRA_RECOVERY_TEST') !== '1') {
            $this->markTestSkipped('Requer cluster descartável recovery-pg na porta 55438.');
        }
        $identity = DB::selectOne("SELECT current_setting('data_directory') AS dir, inet_server_port() AS port, current_user AS usr");
        $expected = str_replace('\\', '/', base_path('storage/framework/testing/recovery-pg'));
        $this->assertSame(strtolower($expected), strtolower(str_replace('\\', '/', $identity->dir)));
        $this->assertSame(55438, $identity->port);
        $this->assertSame('siapra_test', $identity->usr);
        DB::statement('CREATE TEMP TABLE users (id bigserial primary key, username varchar(255), password varchar(255), email varchar(255), remember_token varchar(100))');
        DB::statement('CREATE TEMP TABLE acoes (id bigserial primary key, descricao varchar(255))');
        DB::statement('CREATE TEMP TABLE logs (id bigserial primary key, id_user bigint REFERENCES users(id), id_acao bigint REFERENCES acoes(id), tipo varchar(30), descricao text, data_hora timestamp)');
        DB::statement('CREATE TEMP TABLE sessions (id varchar(255) primary key, user_id bigint, ip_address varchar(45), user_agent text, payload text, last_activity integer)');
        (require base_path('database/migrations/2026_11_08_000001_add_temporary_password_to_users.php'))->up();
        $this->service = app(RecuperacaoSenhaService::class);
        $this->user = User::create(['username' => 'Pessoa', 'password' => 'definitiva123', 'email' => 'pessoa@example.org']);
        Mail::fake();
        Route::middleware(['web', 'auth'])->get('/recovery-test-normal', fn () => 'ok');
    }

    protected function tearDown(): void
    {
        if (isset($this->service)) {
            DB::disconnect('recovery_test');
        }
        parent::tearDown();
    }

    private function issue(): string
    {
        $this->service->issue('Pessoa');
        $this->user->refresh();

        return Mail::sent(SenhaTemporaria::class)->last()->senha;
    }

    private function requestFor(array $marker): Request
    {
        $request = Request::create('/redefinir-senha', 'POST');
        $request->setLaravelSession(app('session')->driver());
        $request->session()->put('recuperacao_senha', $marker);
        $request->setUserResolver(fn () => $this->user->fresh());

        return $request;
    }

    public function test_emission_hash_expiration_serialization_and_replacement(): void
    {
        $plain = $this->issue();
        $this->assertMatchesRegularExpression('/^[ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789]{12}$/', $plain);
        $this->assertTrue(Hash::check($plain, $this->user->temp_password));
        $this->assertTrue(Hash::check('definitiva123', $this->user->password));
        $this->assertEqualsWithDelta(1800, now()->diffInSeconds($this->user->temp_password_expires_at), 2);
        $this->assertArrayNotHasKey('temp_password', $this->user->toArray());
        $this->assertArrayNotHasKey('temp_password_expires_at', $this->user->toArray());
        $this->assertNotNull($this->user->temp_password_expires_at);
        $this->assertFalse(in_array($plain, $this->user->getAttributes(), true));
        $old = $this->user->temp_password;
        $marker = $this->service->marker($this->user);
        $this->issue();
        $this->assertFalse($this->service->valid($this->user, $marker));
        $this->service->compensate($this->user->id, $old);
        $this->assertNotNull($this->user->fresh()->temp_password);
        $this->service->compensate($this->user->id, $this->user->temp_password);
        $this->assertNull($this->user->fresh()->temp_password);
    }

    public function test_generic_requests_and_limits(): void
    {
        foreach (['Pessoa', 'missing', 'Pessoa', 'Pessoa', 'Pessoa'] as $name) {
            $this->post('/esqueci-senha', ['username' => $name])->assertSessionHas('status', RecuperacaoSenhaController::MESSAGE);
        }
        Mail::assertSentCount(3);
    }

    public function test_ip_limit_is_independent_of_account_existence(): void
    {
        config(['recuperacao.ip_limit' => 2]);
        foreach (['missing1', 'missing2', 'Pessoa'] as $name) {
            $this->post('/esqueci-senha', ['username' => $name]);
        }
        Mail::assertNothingSent();
    }

    public function test_invalid_addresses_and_bootstrap_are_ineligible(): void
    {
        foreach ([null, '', 'invalid', 'admin@admin.com'] as $email) {
            $this->user->email = $email;
            $this->user->save();
            $this->service->issue('Pessoa');
            $this->assertNull($this->user->fresh()->temp_password);
        }
        $this->service->issue('pessoa'); // preserves current case-sensitive semantics
        Mail::assertNothingSent();
    }

    public function test_logging_and_composite_transports_are_rejected(): void
    {
        Mail::swap(new MailManager($this->app));
        foreach (['log', 'array', 'failover'] as $transport) {
            config(['mail.default' => $transport]);
            $this->service->issue('Pessoa');
            $this->assertNull($this->user->fresh()->temp_password);
        }
    }

    public function test_failed_send_compensates(): void
    {
        $service = new class extends RecuperacaoSenhaService
        {
            public function assertTransport(): void {}
        };
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('transport failed'));
        $service->issue('Pessoa');
        $this->assertNull($this->user->fresh()->temp_password);
        $this->assertSame(2, DB::table('logs')->count());
    }

    public function test_temporary_login_redirects_and_never_remembers(): void
    {
        $plain = $this->issue();
        $this->post('/login', ['username' => 'Pessoa', 'password' => $plain, 'remember' => true])
            ->assertRedirect(route('password.forced.edit'))->assertSessionHas('recuperacao_senha');
        $this->assertAuthenticatedAs($this->user);
        $this->assertNull($this->user->fresh()->remember_token);
        $this->get('/recovery-test-normal')->assertRedirect(route('password.forced.edit'));
        $this->getJson('/usuarios')->assertForbidden();
        $this->putJson('/usuarios/editar', [])->assertForbidden();
        $this->get('/redefinir-senha')->assertOk();
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_normal_login_cancels_recovery_and_invalidates_old_binding(): void
    {
        $this->issue();
        $marker = $this->service->marker($this->user);
        $this->post('/login', ['username' => 'Pessoa', 'password' => 'definitiva123'])->assertRedirect();
        $this->assertNull($this->user->fresh()->temp_password);
        $this->get('/recovery-test-normal')->assertOk();
        $this->withSession(['recuperacao_senha' => $marker])->get('/recovery-test-normal')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_bad_passwords_share_login_limiter(): void
    {
        $this->issue();
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['username' => 'Pessoa', 'password' => 'wrong'])->assertSessionHasErrors('username');
        }
        $this->post('/login', ['username' => 'Pessoa', 'password' => 'definitiva123'])->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_expired_password_rejected(): void
    {
        $plain = $this->issue();
        $this->user->temp_password_expires_at = now()->subSecond();
        $this->user->save();
        $this->post('/login', ['username' => 'Pessoa', 'password' => $plain])->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_invalid_expired_replaced_and_null_markers_logout(): void
    {
        foreach (['invalid', 'expired', 'replaced', 'null'] as $mode) {
            $this->issue();
            $marker = $this->service->marker($this->user);
            if ($mode === 'invalid') {
                $marker['user_id']++;
            }
            if ($mode === 'expired') {
                $this->user->temp_password_expires_at = now()->subSecond();
                $this->user->save();
            }
            if ($mode === 'replaced') {
                $this->issue();
            }
            if ($mode === 'null') {
                $marker = null;
            }
            $this->actingAs($this->user->fresh())->withSession(['recuperacao_senha' => $marker])->getJson('/recovery-test-normal')->assertUnauthorized();
            $this->assertGuest();
        }
    }

    public function test_normal_session_is_not_restricted_by_pending_recovery(): void
    {
        $this->issue();
        $this->actingAs($this->user)->get('/recovery-test-normal')->assertOk();
        $this->get('/redefinir-senha')->assertForbidden();
    }

    public function test_forced_validation_and_successful_atomic_completion(): void
    {
        $plain = $this->issue();
        $marker = $this->service->marker($this->user);
        $this->actingAs($this->user)->withSession(['recuperacao_senha' => $marker]);
        foreach ([['short', 'short'], ['longpassword', 'different'], [$plain, $plain]] as [$password, $confirmation]) {
            $this->post('/redefinir-senha', ['password' => $password, 'password_confirmation' => $confirmation])->assertSessionHasErrors('password');
            $this->assertNotNull($this->user->fresh()->temp_password);
        }
        config(['session.driver' => 'database']);
        DB::table('sessions')->insert(['id' => 'other', 'user_id' => $this->user->id]);
        $request = $this->requestFor($marker);
        $request->setUserResolver(fn () => $this->user);
        $this->service->finish($request, 'newpassword123');
        $this->assertNull($this->user->fresh()->temp_password);
        $this->assertNull($this->user->fresh()->temp_password_expires_at);
        $this->assertTrue(Hash::check('newpassword123', $this->user->fresh()->password));
        $this->assertFalse($request->session()->exists('recuperacao_senha'));
        $this->assertFalse(DB::table('sessions')->where('id', 'other')->exists());
        $this->assertFalse($this->service->valid($this->user->fresh(), $marker));
    }

    public function test_stale_completion_is_rejected_without_changing_new_generation(): void
    {
        $this->issue();
        $marker = $this->service->marker($this->user);
        $this->issue();
        $hash = $this->user->temp_password;
        try {
            $this->service->finish($this->requestFor($marker), 'newpassword123');
            $this->fail('Must reject stale generation');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->assertSame($hash, $this->user->fresh()->temp_password);
    }

    public function test_voluntary_change_requires_current_password_and_cancels_recovery(): void
    {
        $this->issue();
        $this->actingAs($this->user);
        $this->post('/perfil/senha', ['current_password' => 'wrong', 'password' => 'abcd', 'password_confirmation' => 'abcd'])->assertSessionHasErrors('current_password');
        $this->assertNotNull($this->user->fresh()->temp_password);
        $this->post('/perfil/senha', ['current_password' => 'definitiva123', 'password' => 'abcd', 'password_confirmation' => 'abcd'])->assertRedirect(route('profile.password.edit'));
        $this->assertNull($this->user->fresh()->temp_password);
        $this->assertTrue(Hash::check('abcd', $this->user->fresh()->password));
    }

    public function test_views_and_middleware_order(): void
    {
        $this->get('/login')->assertSee('Esqueci minha senha');
        $this->get('/esqueci-senha')->assertOk();
        $route = app('router')->getRoutes()->getByName('password.forced.update');
        $stack = app('router')->gatherRouteMiddleware($route);
        $restriction = array_search(RestringirSessaoTemporaria::class, $stack);
        $this->assertGreaterThan(array_search(StartSession::class, $stack), $restriction);
        $this->assertLessThan(array_search(Authenticate::class, $stack), $restriction);
        $this->assertContains(ValidateCsrfToken::class, $stack);
        $this->assertGreaterThan(array_search(ValidateCsrfToken::class, $stack), $restriction);
        $this->assertNotContains(AuthenticateSession::class, $stack);
    }

    public function test_rollback_keeps_marker_and_temporary_credential(): void
    {
        $this->issue();
        $marker = $this->service->marker($this->user);
        $request = $this->requestFor($marker);
        config(['session.driver' => 'database']);
        $service = new class extends RecuperacaoSenhaService
        {
            public function audit(User $user, string $action): void
            {
                throw new \RuntimeException('audit unavailable');
            }
        };
        try {
            $service->finish($request, 'newpassword123');
            $this->fail('Expected rollback');
        } catch (\RuntimeException $e) {
            $this->assertSame('audit unavailable', $e->getMessage());
        }
        $this->assertTrue($this->service->valid($this->user->fresh(), $marker));
        $this->assertTrue($request->session()->exists('recuperacao_senha'));
        $this->assertTrue(Hash::check('definitiva123', $this->user->fresh()->password));
    }

    public function test_completed_password_login_and_old_session_revocation(): void
    {
        $plain = $this->issue();
        $marker = $this->service->marker($this->user);
        config(['session.driver' => 'database']);
        $request = $this->requestFor($marker);
        $request->setUserResolver(fn () => $this->user);
        $this->service->finish($request, 'newpassword123');
        config(['session.driver' => 'array']);
        $this->post('/login', ['username' => 'Pessoa', 'password' => $plain])->assertSessionHasErrors('username');
        $this->post('/login', ['username' => 'Pessoa', 'password' => 'newpassword123'])->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_migration_down_preserves_user_and_audit(): void
    {
        $this->issue();
        (require base_path('database/migrations/2026_11_08_000001_add_temporary_password_to_users.php'))->down();
        $this->assertSame(1, DB::table('users')->count());
        $this->assertSame(1, DB::table('logs')->count());
    }

    public function test_real_postgres_lock_serializes_recovery_operations(): void
    {
        // Shared disposable schema: separate PHP processes, not pg_temp simulations.
        $schema = 'recovery_'.bin2hex(random_bytes(8));
        DB::statement('CREATE SCHEMA '.$schema);
        try {
            DB::statement('SET search_path TO '.$schema.', pg_temp');
            DB::statement('CREATE TABLE users (id bigserial primary key, username varchar(255), password varchar(255), email varchar(255), remember_token varchar(100))');
            DB::statement('CREATE TABLE acoes (id bigserial primary key, descricao varchar(255))');
            DB::statement('CREATE TABLE logs (id bigserial primary key, id_user bigint REFERENCES users(id), id_acao bigint REFERENCES acoes(id), tipo varchar(30), descricao text, data_hora timestamp)');
            DB::statement('CREATE TABLE sessions (id varchar(255) primary key, user_id bigint, payload text)');
            (require base_path('database/migrations/2026_11_08_000001_add_temporary_password_to_users.php'))->up();
            $this->user = User::create(['username' => 'Pessoa', 'password' => 'definitiva123', 'email' => 'pessoa@example.org']);
            DB::statement('ALTER TABLE users ADD name varchar(255), ADD cpf varchar(20), ADD phone varchar(20)');
            DB::statement('CREATE TABLE classes (id bigserial primary key, nome varchar(255))');
            DB::statement('CREATE TABLE users_classes (id_user bigint, id_classe bigint)');
            DB::table('acoes')->insert(['descricao' => 'Editar Usuário']);
            foreach (['issue', 'compensate', 'cancel_finish', 'replace_finish', 'finish_finish', 'login_finish', 'login_cancel', 'login_issue', 'admin_recovery'] as $scenario) {
                $this->user->password = 'definitiva123';
                $this->user->save();
                $this->issue();
                $marker = $this->service->marker($this->user);
                DB::beginTransaction();
                User::whereKey($this->user->id)->lockForUpdate()->first();
                $pipes = [];
                $process = proc_open([PHP_BINARY, base_path('tests/Support/recovery-worker.php')],
                    [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes, base_path());
                $this->assertIsResource($process);
                fwrite($pipes[0], json_encode(['schema' => $schema, 'id' => $this->user->id,
                    'operation' => str_contains($scenario, 'finish') && $scenario !== 'login_finish' ? 'finish' : $scenario,
                    'hash' => $this->user->temp_password, 'marker' => $marker, 'key' => config('app.key')]));
                fclose($pipes[0]);
                try {
                    $waiting = false;
                    $deadline = microtime(true) + 10;
                    do {
                        DB::select('SELECT pg_stat_clear_snapshot()');
                        $waiting = DB::selectOne("SELECT count(*) AS n FROM pg_stat_activity WHERE application_name = ? AND wait_event_type = 'Lock'", [$schema])->n > 0;
                        if (! $waiting) {
                            usleep(20000);
                        }
                    } while (! $waiting && microtime(true) < $deadline);
                    $this->assertTrue($waiting, 'Worker must actually wait on PostgreSQL row lock');
                    $query = DB::selectOne("SELECT query FROM pg_stat_activity WHERE application_name = ? AND wait_event_type = 'Lock' LIMIT 1", [$schema])->query;
                    $this->assertStringContainsString('for update', strtolower($query), 'Must block on the explicit SELECT lock, not a later UPDATE');
                    if ($scenario === 'login_cancel') {
                        $user = $this->user->fresh();
                        $user->password = 'changedbeforelogin';
                        $this->service->clear($user);
                        $user->save();
                    } elseif ($scenario === 'cancel_finish') {
                        $user = $this->user->fresh();
                        $this->service->clear($user);
                        $user->save();
                    } elseif (in_array($scenario, ['finish_finish', 'login_finish'])) {
                        config(['session.driver' => 'database']);
                        $this->service->finish($this->requestFor($marker), 'newpassword123');
                    } else {
                        $this->issue();
                    }
                    $expectedHash = $this->user->fresh()->temp_password;
                    DB::commit();
                    $output = stream_get_contents($pipes[1]);
                    $errors = stream_get_contents($pipes[2]);
                    $this->assertSame('', $errors);
                    $this->assertSame(str_contains($scenario, 'finish') || $scenario === 'login_cancel' ? 'rejected' : 'ok', $output);
                    if ($scenario === 'issue') {
                        $this->assertNotEquals($expectedHash, $this->user->fresh()->temp_password);
                    } elseif (in_array($scenario, ['login_issue', 'admin_recovery'])) {
                        $this->assertNull($this->user->fresh()->temp_password);
                    } else {
                        $this->assertSame($expectedHash, $this->user->fresh()->temp_password);
                    }
                } finally {
                    while (DB::transactionLevel()) {
                        DB::rollBack();
                    }
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    proc_terminate($process);
                    proc_close($process);
                }
            }
        } finally {
            DB::statement('SET search_path TO pg_temp');
            // Identifier generated above, never supplied by environment/user input.
            DB::statement('DROP SCHEMA '.$schema.' CASCADE');
        }
    }

    public function test_administrative_password_update_cancels_pending_recovery(): void
    {
        DB::statement('ALTER TABLE users ADD name varchar(255), ADD cpf varchar(20), ADD phone varchar(20)');
        DB::statement('CREATE TEMP TABLE classes (id bigserial primary key, nome varchar(255))');
        DB::statement('CREATE TEMP TABLE users_classes (id_user bigint, id_classe bigint)');
        DB::table('acoes')->insert(['descricao' => 'Editar Usuário']);
        $this->issue();
        $this->actingAs($this->user);
        $request = Request::create('/usuarios/editar', 'PUT', ['id_edit' => $this->user->id,
            'username' => 'Pessoa', 'name' => 'Pessoa', 'password' => 'adminchanged123',
            'cpf' => '123', 'phone' => '123', 'email' => 'pessoa@example.org', 'classes' => []]);
        $request->setLaravelSession(app('session')->driver());
        $response = app(UsersController::class)->update($request);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertNull($this->user->fresh()->temp_password);
        $this->assertTrue(Hash::check('adminchanged123', $this->user->fresh()->password));
        $this->get('/recovery-test-normal')->assertOk();
        $this->assertAuthenticated();
        $this->assertStringNotContainsString('temp_password', DB::table('logs')->pluck('descricao')->implode(' '));
    }

    public function test_csrf_still_protects_allowed_forced_update(): void
    {
        $this->issue();
        $this->actingAs($this->user)->withSession(['recuperacao_senha' => $this->service->marker($this->user)]);
        $this->app['env'] = 'production'; // enable real CSRF middleware, no outbound mail here
        $this->post('/redefinir-senha', ['password' => 'newpassword123', 'password_confirmation' => 'newpassword123'])->assertStatus(419);
        $this->assertNotNull($this->user->fresh()->temp_password);
    }

    public function test_full_forced_http_flow_regenerates_session_and_csrf(): void
    {
        config(['session.driver' => 'database']);
        $plain = $this->issue();
        $this->post('/login', ['username' => 'Pessoa', 'password' => $plain])->assertRedirect(route('password.forced.edit'));
        $oldId = session()->getId();
        $oldCsrf = session()->token();
        $this->post('/redefinir-senha', ['password' => 'newpassword123', 'password_confirmation' => 'newpassword123'])
            ->assertRedirect(route('dashboard'))->assertSessionMissing('recuperacao_senha');
        $this->assertNotSame($oldId, session()->getId());
        $this->assertNotSame($oldCsrf, session()->token());
        $this->get('/recovery-test-normal')->assertOk();
    }

    public function test_normal_session_ignores_stale_forced_post(): void
    {
        $this->issue();
        $before = $this->user->fresh()->getAttributes();
        $this->actingAs($this->user)->post('/redefinir-senha', ['password' => 'newpassword123', 'password_confirmation' => 'newpassword123'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertSame($before, $this->user->fresh()->getAttributes());
    }

    public function test_hash_work_is_equal_and_inside_guard_timebox(): void
    {
        $this->issue();
        $real = Hash::getFacadeRoot();
        foreach (['missing', 'absent', 'expired', 'active'] as $mode) {
            $user = $this->user->fresh();
            if ($mode === 'absent') {
                $this->service->clear($user);
                $user->save();
            } else {
                $this->issue();
            }
            if ($mode === 'expired') {
                $user = $this->user->fresh();
                $user->temp_password_expires_at = now()->subMinute();
                $user->save();
            }
            $count = 0;
            $spy = \Mockery::mock($real);
            Hash::swap($spy);
            $guard = Auth::guard('web');
            $provider = $guard->getProvider();
            $guard->setProvider(new EloquentUserProvider($spy, User::class));
            $spy->shouldReceive('check')->twice()->andReturnUsing(function ($plain, $hash) use ($real, &$count) {
                $count++;
                if ($count === 1) {
                    $frames = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                    $this->assertTrue(collect($frames)->contains(fn ($f) => ($f['class'] ?? '') === Timebox::class && $f['function'] === 'call'));
                }

                return $real->check($plain, $hash);
            });
            $request = Request::create('/login', 'POST', ['username' => $mode === 'missing' ? 'unknown' : 'Pessoa', 'password' => 'wrong']);
            $request->setLaravelSession(app('session')->driver());
            $this->assertFalse($this->service->authenticate($request));
            $this->assertSame(2, $count);
            Hash::swap($real);
            $guard->setProvider($provider);
        }
    }

    public function test_request_timebox_receives_positive_floor(): void
    {
        config(['recuperacao.floor_ms' => 25]);
        $box = new class extends Timebox
        {
            public int $requested = 0;

            public function call(callable $callback, int $microseconds)
            {
                $this->requested = $microseconds;

                return parent::call($callback, $microseconds);
            }
        };
        $this->app->instance(Timebox::class, $box);
        $start = hrtime(true);
        $this->post('/esqueci-senha', ['username' => 'missing'])->assertSessionHas('status');
        $this->assertSame(25000, $box->requested);
        $this->assertGreaterThanOrEqual(25, (hrtime(true) - $start) / 1e6);
    }

    public function test_contended_limit_does_not_block_independent_requests(): void
    {
        $limiter = app(LimitesRecuperacao::class);
        $lock = Cache::lock('recovery:login:'.hash('sha256', 'Pessoa').':lock', 10);
        $this->assertTrue($lock->get());
        try {
            $this->assertFalse($limiter->admit('Pessoa', '127.0.0.1'));
            $this->assertTrue($limiter->admit('Outra', '127.0.0.2'));
        } finally {
            $lock->release();
        }
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($limiter->admit('Pessoa', '127.0.0.1'));
        }
        $this->assertFalse($limiter->admit('Pessoa', '127.0.0.3'));
    }

    public function test_csrf_precedes_invalid_marker_logout(): void
    {
        $this->actingAs($this->user)->withSession(['recuperacao_senha' => ['invalid']]);
        $this->app['env'] = 'production';
        $this->post('/redefinir-senha')->assertStatus(419);
        $this->assertAuthenticated();
        $this->get('/redefinir-senha')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_other_user_edit_preserves_normal_sessions_but_cancels_temporary(): void
    {
        DB::statement('ALTER TABLE users ADD name varchar(255), ADD cpf varchar(20), ADD phone varchar(20)');
        DB::statement('CREATE TEMP TABLE classes (id bigserial primary key, nome varchar(255))');
        DB::statement('CREATE TEMP TABLE users_classes (id_user bigint, id_classe bigint)');
        DB::table('acoes')->insert(['descricao' => 'Editar Usuário']);
        $this->issue();
        $marker = $this->service->marker($this->user);
        $admin = User::create(['username' => 'operador', 'password' => 'adminpassword123', 'email' => 'operador@example.org']);
        $this->actingAs($admin);
        DB::table('sessions')->insert(['id' => 'target-normal', 'user_id' => $this->user->id]);
        $request = Request::create('/usuarios/editar', 'PUT', ['id_edit' => $this->user->id,
            'username' => 'Pessoa', 'name' => 'Pessoa', 'password' => 'adminchanged123',
            'cpf' => '123', 'phone' => '123', 'email' => 'pessoa@example.org', 'classes' => []]);
        $request->setLaravelSession(app('session')->driver());
        app(UsersController::class)->update($request);
        $this->get('/recovery-test-normal')->assertOk();
        $this->assertAuthenticatedAs($admin);
        $this->assertTrue(DB::table('sessions')->where('id', 'target-normal')->exists());
        $this->actingAs($this->user->fresh())->get('/recovery-test-normal')->assertOk();
        $this->withSession(['recuperacao_senha' => $marker])->getJson('/recovery-test-normal')->assertUnauthorized();
        $this->assertGuest();
    }

    public function test_concurrent_limits_on_shared_database_cache(): void
    {
        $schema = 'recovery_'.bin2hex(random_bytes(8));
        DB::statement('CREATE SCHEMA '.$schema);
        try {
            DB::statement('CREATE TABLE '.$schema.'.cache (key varchar(255) primary key, value text, expiration integer)');
            DB::statement('CREATE TABLE '.$schema.'.cache_locks (key varchar(255) primary key, owner varchar(255), expiration integer)');
            foreach (['login', 'ip'] as $mode) {
                DB::statement('TRUNCATE '.$schema.'.cache, '.$schema.'.cache_locks');
                DB::select('SELECT pg_advisory_lock(55438)');
                $workers = [];
                try {
                    for ($i = 0; $i < 8; $i++) {
                        $pipes = [];
                        $process = proc_open([PHP_BINARY, base_path('tests/Support/recovery-worker.php')], [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes, base_path());
                        $workers[] = [$process, $pipes];
                        fwrite($pipes[0], json_encode(['schema' => $schema, 'key' => config('app.key'), 'operation' => 'limit',
                            'login_limit' => $mode === 'login' ? 3 : 100, 'ip_limit' => $mode === 'ip' ? 2 : 100,
                            'login' => $mode === 'login' ? 'same' : 'person'.$i]));
                        fclose($pipes[0]);
                    }
                    $deadline = microtime(true) + 15;
                    do {
                        DB::select('SELECT pg_stat_clear_snapshot()');
                        $waiting = (int) DB::selectOne("SELECT count(*) AS n FROM pg_stat_activity WHERE application_name = ? AND wait_event_type = 'Lock'", [$schema])->n;
                        if ($waiting < 8) {
                            usleep(20000);
                        }
                    } while ($waiting < 8 && microtime(true) < $deadline);
                    $this->assertSame(8, $waiting, 'All distinct processes must reach the start barrier');
                    DB::select('SELECT pg_advisory_unlock(55438)');
                    $accepted = 0;
                    foreach ($workers as [$process, $pipes]) {
                        $output = stream_get_contents($pipes[1]);
                        $this->assertSame('', stream_get_contents($pipes[2]));
                        $this->assertContains($output, ['yes', 'no']);
                        $accepted += $output === 'yes' ? 1 : 0;
                    }
                    $this->assertGreaterThan(0, $accepted);
                    $this->assertLessThanOrEqual($mode === 'login' ? 3 : 2, $accepted);
                } finally {
                    DB::select('SELECT pg_advisory_unlock_all()');
                    foreach ($workers as [$process, $pipes]) {
                        fclose($pipes[1]);
                        fclose($pipes[2]);
                        proc_terminate($process);
                        proc_close($process);
                    }
                }
            }
        } finally {
            DB::statement('DROP SCHEMA '.$schema.' CASCADE');
        }
    }

    public function test_complementary_login_timing_at_cost_twelve(): void
    {
        config(['hashing.bcrypt.rounds' => 12]);
        Hash::getFacadeRoot()->forgetDrivers();
        $this->user->password = Hash::make('definitiva123');
        $this->user->save();
        $this->issue();
        $hash = $this->user->temp_password;
        $samples = [];
        // Interleave paths to reduce drift; timings complement structural assertions.
        for ($i = 0; $i < 6; $i++) {
            foreach (['missing', 'absent', 'expired', 'active'] as $mode) {
                $this->user->temp_password = $mode === 'absent' ? null : $hash;
                $this->user->temp_password_expires_at = $mode === 'expired' ? now()->subMinute() : now()->addMinutes(30);
                $this->user->save();
                $request = Request::create('/login', 'POST', ['username' => $mode === 'missing' ? 'unknown' : 'Pessoa', 'password' => 'wrong']);
                $request->setLaravelSession(app('session')->driver());
                $start = hrtime(true);
                $this->assertFalse($this->service->authenticate($request));
                $samples[$mode][] = (hrtime(true) - $start) / 1e6;
            }
        }
        $medians = [];
        foreach ($samples as $mode => $values) {
            sort($values);
            $medians[$mode] = round(($values[2] + $values[3]) / 2, 1);
        }
        fwrite(STDOUT, "\nLogin medians ms (bcrypt12, n=6): ".json_encode($medians)."\n");
    }

    public function test_database_revocation_survives_late_write_of_existing_session(): void
    {
        $this->issue();
        $marker = $this->service->marker($this->user);
        DB::table('sessions')->insert(['id' => 'old-session', 'user_id' => $this->user->id,
            'payload' => base64_encode('old-payload'), 'last_activity' => time()]);
        $handler = new DatabaseSessionHandler(DB::connection(), 'sessions', 120);
        $this->assertSame('old-payload', $handler->read('old-session'));
        config(['session.driver' => 'database']);
        $this->service->finish($this->requestFor($marker), 'newpassword123');
        $handler->write('old-session', 'late-payload');
        $this->assertFalse(DB::table('sessions')->where('id', 'old-session')->exists());
        $freshHandler = new DatabaseSessionHandler(DB::connection(), 'sessions', 120);
        $this->assertSame('', $freshHandler->read('old-session'));
    }

    private function legacyRecaller(): array
    {
        $this->user->remember_token = Str::random(60);
        $this->user->save();

        return [Auth::guard('web')->getRecallerName(),
            $this->user->id.'|'.$this->user->remember_token.'|'.$this->user->password];
    }

    public function test_manual_remember_login_never_issues_recaller(): void
    {
        $name = Auth::guard('web')->getRecallerName();
        $this->post('/login', ['username' => 'Pessoa', 'password' => 'definitiva123', 'remember' => '1'])
            ->assertRedirect(route('dashboard'))->assertCookieMissing($name);
        $this->assertAuthenticatedAs($this->user);
        $this->assertNull($this->user->fresh()->remember_token);
        $this->assertFalse(Auth::guard('web')->viaRemember());
    }

    public function test_valid_legacy_recaller_is_ignored_without_session_on_all_route_types(): void
    {
        [$name, $value] = $this->legacyRecaller();
        // Positive control: this is a genuine recaller accepted by the stock guard.
        $request = Request::create('/', 'GET', [], [$name => $value]);
        $session = new Store('control', new ArraySessionHandler(120));
        $guard = new SessionGuard('web', Auth::guard('web')->getProvider(), $session, $request);
        $this->assertSame($this->user->id, $guard->user()->id);
        $this->assertTrue($guard->viaRemember());

        $this->withCookie($name, $value)->get('/recovery-test-normal')->assertRedirect(route('login'))->assertCookieExpired($name);
        $this->assertGuest();
        $this->assertFalse(Auth::guard('web')->viaRemember());
        $this->get('/usuarios')->assertRedirect(route('login'));
        $this->get('/login')->assertOk();
        $this->get('/esqueci-senha')->assertOk();
        $this->assertGuest();
        $this->assertFalse(session()->has(Auth::guard('web')->getName()));
    }

    public function test_legacy_cookie_preserves_real_conventional_session(): void
    {
        config(['session.driver' => 'database']);
        [$name, $value] = $this->legacyRecaller();
        $this->post('/login', ['username' => 'Pessoa', 'password' => 'definitiva123'])->assertRedirect();
        $sessionId = session()->getId();
        Auth::forgetGuards(); // next request must load the persisted session, not cached user
        $this->withCookies([config('session.cookie') => $sessionId, $name => $value])
            ->get('/recovery-test-normal')->assertOk()->assertCookieExpired($name);
        $this->assertAuthenticatedAs($this->user);
        $this->assertFalse(Auth::guard('web')->viaRemember());
        $this->assertTrue(DB::table('sessions')->where('id', $sessionId)->exists());
    }

    public function test_n1_adversarial_request_cannot_establish_recalled_session_across_finish(): void
    {
        config(['session.driver' => 'database']);
        $this->issue();
        $marker = $this->service->marker($this->user);
        [$name, $value] = $this->legacyRecaller();
        $attempted = false;
        $finished = false;
        // Actual HTTP pipeline: attempt recall, finish recovery in an independent
        // session while the first request is in flight, then let StartSession save.
        Route::middleware('web')->get('/recovery-test-n1', function (Request $incoming) use ($marker, $name, &$attempted, &$finished) {
            $this->assertFalse($incoming->cookies->has($name));
            $attempted = true;
            $this->assertNull(Auth::guard('web')->user());
            $this->assertFalse(Auth::guard('web')->viaRemember());
            $this->assertFalse($incoming->session()->has(Auth::guard('web')->getName()));
            $other = Request::create('/redefinir-senha', 'POST');
            $other->setLaravelSession(new Store('recovery', new ArraySessionHandler(120)));
            $other->session()->put('recuperacao_senha', $marker);
            $user = $this->user->fresh();
            $other->setUserResolver(fn () => $user);
            $this->service->finish($other, 'newpassword123');
            $finished = true;

            return response('done');
        });
        $this->withCookie($name, $value)->get('/recovery-test-n1')->assertOk()->assertCookieExpired($name);
        $this->assertTrue($attempted && $finished);
        $sessionId = session()->getId();
        $this->assertFalse(DB::table('sessions')->where('id', $sessionId)->whereNotNull('user_id')->exists());
        Auth::forgetGuards();
        $this->withCookies([config('session.cookie') => $sessionId, $name => $value])
            ->get('/recovery-test-normal')->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertTrue(Hash::check('newpassword123', $this->user->fresh()->password));
    }

    public function test_recaller_neutralization_precedes_global_and_route_user_resolution(): void
    {
        $kernel = app(Kernel::class);
        $this->assertSame(DesativarRememberMe::class, (new \ReflectionProperty($kernel, 'middleware'))->getValue($kernel)[0]);
        [$name, $value] = $this->legacyRecaller();
        $seen = false;
        $kernel->pushMiddleware(function (Request $request, $next) use ($name, &$seen) {
            $seen = true;
            $this->assertFalse($request->cookies->has($name));
            $this->assertNull(Auth::guard('web')->user());

            return $next($request);
        });
        $this->withCookie($name, $value)->get('/recovery-test-normal')->assertRedirect(route('login'));
        $this->assertTrue($seen);
    }
}
