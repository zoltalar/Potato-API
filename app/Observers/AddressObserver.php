<?php

declare(strict_types = 1);

namespace App\Observers;

use App\Models\Address;
use App\Services\AddressCoordinatesResolver;

class AddressObserver
{
    public function saving(Address $address)
    {
        if ($address->resolveAddressCoordinates()) {
            $dirtyAttributes = array_keys($address->getDirty());
            $attributes = ['address', 'address_2', 'city', 'state_id', 'zip'];

            if (count(array_intersect($dirtyAttributes, $attributes)) > 0
                || empty($address->latitude)
                || empty($address->longitude)) {
                $coordinates = (new AddressCoordinatesResolver())->resolve($address->addressLine(','));
                list($latitude, $longitude) = $coordinates;

                $address->latitude = $latitude;
                $address->longitude = $longitude;
            }
        }
    }
}
