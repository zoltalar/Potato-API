<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\AddressRequest;
use App\Http\Requests\Potato\ContactInformationUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\FarmResource;
use App\Http\Resources\UserResource;
use App\Models\Address;
use App\Models\City;
use App\Models\Farm;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato'])->except(['meta']);
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

        return new AddressResource($address);
    }

    public function meta()
    {
        $meta = [
            'addressable_types' => Address::addressableTypes(),
            'types' => Address::types()
        ];

        return response()->json($meta);
    }
}
