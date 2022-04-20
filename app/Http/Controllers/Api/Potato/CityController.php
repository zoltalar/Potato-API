<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);
        $country = $request->get('country');

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
            ->orders('name', 'asc')
            ->take($limit);

        $cities = $query->get();

        return CityResource::collection($cities);
    }
}
