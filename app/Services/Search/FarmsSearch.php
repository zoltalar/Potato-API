<?php

declare(strict_types = 1);

namespace App\Services\Search;

use App\Models\Address;
use App\Models\City;
use App\Models\Farm;
use App\Models\Unit;
use App\Services\Parameter\CountryHeader;
use App\Services\Parameter\LimitVar;
use Illuminate\Pagination\LengthAwarePaginator;

final class FarmsSearch extends BaseSearch
{
    public function results(): mixed
    {
        $inventoryId = $this->inventoryId();
        
        // We have the city model
        if (($city = $this->city()) !== null) {
            return $this->haversineSearch($city, $inventoryId);
        }
        
        return $this->basicSearch($inventoryId);
    }
    
    protected function haversineSearch(City $city, int $inventoryId): LengthAwarePaginator
    {
        $country = (new CountryHeader())->get();
        $abbreviation = Unit::abbreviation($country, Unit::TYPE_LENGTH);
        $radius = Address::radius($abbreviation, (int) $this->request->radius);
        $limit = (new LimitVar())->get();
        
        return Farm::query()
            ->with([
                'addresses' => function($query) use ($city, $abbreviation) {
                    $query->select();
                    $query->haversine($city->latitude, $city->longitude, $abbreviation);
                    $query->where('type', Address::TYPE_LOCATION);
                },
                'addresses.state.country',
                'images',
                'products.inventory.translations'
            ])
            ->active()
            ->where(function($query) use ($city, $abbreviation, $radius) {
                $query->whereHas('addresses', function($query) use ($city, $abbreviation, $radius) {
                    $query
                        ->haversine($city->latitude, $city->longitude, $abbreviation)
                        ->where('type', Address::TYPE_LOCATION)
                        ->havingRaw('distance < ?', [$radius]);
                });
            })
            ->when( ! empty($inventoryId), function($query) use ($inventoryId) {
                return $query->where(function($query) use ($inventoryId) {
                    $query->whereHas('products', function($query) use ($inventoryId) {
                        $query
                            ->season()
                            ->where('inventory_id', $inventoryId);
                    });
                });
            })
            ->orderBy('promote', 'desc')
            ->paginate($limit);
    }
    
    protected function basicSearch(int $inventoryId): LengthAwarePaginator
    {
        $cityName = $this->request->city_name;
        $limit = (new LimitVar())->get();
        
        return Farm::query()
            ->with([
                'addresses' => function($query) {
                    $query->where('type', Address::TYPE_LOCATION);
                },
                'addresses.state.country',
                'images',
                'products.inventory.translations'
            ])
            ->active()
            ->when( ! empty($cityName), function($query) use ($cityName) {
                return $query->where(function($query) use ($cityName) {
                    $query->whereHas('addresses', function($query) use ($cityName) {
                        $query
                            ->search(['city'], $cityName)
                            ->where('type', Address::TYPE_LOCATION);
                    });
                });
            })
            ->when( ! empty($inventoryId), function($query) use ($inventoryId) {
                return $query->where(function($query) use ($inventoryId) {
                    $query->whereHas('products', function($query) use ($inventoryId) {
                        $query
                            ->season()
                            ->where('inventory_id', $inventoryId);
                    });
                });
            })
            ->orderBy('promote', 'desc')
            ->paginate($limit);
    }
}
