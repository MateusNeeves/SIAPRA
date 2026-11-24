<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class LimitesRecuperacao
{
    public function admit(string $username, string $ip): bool
    {
        $login = 'recovery:login:'.hash('sha256', $username);
        $address = 'recovery:ip:'.hash('sha256', $ip);
        $keys = [$login, $address];
        sort($keys, SORT_STRING); // total ordering avoids deadlocks
        $held = [];
        try {
            foreach ($keys as $key) {
                $lock = Cache::lock($key.':lock', 10);
                // Do not queue requests behind a contended key; fail closed.
                if (! $lock->get()) {
                    return false;
                }
                $held[] = $lock;
            }
            if (RateLimiter::tooManyAttempts($login, max(1, config('recuperacao.login_limit')))
                || RateLimiter::tooManyAttempts($address, max(1, config('recuperacao.ip_limit')))) {
                return false;
            }
            RateLimiter::hit($login, 3600);
            RateLimiter::hit($address, 3600);

            return true;
        } catch (\Throwable $e) {
            logger()->warning('Limitador de recuperação indisponível.');

            return false;
        } finally {
            foreach (array_reverse($held) as $lock) {
                try {
                    $lock->release();
                } catch (\Throwable $e) {
                    logger()->warning('Liberação de limitador de recuperação indisponível.');
                }
            }
        }
    }
}
