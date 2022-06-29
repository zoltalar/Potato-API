<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Price extends Base
{
    protected $fillable = [
        'productable_id',
        'productable_type',
        'inventory_id',
        'price',
        'currency_id',
        'price_unit'
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
