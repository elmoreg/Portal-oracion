<?php

use App\Providers\AppServiceProvider;
use App\Providers\VoltServiceProvider;

return [
    AppServiceProvider::class,
    AppProvidersEventServiceProvider::class,
    VoltServiceProvider::class,
];
