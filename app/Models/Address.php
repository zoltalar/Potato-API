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
}
