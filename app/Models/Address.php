<?php

declare(strict_types = 1);

namespace App\Models;

use App\Contracts\Addressable as AddressableContract;
use App\Contracts\Coordinable as CoordinableContract;
use App\Traits\Addressable;
use App\Traits\Coordinable;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Address extends Base implements
    AddressableContract,
    CoordinableContract
{
    use Addressable,
        Coordinable;

    const DEFAULT_RADIUS_KM = 60;
    const DEFAULT_RADIUS_MI = 40;

    const TYPE_LOCATION = 1;
    const TYPE_MAILING = 2;

    const TYPE_ADDRESSABLE_FARM = 'farm';
    const TYPE_ADDRESSABLE_MARKET = 'market';

    protected $fillable = [
        'address',
        'address_2',
        'city',
        'state_id',
        'zip',
        'latitude',
        'longitude',
        'timezone',
        'directions',
        'type'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'type' => 'integer'
    ];

    // --------------------------------------------------
    // Accessors and Mutators
    // --------------------------------------------------

    public function setDirectionsAttribute($value): void
    {
        if ( ! empty($value)) {
            $value = strip_tags($value);
        }

        $this->attributes['directions'] = $value;
    }

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public static function radius(string $unit, int $radius = 100): int
    {
        switch($unit) {
            default:
            case Unit::ABBREVIATION_KILOMETER:
                return min(self::DEFAULT_RADIUS_KM, $radius);
            case Unit::ABBREVIATION_MILE:
                return min(self::DEFAULT_RADIUS_MI, $radius);
        }
    }

    public function resolveAddressCoordinates(): bool
    {
        return (bool) config('services.google.resolve_address_coordinates');
    }

    public static function addressableTypes(): array
    {
        return [
            self::TYPE_ADDRESSABLE_FARM,
            self::TYPE_ADDRESSABLE_MARKET
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_LOCATION => 'location',
            self::TYPE_MAILING => 'mailing'
        ];
    }
}
