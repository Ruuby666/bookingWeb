<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // This method is intentionally left empty because there are no services to register at this time.
    }

    public function boot(): void
    {
        // This method is intentionally left empty because there are no actions to perform during the application's bootstrapping phase.
    }
}
