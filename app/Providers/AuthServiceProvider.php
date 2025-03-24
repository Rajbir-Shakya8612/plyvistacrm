<?php

namespace App\Providers;

use App\Models\Lead;
use App\Models\Sale;
use App\Policies\LeadPolicy;
use App\Policies\SalePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Lead::class => LeadPolicy::class,
        Sale::class => SalePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('isSalesperson', function ($user) {
            return $user->role === 'salesperson';
        });

        Gate::define('isDealer', function ($user) {
            return $user->role === 'dealer';
        });

        Gate::define('isCarpenter', function ($user) {
            return $user->role === 'carpenter';
        });
    }
} 