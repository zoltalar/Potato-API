<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Product extends Base
{
    protected $fillable = [
        'inventory_id',
        'available_from',
        'available_to',
        'amount',
        'unit'
    ];

    protected $casts = [
        'available_from' => 'integer',
        'available_to' => 'integer',
        'amount' => 'float'
    ];

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
