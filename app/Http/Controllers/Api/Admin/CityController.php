<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CityStoreRequest;
use App\Http\Requests\Admin\CityUpdateRequest;
use App\Http\Resources\BaseResource;
use App\Models\City;
use Exception;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = City::query()
            ->with([
                'state',
                'state.country'
            ])
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query
                        ->search(['name', 'name_ascii'], $search)
                        ->orWhereHas('state', function($query) use ($search) {
                            $query->search(['name'], $search);
                        });
                });
            })
            ->orders('name', 'asc');

        $cities = $query->paginate($limit);

        return BaseResource::collection($cities);
    }

    public function store(CityStoreRequest $request)
    {
        $city = new City();
        $city->fill($request->only($city->getFillable()));
        $city->save();

        return new BaseResource($city);
    }

    public function update(CityUpdateRequest $request, City $city)
    {
        $city->fill($request->only($city->getFillable()));
        $city->update();

        return new BaseResource($city);
    }

    public function destroy(City $city)
    {
        $status = 403;

        try {
            if ($city->delete(true)) {
                $status = 204;
            }
        } catch (Exception $e) {}

        return response()->json(null, $status);
    }

    public function meta()
    {
        return response()->json(['timezones' => City::timezones()]);
    }
}
