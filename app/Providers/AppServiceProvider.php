<?php

namespace App\Providers;

use App\Mixins\StrMixin;
use App\Models\Base;
use App\Models\City;
use App\Observers\CityObserver;
use Illuminate\Support\ServiceProvider;
use Schema;
use Str;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Schema::defaultStringLength(Base::DEFAULT_STRING_LENGTH);

        // Mixins
        Str::mixin(new StrMixin());

        // Observers
        City::observe(CityObserver::class);
    }
}
