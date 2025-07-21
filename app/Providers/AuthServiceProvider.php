<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('view-personnel-profile', function ($user) {
        return $user->role === 'Administrator';
    });

      Gate::define('view-account-payable', function ($user) {
        return $user->role === 'Administrator';
    });

     Gate::define('view-sales-management', function ($user) {
    return in_array($user->role, ['Administrator', 'Secretary']);
    });

    Gate::define('view-store-management', function ($user) {
        return in_array($user->role, ['Administrator', 'Secretary']);
    });

    Gate::define('view-order-management', function ($user) {
    return in_array($user->role, ['Administrator', 'Secretary']);
    });

    Gate::define('view-order-details', function ($user) {
        return in_array($user->role, ['Administrator', 'Secretary', 'Graphic Artist', 'Production']);
    });

    Gate::define('view-inventory', function ($user) {
        return in_array($user->role, ['Administrator', 'Secretary', 'Graphic Artist', 'Production']);
    });

    

    }
}
