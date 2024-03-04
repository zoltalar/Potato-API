<?php

declare(strict_types = 1);

namespace App\Services\Search;

use App\Models\City;
use App\Models\Inventory;
use App\Services\Parameter\CountryHeader;

abstract class BaseSearch 
{
    /** @var Request */
    protected $request;
    
    public function __construct(?Request $request = null) 
    {
        if (empty($request)) {
            $request = request();
        }
        
        $this->request = $request;
    }
    
    protected function inventoryId(): int
    {
        $itemName = $this->request->item_name;
        $inventoryId = $this->request->get('inventory_id', 0);
        
        if (empty($inventoryId) && ! empty($itemName)) {
            $inventory = Inventory::query()
                ->whereHas('translations', function($query) use ($item) {
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
        $cityName = $this->request->city_name;
        $cityId = $this->request->get('city_id', 0);
        
        if (empty($cityId) && ! empty($cityName)) {
            $country = (new CountryHeader())->get();
            
            $city = City::query()
                ->search(['name', 'name_ascii'], $cityName)
                ->whereHas('state.country', function($query) use ($country) {
                    $query->where('code', $country);
                })
                ->first();
        } else {
            $city = City::find($cityId);
        }
        
        return $city;
    }
    
    abstract public function results(): mixed;
}
