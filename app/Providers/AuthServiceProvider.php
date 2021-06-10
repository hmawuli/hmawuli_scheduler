<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

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

        Passport::routes();

        $expire_months = 12 * 5;

        Passport::tokensExpireIn(now()->addMonths($expire_months));

        Passport::refreshTokensExpireIn(now()->addMonths($expire_months));

        Passport::personalAccessTokensExpireIn(now()->addMonths($expire_months));
    }
}
