<?php

namespace App\Models;

use App\Services\Season;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Product extends Base
{
    const TYPE_PRODUCTABLE_FARM = 'farm';

    protected $fillable = [
        'inventory_id',
        'spring',
        'summer',
        'fall',
        'winter',
        'amount',
        'amount_unit',
        'price',
        'currency_id',
        'price_unit'
    ];

    protected $casts = [
        'spring' => 'integer',
        'summer' => 'integer',
        'fall' => 'integer',
        'winter' => 'integer',
        'amount' => 'float',
        'price' => 'float'
    ];

    // --------------------------------------------------
    // Scopes
    // --------------------------------------------------

    public function scopeSeason(Builder $query): Builder
    {
        $service = new Season();
        $season = $service->season(now());

        if ($season === Season::SPRING) {
            return $query->where('spring', 1);
        } elseif ($season === Season::SUMMER) {
            return $query->where('summer', 1);
        } elseif ($season === Season::FALL) {
            return $query->where('fall', 1);
        } elseif ($season === Season::WINTER) {
            return $query->where('winter', 1);
        }
    }

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function productable(): MorphTo
    {
        return $this->morphTo();
    }
}
