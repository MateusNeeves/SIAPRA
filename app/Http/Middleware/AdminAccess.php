<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle($request, Closure $next)
    {   
        if(!$request->user())
            return redirect('/login');
        else if ($request->user()->id != 1) {
            return redirect('/')->with('alert-danger', 'Acesso Negado');
        }

        return $next($request);
    }
}
