<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // SessionGuard fires Failed before its Timebox returns. Missing accounts
        // must perform the same definitive hash work inside that envelope.
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
            if ($event->guard === 'web' && $event->user === null && isset($event->credentials['password'])) {
                app(\App\Services\SenhaFicticia::class)->check($event->credentials['password']);
            }
        });
    }
}
