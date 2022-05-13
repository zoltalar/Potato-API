<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\FarmStoreRequest;
use App\Http\Resources\FarmResource;
use App\Models\Farm;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato']);
    }

    public function store(FarmStoreRequest $request)
    {
        $farm = new Farm();
        $farm->fill($request->only($farm->getFillable()));
        $farm->active = 1;

        auth()
            ->user()
            ->farms()
            ->save($farm);

        return new FarmResource($farm);
    }
}
