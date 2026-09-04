<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Product::class => ProductPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function (User $user, string $ability): ?bool {
            return $user->isSuperAdmin() ? true : null;
        });

        Gate::define('manage-users', fn (User $user) => $user->hasPermission('manage-users'));
        Gate::define('create-users', fn (User $user) => $user->hasPermission('create-users'));
        Gate::define('edit-users', fn (User $user) => $user->hasPermission('edit-users'));
        Gate::define('delete-users', fn (User $user) => $user->hasPermission('delete-users'));
        Gate::define('assign-roles', fn (User $user) => $user->hasPermission('assign-roles'));
        Gate::define('manage-admins', fn (User $user) => $user->hasPermission('manage-admins'));
        Gate::define('manage-contributors', fn (User $user) => $user->hasPermission('manage-contributors'));
        Gate::define('manage-customers', fn (User $user) => $user->hasPermission('manage-customers'));
        Gate::define('manage-categories', fn (User $user) => $user->hasPermission('manage-categories'));
        Gate::define('manage-products', fn (User $user) => $user->hasPermission('manage-products'));
        Gate::define('approve-products', fn (User $user) => $user->hasPermission('approve-products'));
        Gate::define('manage-product-files', fn (User $user) => $user->hasPermission('manage-product-files'));
        Gate::define('manage-orders', fn (User $user) => $user->hasPermission('manage-orders'));
        Gate::define('view-payments', fn (User $user) => $user->hasPermission('view-payments'));
        Gate::define('view-paddle-transactions', fn (User $user) => $user->hasPermission('view-paddle-transactions'));
        Gate::define('manage-subscriptions', fn (User $user) => $user->hasPermission('manage-subscriptions'));
        Gate::define('manage-settings', fn (User $user) => $user->hasPermission('manage-settings'));
        Gate::define('view-reports', fn (User $user) => $user->hasPermission('view-reports'));
        Gate::define('view-sales', fn (User $user) => $user->hasPermission('view-sales'));
        Gate::define('view-contributor-earnings', fn (User $user) => $user->hasPermission('view-contributor-earnings'));
        Gate::define('manage-payouts', fn (User $user) => $user->hasPermission('manage-payouts'));
        Gate::define('view-audit-logs', fn (User $user) => $user->hasPermission('view-audit-logs'));
        Gate::define('manage-permissions', fn (User $user) => $user->hasPermission('manage-permissions'));
        Gate::define('manage-system-configuration', fn (User $user) => $user->hasPermission('manage-system-configuration'));
    }
}
