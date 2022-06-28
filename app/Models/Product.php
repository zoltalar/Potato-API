<?php

namespace App\Models;

use App\Http\Controllers\Api\Potato\CurrencyController;
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
        'currency_id'
    ];

    protected $casts = [
        'spring' => 'integer',
        'summer' => 'integer',
        'fall' => 'integer',
        'winter' => 'integer',
        'amount' => 'float'
    ];

    public $timestamps = false;

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
