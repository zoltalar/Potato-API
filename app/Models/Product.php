<?php

namespace App\Models;

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
        'unit'
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

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function productable(): MorphTo
    {
        return $this->morphTo();
    }
}
