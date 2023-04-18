<?php

declare(strict_types = 1);

namespace App\Traits;

use App\Models\City;

trait SearchableRequest
{
    protected function city(): City|null
    {
        $location = $this->request->location;
        $cityId = $this->request->get('city_id', 0);
        
        if (empty($cityId) && ! empty($location)) {
            $country = $this->country();
            
            $city = City::query()
                ->search(['name', 'name_ascii'], $location)
                ->whereHas('state.country', function($query) use ($country) {
                    $query->where('code', $country);
                })
                ->first();
        } else {
            $city = City::find($cityId);
        }
        
        return $city;
    }

    
}
