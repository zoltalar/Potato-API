<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\AddressRequest;
use App\Http\Resources\BaseResource;
use App\Models\Address;
use App\Models\Country;
use App\Models\Farm;
use App\Models\Unit;
use Cache;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato'])->except(['plot', 'meta']);
    }

    public function save(AddressRequest $request, string $type, int $id)
    {
        $address = null;
        $addressable = null;

        if ($type === Address::TYPE_ADDRESSABLE_FARM) {
            $addressable = Farm::query()
                ->with(['addresses'])
                ->where('user_id', auth()->id())
                ->find($id);
        }

        if ($addressable !== null) {
            $addressable->fill($request->addressable);
            $addressable->update();

            $address = $addressable
                ->addresses
                ->filter(function($address) use ($request) {
                    return $address->type == $request->address['type'];
                })
                ->first();

            if ($address !== null) {
                $address->fill($request->address);
                $address->update();
            } else {
                $address = new Address();
                $address->fill($request->address);
                $address->type = $request->address['type'];

                $addressable->addresses()->save($address);
            }
        }

        return new BaseResource($address);
    }

    public function plot(Request $request, string $type)
    {
        $code = $request->header('X-country', Country::CODE_PL);
        $cacheKey = sprintf('potato.addresses.plot.%s', $code);

        if (Cache::has($cacheKey)) {
            $addresses = Cache::get($cacheKey);
        } else {
            $addresses = Address::query()
                ->select([
                    'latitude',
                    'longitude',
                    'addressable_id',
                    'addressable_type'
                ])
                ->with([
                    'addressable' => function($query) {
                        $query->select(['id', 'name']);
                    }
                ])
                ->where('addressable_type', $type)
                ->where('type', Address::TYPE_LOCATION)
                ->whereHas('addressable', function($query) {
                    $query->active();
                })
                ->whereHas('state.country', function($query) use ($code) {
                    $query->where('code', $code);
                })
                ->get();

            Cache::put($cacheKey, $addresses, 600);
        }

        return BaseResource::collection($addresses);
    }

    public function meta(Request $request)
    {
        $code = $request->header('X-country', Country::CODE_PL);
        $abbreviation = Unit::unitAbbreviation($code, Unit::TYPE_LENGTH);

        $meta = [
            'addressable_types' => Address::addressableTypes(),
            'types' => Address::types(),
            'radius' => Address::radius($abbreviation)
        ];

        return response()->json($meta);
    }
}
