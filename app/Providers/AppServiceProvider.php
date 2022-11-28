<?php

namespace App\Providers;

use App\Mixins\StrMixin;
use App\Models\Address;
use App\Models\Base;
use App\Models\Category;
use App\Models\City;
use App\Models\Event;
use App\Models\Farm;
use App\Models\Image;
use App\Models\Inventory;
use App\Models\Market;
use App\Models\Message;
use App\Models\OperatingHour;
use App\Models\Review;
use App\Models\VerificationCode;
use App\Observers\AddressObserver;
use App\Observers\CategoryObserver;
use App\Observers\CityObserver;
use App\Observers\EventObserver;
use App\Observers\FarmObserver;
use App\Observers\ImageObserver;
use App\Observers\InventoryObserver;
use App\Observers\MarketObserver;
use App\Observers\MessageObserver;
use App\Observers\VerificationCodeObserver;
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
            'event' => Event::class,
            'farm' => Farm::class,
            'image' => Image::class,
            'inventory' => Inventory::class,
            'market' => Market::class,
            'operating_hour' => OperatingHour::class,
            'review' => Review::class
        ]);

        // Observers
        Address::observe(AddressObserver::class);
        Category::observe(CategoryObserver::class);
        City::observe(CityObserver::class);
        Event::observe(EventObserver::class);
        Farm::observe(FarmObserver::class);
        Image::observe(ImageObserver::class);
        Inventory::observe(InventoryObserver::class);
        Market::observe(MarketObserver::class);
        Message::observe(MessageObserver::class);
        VerificationCode::observe(VerificationCodeObserver::class);
    }
}
