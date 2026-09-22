<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(function (Failed $event) {
            Log::error('Login failed event', [
                'email' => $event->credentials['email'] ?? null,
                'password' => $event->credentials['password'] ?? null,
            ]);
        });
    }
}
