<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Property;
use App\Policies\PropertyPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Property::class => PropertyPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
