<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\AddressRequest;
use App\Http\Requests\Potato\ContactInformationUpdateRequest;
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
        $this->middleware(['auth:user', 'scope:potato']);
    }

    public function save(AddressRequest $request, string $type, int $id)
    {
        $addressable = null;

        if ($type === Address::TYPE_ADDRESSABLE_FARM) {

        }
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
