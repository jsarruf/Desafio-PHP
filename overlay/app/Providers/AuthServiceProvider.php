<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Gate::define('manage-gateways', fn(User $u) => in_array($u->role, ['ADMIN','MANAGER']));
        Gate::define('manage-products', fn(User $u) => in_array($u->role, ['ADMIN','MANAGER','FINANCE']));
        Gate::define('manage-clients', fn(User $u) => in_array($u->role, ['ADMIN','MANAGER']));
        Gate::define('manage-users', fn(User $u) => in_array($u->role, ['ADMIN','MANAGER']));
        Gate::define('view-transactions', fn(User $u) => in_array($u->role, ['ADMIN','MANAGER','FINANCE']));
        Gate::define('view-clients', fn(User $u) => in_array($u->role, ['ADMIN','MANAGER','FINANCE']));
        Gate::define('refund-transaction', fn(User $u) => in_array($u->role, ['ADMIN','FINANCE']));
    }
}
