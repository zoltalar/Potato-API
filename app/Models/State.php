<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class State extends Base
{
    protected $fillable = [
        'name',
        'abbreviation',
        'country_id'
    ];

    public $timestamps = false;

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
