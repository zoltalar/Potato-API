<?php

declare(strict_types = 1);

namespace App\Models;

use App\Contracts\Addressable as AddressableContract;
use App\Traits\Addressable;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Address extends Base implements AddressableContract
{
    use Addressable;

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

    public function doAddressCoordinates(): bool
    {
        return (bool) config('services.google.address_coordinates');
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
            self::TYPE_LOCATION => __('phrases.location'),
            self::TYPE_MAILING => __('phrases.mailing')
        ];
    }
}
