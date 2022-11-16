<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Event extends Base
{
    protected $fillable = [
        'title',
        'organizer',
        'type',
        'website',
        'phone',
        'email',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'description',
        'status'
    ];

    protected $casts = [
        'type' => 'integer',
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'status' => 'integer'
    ];

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function eventable(): MorphTo
    {
        return $this->morphTo();
    }
}
