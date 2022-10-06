<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\City;
use App\Models\Country;
use App\Models\Unit;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);
        $country = $request->header('X-country', Country::CODE_PL);
        $countryId = $request->country_id;
        $stateId = $request->state_id;
        $population = $request->population;

        if ($limit > 25) {
            $limit = 25;
        }

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

    public function locate(Request $request, float $latitude, float $longitude)
    {
        $code = $request->header('X-country', Country::CODE_PL);
        $abbreviation = Unit::unitAbbreviation($code, Unit::TYPE_LENGTH);
        $limit = $request->get('limit', 10);

        if ($limit > 10) {
            $limit = 10;
        }

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
