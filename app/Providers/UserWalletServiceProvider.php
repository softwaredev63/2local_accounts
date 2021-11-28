<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UserWalletService;

/**
 * Class UserWalletServiceProvider
 *
 * @package App\Providers
 * @author Bojte Szabolcs
 */
class UserWalletServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UserWalletService::class, function ($app) {
            return new UserWalletService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
