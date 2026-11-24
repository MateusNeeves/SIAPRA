<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class DesativarRememberMe
{
    public function handle(Request $request, Closure $next)
    {
        // Resolving the guard/name does not resolve its user. Run globally before
        // EncryptCookies/StartSession and before any auth/guest/permission check.
        $name = Auth::guard('web')->getRecallerName();
        $present = $request->cookies->has($name);
        $request->cookies->remove($name);

        $response = $next($request);

        if ($present) {
            // Expire only the recaller, using the same path/domain as Laravel's
            // cookie jar. Even if browser deletion fails, input remains ignored.
            $response->headers->setCookie(Cookie::forget($name));
        }

        return $response;
    }
}
