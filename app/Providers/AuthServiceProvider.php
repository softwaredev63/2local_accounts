<?php

namespace App\Providers;

use App\Models\AdminUser;
use App\Models\User;
use App\Models\UserWallet;
use App\Policies\AdminUserPolicy;
use App\Policies\UserPolicy;
use App\Policies\UserWalletPolicy;
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
        AdminUser::class => AdminUserPolicy::class,
        UserWallet::class => UserWalletPolicy::class,
        User::class => UserPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
