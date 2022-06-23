<?php

namespace App\Providers;

use App\Mixins\StrMixin;
use App\Models\Address;
use App\Models\Base;
use App\Models\Category;
use App\Models\City;
use App\Models\Farm;
use App\Models\Image;
use App\Models\Inventory;
use App\Models\Message;
use App\Models\Review;
use App\Observers\AddressObserver;
use App\Observers\CategoryObserver;
use App\Observers\CityObserver;
use App\Observers\FarmObserver;
use App\Observers\ImageObserver;
use App\Observers\InventoryObserver;
use App\Observers\MessageObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
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

        // Morph Maps
        Relation::enforceMorphMap([
            'category' => Category::class,
            'farm' => Farm::class,
            'image' => Image::class,
            'inventory' => Inventory::class,
            'review' => Review::class
        ]);

        // Observers
        Address::observe(AddressObserver::class);
        Category::observe(CategoryObserver::class);
        City::observe(CityObserver::class);
        Farm::observe(FarmObserver::class);
        Image::observe(ImageObserver::class);
        Inventory::observe(InventoryObserver::class);
        Message::observe(MessageObserver::class);
    }
}
