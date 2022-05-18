<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\ContactInformationUpdateRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\FarmResource;
use App\Http\Resources\UserResource;
use App\Models\City;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato']);
    }

    public function farms()
    {
        $farms = auth()
            ->user()
            ->farms()
            ->orderBy('name', 'asc')
            ->get();

        return FarmResource::collection($farms);
    }

    public function updateContactInformation(ContactInformationUpdateRequest $request)
    {

    }
}
