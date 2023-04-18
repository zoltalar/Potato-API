<?php

declare(strict_types = 1);

namespace App\Services\Search;

use App\Models\Address;
use App\Models\City;
use App\Models\Event;
use App\Models\Unit;
use App\Services\Parameter\CountryHeader;
use App\Services\Parameter\LimitVar;
use Illuminate\Pagination\LengthAwarePaginator;

final class EventsSearch extends BaseSearch
{
    public function results(): mixed
    {
        // We have the city model
        if (($city = $this->city()) !== null) {
            return $this->haversineSearch($city);
        }
        
        return $this->basicSearch();
    }
    
    protected function haversineSearch(City $city): LengthAwarePaginator
    {
        $country = (new CountryHeader())->get();
        $abbreviation = Unit::abbreviation($country, Unit::TYPE_LENGTH);
        $radius = Address::radius($abbreviation, (int) $this->request->radius);
        $scope = (int) $this->request->scope;
        $keyword = $this->request->keyword;
        $limit = (new LimitVar())->get();
        
        return Event::query()
            ->with([
                'addresses' => function($query) use ($city, $abbreviation) {
                    $query->select();
                    $query->haversine($city->latitude, $city->longitude, $abbreviation);
                    $query->where('type', Address::TYPE_LOCATION);
                },
                'addresses.state.country'
            ])
            ->approved()
            ->when($keyword, function($query) use ($keyword) {
                return $query->where(function($query) use ($keyword) {
                    $query->search(['title', 'description'], $keyword);
                });
            })
            ->where(function($query) use ($city, $abbreviation, $radius) {
                $query->whereHas('addresses', function($query) use ($city, $abbreviation, $radius) {
                    $query
                        ->haversine($city->latitude, $city->longitude, $abbreviation)
                        ->where('type', Address::TYPE_LOCATION)
                        ->havingRaw('distance < ?', [$radius]);
                });
            })
            ->when($scope, function($query) use ($scope) {
                if ($scope === Event::SCOPE_FUTURE) {
                    return $query->future();
                } elseif ($scope === Event::SCOPE_PAST) {
                    return $query->past();
                }
            })
            ->orderBy('start_date')
            ->paginate($limit);
    }
    
    protected function basicSearch(): LengthAwarePaginator
    {
        $location = $this->request->location;
        $scope = (int) $this->request->scope;
        $keyword = $this->request->keyword;
        $limit = (new LimitVar())->get();
        
        return Event::query()
            ->with([
                'addresses' => function($query) {
                    $query->where('type', Address::TYPE_LOCATION);
                },
                'addresses.state.country'
            ])
            ->approved()
            ->when($keyword, function($query) use ($keyword) {
                return $query->where(function($query) use ($keyword) {
                    $query->search(['title', 'description'], $keyword);
                });
            })
            ->where(function($query) use ($location) {
                $query
                    ->search(['city'], $location)
                    ->where('type', Address::TYPE_LOCATION);
            })
            ->when($scope, function($query) use ($scope) {
                if ($scope === Event::SCOPE_FUTURE) {
                    return $query->future();
                } elseif ($scope === Event::SCOPE_PAST) {
                    return $query->past();
                }
            })
            ->orderBy('start_date')
            ->paginate($limit);
    }
}
