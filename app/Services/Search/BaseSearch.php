<?php

declare(strict_types = 1);

namespace App\Services\Search;

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
        $item = $this->request->item;
        $inventoryId = $this->request->get('inventory_id', 0);
        
        if (empty($inventoryId) && ! empty($item)) {
            $inventory = Inventory::query()
                ->whereHas('translations', function($query) use ($item) {
                    $query->search(['name'], $item);
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
        $location = $this->request->location;
        $cityId = $this->request->get('city_id', 0);
        
        if (empty($cityId) && ! empty($location)) {
            $country = (new CountryHeader())->get();
            
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
    
    abstract public function results(): mixed;
}
