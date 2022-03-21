<?php

declare(strict_types = 1);

namespace App\Observers;

use App\Models\City;
use Str;

class CityObserver
{
    public function saving(City $city)
    {
        if (empty($city->name_ascii)) {
            $city->name_ascii = Str::ascii($city->name);
        }
    }
}
