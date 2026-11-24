<?php

namespace App\Services;

use App\Mail\SenhaTemporaria;
use App\Models\Log as AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Testing\Fakes\MailFake;
use Illuminate\Validation\ValidationException;

class RecuperacaoSenhaService
{
    public function generate(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $password = '';
        for ($i = 0; $i < 12; $i++) {
            $password .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $password;
    }

    public function marker(User $user): array
    {
        $key = config('app.key');
        if (! $key) {
            throw new \RuntimeException('Chave de aplicação ausente.');
        }

        return ['user_id' => $user->id, 'binding' => hash_hmac('sha256', 'siapra-recovery:'.$user->temp_password, $key)];
    }

    public function valid(?User $user, mixed $marker): bool
    {
        return $user && $user->temp_password && $user->temp_password_expires_at?->isFuture()
            && is_array($marker) && ($marker['user_id'] ?? null) === $user->id
            && is_string($marker['binding'] ?? null)
            && hash_equals($this->marker($user)['binding'], $marker['binding']);
    }

    public function clear(User $user): void
    {
        $user->temp_password = null;
        $user->temp_password_expires_at = null;
    }

    public function audit(User $user, string $action): void
    {
        AuditLog::create(['id_user' => $user->id,
            'id_acao' => DB::table('acoes')->where('descricao', $action)->value('id'),
            'tipo' => 'Info', 'descricao' => $action, 'data_hora' => now()]);
    }

    public function assertTransport(): void
    {
        if (app()->runningUnitTests() && Mail::getFacadeRoot() instanceof MailFake) {
            return;
        }
        // Reject logging, composite fallbacks and unknown custom transports.
        $transport = config('mail.mailers.'.config('mail.default').'.transport');
        if (! in_array($transport, ['smtp', 'ses', 'ses-v2', 'postmark', 'mailgun', 'sendmail'], true)) {
            throw new \RuntimeException('Transporte sensível indisponível.');
        }
    }

    public function issue(string $username): void
    {
        $issued = null;
        try {
            $this->assertTransport();
            $plain = $this->generate();
            $hash = Hash::make($plain);
            $issued = DB::transaction(function () use ($username, $hash) {
                $user = User::where('username', $username)->lockForUpdate()->first();
                if (! $user || ! filter_var($user->email, FILTER_VALIDATE_EMAIL)
                    || in_array(strtolower($user->email), config('recuperacao.blocked_emails'), true)) {
                    return null;
                }
                $user->temp_password = $hash;
                $user->temp_password_expires_at = now()->addMinutes(max(1, config('recuperacao.minutes')));
                $user->save();
                $this->audit($user, 'Recuperação emitida');

                return $user;
            });
            if ($issued) {
                Mail::to($issued->email)->send(new SenhaTemporaria($plain, $issued->temp_password_expires_at));
            }
        } catch (\Throwable $e) {
            if ($issued) {
                try {
                    $this->compensate($issued->id, $issued->temp_password);
                } catch (\Throwable $ignored) {
                    logger()->error('Falha na compensação de recuperação.');
                }
            }
            // Never report exception messages, SQL bindings or mailable content.
            logger()->warning('Recuperação indisponível; verificar banco e transporte de e-mail.');
        }
    }

    public function compensate(int $id, string $hash): void
    {
        DB::transaction(function () use ($id, $hash) {
            $user = User::whereKey($id)->lockForUpdate()->first();
            if ($user && hash_equals($hash, $user->temp_password ?? '')) {
                $this->clear($user);
                $user->save();
                $this->audit($user, 'Recuperação: falha de envio');
            }
        });
    }

    public function authenticate(Request $request): bool
    {
        return DB::transaction(function () use ($request) {
            // Serialize verification and cancellation against emissions/password changes.
            $user = User::where('username', $request->input('username'))->lockForUpdate()->first();
            if (Auth::guard('web')->attempt($request->only('username', 'password'), false)) {
                if ($user->temp_password) {
                    $this->clear($user);
                    $user->save();
                    $this->audit($user, 'Recuperação cancelada');
                }
                $request->session()->forget('recuperacao_senha');

                return true;
            }
            // Always perform the second hash check, even if absent or expired.
            $temporaryMatches = Hash::check($request->input('password'),
                $user?->temp_password ?? app(SenhaFicticia::class)->hash());
            if (! $user || ! $user->temp_password || ! $user->temp_password_expires_at?->isFuture()
                || ! $temporaryMatches) {
                return false;
            }
            $this->audit($user, 'Autenticação temporária');
            Auth::guard('web')->login($user, false);
            $request->session()->put('recuperacao_senha', $this->marker($user));

            return true;
        });
    }

    public function finish(Request $request, string $password): void
    {
        DB::transaction(function () use ($request, $password) {
            $user = User::whereKey($request->user()->id)->lockForUpdate()->first();
            abort_unless($this->valid($user, $request->session()->get('recuperacao_senha')), 403);
            if (Hash::check($password, $user->temp_password)) {
                throw ValidationException::withMessages(['password' => 'Escolha uma senha diferente da temporária.']);
            }
            if (config('session.driver') !== 'database'
                || (config('session.connection') && config('session.connection') !== config('database.default'))) {
                throw ValidationException::withMessages(['password' => 'Redefinição indisponível. Contate o suporte.']);
            }
            $user->password = Hash::make($password);
            $user->remember_token = Str::random(60);
            $this->clear($user);
            $user->save();
            DB::table(config('session.table'))->where('user_id', $user->id)
                ->where('id', '!=', $request->session()->getId())->delete();
            $this->audit($user, 'Recuperação concluída');
            $request->user()->setRawAttributes($user->getAttributes(), true);
        });
        $request->session()->forget('recuperacao_senha');
        $request->session()->forget('url.intended');
        $request->session()->regenerate();
    }

    public function voluntary(Request $request): void
    {
        DB::transaction(function () use ($request) {
            $user = User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            if (! Hash::check($request->input('current_password'), $user->password)) {
                throw ValidationException::withMessages(['current_password' => 'Senha atual incorreta.']);
            }
            $user->password = $request->input('password');
            $this->clear($user);
            $user->save();
            $request->user()->setRawAttributes($user->getAttributes(), true);
        });
    }
}
