<?php

declare(strict_types = 1);

namespace App\Models;

use App\Contracts\Coordinable as CoordinableContract;
use App\Traits\Coordinable;
use DateTimeZone;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class City extends Base implements CoordinableContract
{
    use Coordinable;

    protected $fillable = [
        'name',
        'name_ascii',
        'zips',
        'latitude',
        'longitude',
        'timezone',
        'state_id'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    public $timestamps = false;

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public static function timezones(): array
    {
        return DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    }
}
