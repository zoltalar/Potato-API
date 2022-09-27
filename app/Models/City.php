<?php

declare(strict_types = 1);

namespace App\Models;

use App\Contracts\Coordinable as CoordinableContract;
use App\Traits\Coordinable;
use DateTimeZone;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class City extends Base implements CoordinableContract
{
    use Coordinable;

    const DEFAULT_RADIUS_KM = 30;
    const DEFAULT_RADIUS_MI = 20;

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

    protected $constraints = ['addresses'];

    public $timestamps = false;

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public static function radius(string $unit): int
    {
        switch($unit) {
            default:
            case Unit::ABBREVIATION_KILOMETER:
                return self::DEFAULT_RADIUS_KM;
            case Unit::ABBREVIATION_MILE:
                return self::DEFAULT_RADIUS_MI;
        }
    }

    public static function timezones(): array
    {
        return DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    }
}
