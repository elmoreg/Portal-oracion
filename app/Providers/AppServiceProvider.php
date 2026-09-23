<?php

namespace App\Providers;

use App\Services\Assignment\AssignmentService;
use App\Services\GeoIp\GeoIpService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GeoIpService::class);

        $this->app->singleton(AssignmentService::class, fn () => new AssignmentService(
            maxPerIntercessor: (int) config('prayer.assignment.max_per_intercessor', 10),
            batchSize: (int) config('prayer.assignment.batch_size', 100),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme('https');
        }
    }
}
