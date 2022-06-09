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

    const TYPE_LOCATION = 1;
    const TYPE_MAILING = 2;

    const TYPE_ADDRESSABLE_FARM = 'farm';

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

    public function resolveAddressCoordinates(): bool
    {
        return (bool) config('services.google.resolve_address_coordinates');
    }

    public static function addressableTypes(): array
    {
        return [
            self::TYPE_ADDRESSABLE_FARM
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
