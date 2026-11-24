<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LimitesRecuperacao;
use App\Services\RecuperacaoSenhaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Timebox;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RecuperacaoSenhaController extends Controller
{
    public const MESSAGE = 'Se o login estiver cadastrado e possuir um e-mail válido, você receberá as instruções de recuperação.';

    public function create()
    {
        return view('auth.esqueci-senha');
    }

    public function store(Request $request, RecuperacaoSenhaService $service)
    {
        $request->validate(['username' => ['required', 'string', 'max:255']]);
        app(Timebox::class)->call(function () use ($request, $service) {
            $allowed = app(LimitesRecuperacao::class)->admit(
                $request->input('username'), $request->ip());
            if ($allowed) {
                $service->issue($request->input('username'));
            }
        }, max(0, config('recuperacao.floor_ms')) * 1000);

        return back()->with('status', self::MESSAGE);
    }

    public function edit(Request $request, RecuperacaoSenhaService $service)
    {
        abort_unless($service->valid($request->user(), $request->session()->get('recuperacao_senha')), 403);

        return view('auth.senha-obrigatoria');
    }

    public function update(Request $request, RecuperacaoSenhaService $service)
    {
        if (! $request->session()->exists('recuperacao_senha')) {
            return redirect()->route('dashboard');
        }
        $data = $request->validate(['password' => ['required', 'string', 'confirmed', 'min:8']]);
        try {
            $service->finish($request, $data['password']);
        } catch (ValidationException $e) {
            throw $e;
        } catch (HttpException $e) {
            if ($e->getStatusCode() !== 403) {
                throw $e;
            }
            Auth::guard('web')->logoutCurrentDevice();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $request->expectsJson()
                ? response()->json(['message' => 'Sessão expirada. Entre novamente.'], 401)
                : redirect()->route('login')->with('status', 'Sessão expirada. Entre novamente.');
        } catch (\Throwable $e) {
            logger()->error('Falha transacional na redefinição de senha.');

            return back()->withErrors(['password' => 'Não foi possível concluir. Tente novamente.']);
        }

        return redirect()->route('dashboard')->with('status', 'Senha redefinida com sucesso.');
    }
}
