<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\City;
use App\Models\Unit;
use App\Services\Parameter\CountryHeader;
use App\Services\Parameter\LimitVar;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $limit = (new LimitVar(max: 25))->get();
        $country = (new CountryHeader())->get();
        $countryId = $request->country_id;
        $stateId = $request->state_id;
        $population = $request->population;
        $cityId = $request->city_id;

        if ( ! empty($countryId)) {
            $country = null;
        }

        $query = City::query()
            ->with(['state'])
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->search(['name', 'name_ascii', 'zips'], $search);
                });
            })
            ->when($country, function($query) use ($country) {
                return $query->whereHas('state.country', function($query) use ($country) {
                    $query->where('code', $country);
                });
            })
            ->when($countryId, function($query) use ($countryId) {
                return $query->whereHas('state', function($query) use ($countryId) {
                    $query->where('country_id', $countryId);
                });
            })
            ->when($stateId, function($query) use ($stateId) {
                return $query->where('state_id', $stateId);
            })
            ->when($population, function($query) use ($population) {
                return $query->where('population', '>=', (int) $population);
            })
            ->when($cityId, function($query) use ($cityId) {
                return $query->where('id', $cityId);
            })
            ->orders('name', 'asc')
            ->take($limit);

        $cities = $query->get();

        return BaseResource::collection($cities);
    }

    public function show(City $city)
    {
        $city->load(['state.country']);

        return new BaseResource($city);
    }

    public function locate(float $latitude, float $longitude)
    {
        $code = (new CountryHeader())->get();
        $abbreviation = Unit::abbreviation($code, Unit::TYPE_LENGTH);
        $limit = (new LimitVar())->get();

        $query = City::query()
            ->select([
                'id',
                'name',
                'latitude',
                'longitude',
                'state_id'
            ])
            ->haversine($latitude, $longitude, $abbreviation)
            ->havingRaw('distance < ?', [City::radius($abbreviation)])
            ->orderBy('distance', 'asc')
            ->take($limit);

        return BaseResource::collection($query->get());
    }
}
