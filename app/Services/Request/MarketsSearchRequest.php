<?php

declare(strict_types = 1);

namespace App\Services\Request;

use App\Models\Address;
use App\Models\City;
use App\Models\Inventory;
use App\Models\Market;
use App\Models\Unit;
use App\Traits\SearchableRequest;
use Illuminate\Pagination\LengthAwarePaginator;

final class MarketsSearchRequest extends BaseRequest
{
    use SearchableRequest;
     
    public function get(): mixed
    {
        $inventoryId = $this->inventoryId();
        
        // Inventory item not found so bail out
        if (empty($inventoryId)) {
            return collect([]);
        }
        
        // We have the city model
        if (($city = $this->city()) !== null) {
            return $this->haversineSearch($city, $inventoryId);
        }
        
        return $this->basicSearch($inventoryId);
    }
    
    protected function inventoryId(): int
    {
        $itemName = $this->request->item;
        $inventoryId = $this->request->get('inventory_id', 0);
        
        if (empty($inventoryId) && ! empty($itemName)) {
            $inventory = Inventory::query()
                ->whereHas('translations', function($query) use ($itemName) {
                    $query->search(['name'], $itemName);
                })
                ->first();
                
            if ($inventory !== null) {
                $inventoryId = $inventory->id;
            }
        }
        
        return (int) $inventoryId;
    }
    
    protected function city(): City|null
    {
        $locationName = $this->request->location;
        $cityId = $this->request->get('city_id', 0);
        
        if (empty($cityId) && ! empty($locationName)) {
            $country = $this->country();
            
            $city = City::query()
                ->search(['name', 'name_ascii'], $locationName)
                ->whereHas('state.country', function($query) use ($country) {
                    $query->where('code', $country);
                })
                ->first();
        } else {
            $city = City::find($cityId);
        }
        
        return $city;
    }
    
    protected function haversineSearch(City $city, int $inventoryId): LengthAwarePaginator
    {
        $country = $this->country();
        $abbreviation = Unit::abbreviation($country, Unit::TYPE_LENGTH);
        $radius = Address::radius($abbreviation, (int) $this->request->radius);
        
        return Market::query()
            ->with([
                'addresses' => function($query) use ($city, $abbreviation) {
                    $query->select();
                    $query->haversine($city->latitude, $city->longitude, $abbreviation);
                    $query->where('type', Address::TYPE_LOCATION);
                },
                'addresses.state.country',
                'images' => function($query) {
                    $query->primary();
                },
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
            ->paginate($this->limit());
    }
    
    protected function basicSearch(int $inventoryId): LengthAwarePaginator
    {
        $locationName = $this->request->location;
        
        return Market::query()
            ->with([
                'addresses' => function($query) {
                    $query->where('type', Address::TYPE_LOCATION);
                },
                'addresses.state.country',
                'images' => function($query) {
                    $query->primary();
                },
                'products.inventory.translations'
            ])
            ->active()
            ->whereHas('addresses', function($query) use ($locationName) {
                $query
                    ->search(['city'], $locationName)
                    ->where('type', Address::TYPE_LOCATION);
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
            ->paginate($this->limit());
    }
}
