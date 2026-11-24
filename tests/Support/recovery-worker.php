<?php

use App\Http\Controllers\UsersController;
use App\Models\User;
use App\Services\LimitesRecuperacao;
use App\Services\RecuperacaoSenhaService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\HttpException;

require __DIR__.'/../../vendor/autoload.php';
putenv('APP_ENV=testing');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=:memory:');
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$data = json_decode(stream_get_contents(STDIN), true);
if (! preg_match('/^recovery_[a-f0-9]{16}$/', $data['schema'] ?? '')) {
    exit(2);
}
config(['database.default' => 'recovery_worker', 'database.connections' => ['recovery_worker' => [
    'driver' => 'pgsql', 'host' => '127.0.0.1', 'port' => 55438, 'database' => 'postgres',
    'username' => 'siapra_test', 'password' => '', 'charset' => 'utf8', 'prefix' => '', 'search_path' => $data['schema'], 'sslmode' => 'disable',
]], 'session.driver' => 'database', 'session.connection' => null, 'app.key' => $data['key']]);
$identity = DB::selectOne("SELECT current_setting('data_directory') AS dir, inet_server_port() AS port, current_user AS usr");
if (strtolower(str_replace('\\', '/', $identity->dir)) !== strtolower(str_replace('\\', '/', base_path('storage/framework/testing/recovery-pg')))
    || $identity->port !== 55438 || $identity->usr !== 'siapra_test') {
    exit(3);
}
DB::select("SELECT set_config('application_name', ?, false)", [$data['schema']]);
Mail::fake();
$service = app(RecuperacaoSenhaService::class);
try {
    if ($data['operation'] === 'limit') {
        config(['cache.default' => 'database', 'cache.prefix' => 'recovery-test',
            'cache.stores.database.connection' => 'recovery_worker',
            'cache.stores.database.lock_connection' => 'recovery_worker',
            'recuperacao.login_limit' => $data['login_limit'], 'recuperacao.ip_limit' => $data['ip_limit']]);
        DB::select('SELECT pg_advisory_lock_shared(55438)');
        DB::select('SELECT pg_advisory_unlock_shared(55438)');
        echo app(LimitesRecuperacao::class)->admit($data['login'], '127.0.0.1') ? 'yes' : 'no';
        exit(0);
    } elseif ($data['operation'] === 'issue') {
        $service->issue('Pessoa');
    } elseif ($data['operation'] === 'compensate') {
        $service->compensate($data['id'], $data['hash']);
    } elseif (in_array($data['operation'], ['login_cancel', 'login_issue', 'login_finish'])) {
        $request = Request::create('/login', 'POST', ['username' => 'Pessoa', 'password' => 'definitiva123']);
        $request->setLaravelSession(new Store('test', new ArraySessionHandler(120)));
        if (! $service->authenticate($request)) {
            echo 'rejected';
            exit(0);
        }
    } elseif ($data['operation'] === 'admin_recovery') {
        Auth::setUser(User::findOrFail($data['id']));
        $request = Request::create('/usuarios/editar', 'PUT', ['id_edit' => $data['id'],
            'username' => 'Pessoa', 'name' => 'Pessoa', 'password' => 'adminchanged123',
            'cpf' => '123', 'phone' => '123', 'email' => 'pessoa@example.org', 'classes' => []]);
        $request->setLaravelSession(new Store('test', new ArraySessionHandler(120)));
        app(UsersController::class)->update($request);
    } else {
        $request = Request::create('/redefinir-senha', 'POST');
        $session = new Store('test', new ArraySessionHandler(120));
        $request->setLaravelSession($session);
        $session->put('recuperacao_senha', $data['marker']);
        $user = User::findOrFail($data['id']);
        $request->setUserResolver(fn () => $user);
        $service->finish($request, 'newpassword123');
    }
    echo 'ok';
} catch (HttpException $e) {
    echo 'rejected';
} catch (Throwable $e) {
    echo 'failed';
    exit(1);
}
