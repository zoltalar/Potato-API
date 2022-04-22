<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensExpireIn(now()->addHours(8));
        Passport::personalAccessTokensExpireIn(now()->addHours(8));

        Passport::tokensCan([
            'admin' => 'Access Admin',
            'potato' => 'Access Potato'
        ]);

        Passport::setDefaultScope(['potato']);
    }
}
