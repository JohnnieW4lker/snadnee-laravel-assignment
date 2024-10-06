<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Policies\ReservationsPolicy;
use App\Services\AuthenticationService;
use App\Services\ReservationService;
use App\Services\TableRepository;
use App\Services\TableService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Reservation::class, ReservationsPolicy::class);
    }

    public function register(): void
    {
        $this->app->singleton(AuthenticationService::class);
        $this->app->singleton(ReservationService::class);
        $this->app->singleton(TableService::class);
        $this->app->singleton(TableRepository::class);
    }
}
