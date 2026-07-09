<?php

namespace App\Providers;

use App\Models\Property;
use App\Models\Reservation;
use App\Models\ReservationPrice;
use App\Policies\PropertyPolicy;
use App\Policies\ReservationPolicy;
use App\Policies\ReservationPricePolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Property::class, PropertyPolicy::class);
        Gate::policy(Reservation::class, ReservationPolicy::class);
        Gate::policy(ReservationPrice::class, ReservationPricePolicy::class);

        // The framework's built-in pagination views assume Tailwind/Bootstrap
        // are loaded; this app ships neither, so use a plain, dependency-free view.
        Paginator::defaultView('pagination.plain');
    }
}
