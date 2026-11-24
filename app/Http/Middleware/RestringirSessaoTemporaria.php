<?php

namespace App\Http\Middleware;

use App\Services\RecuperacaoSenhaService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestringirSessaoTemporaria
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->session()->exists('recuperacao_senha')) {
            return $next($request);
        }
        if (! app(RecuperacaoSenhaService::class)->valid($request->user()?->fresh(), $request->session()->get('recuperacao_senha'))) {
            Auth::guard('web')->logoutCurrentDevice();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $request->expectsJson()
                ? response()->json(['message' => 'Sessão expirada. Entre novamente.'], 401)
                : redirect()->route('login')->with('status', 'Sessão expirada. Entre novamente.');
        }
        $allowed = ($request->isMethod('GET') && $request->routeIs('password.forced.edit'))
            || ($request->isMethod('POST') && $request->routeIs('password.forced.update', 'logout'));
        if (! $allowed) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Redefina sua senha para continuar.'], 403)
                : redirect()->route('password.forced.edit');
        }

        return $next($request);
    }
}
