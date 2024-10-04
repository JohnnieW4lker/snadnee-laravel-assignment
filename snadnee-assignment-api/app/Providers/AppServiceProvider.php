<?php

namespace App\Providers;

use App\Services\AuthenticationService;
use App\Services\ReservationService;
use App\Services\TableRepository;
use App\Services\TableService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuthenticationService::class);
        $this->app->singleton(ReservationService::class);
        $this->app->singleton(TableService::class);
        $this->app->singleton(TableRepository::class);
    }
}
