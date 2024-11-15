<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserClasses
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  array|string  $allowedClasses
     * @return mixed
     */
    public function handle($request, Closure $next, ...$allowedClasses)
    {
        // Obtem as classes do usuário
        $userClasses = Auth::user()->getClassNamesAttribute();

        // Verifica se o usuário pertence a alguma classe permitida
        if (empty(array_intersect($allowedClasses, $userClasses))) {
            // Redireciona ou retorna erro se não tiver acesso
            return redirect('/unauthorized'); // Customize o redirecionamento
        }
        
        return $next($request);
    }
}
