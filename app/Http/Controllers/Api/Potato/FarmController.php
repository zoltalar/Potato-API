<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\FarmContactInformationUpdateRequest;
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

    public function show(int $id)
    {
        $farm = Farm::query()
            ->where('user_id', auth()->id())
            ->find($id);

        return new FarmResource($farm);
    }

    public function updateContactInformation(FarmContactInformationUpdateRequest $request, int $id)
    {
        $farm = Farm::query()
            ->where('user_id', auth()->id())
            ->find($id);

        if ($farm !== null) {
            $farm->fill($request->only($farm->getFillable()));
            $farm->update();
        }

        return new FarmResource($farm);
    }

    public function updateDescription(Request $request, int $id)
    {
        $farm = Farm::query()
            ->where('user_id', auth()->id())
            ->find($id);

        if ($farm !== null) {
            $farm->update(['description' => $request->description]);
        }

        return new FarmResource($farm);
    }

    public function updateOperatingHours(Request $request, int $id)
    {
        $farm = Farm::query()
            ->where('user_id', auth()->id())
            ->find($id);

        if ($farm !== null) {
            $farm->update(['operating_hours' => $request->operating_hours]);
        }

        return new FarmResource($farm);
    }
}
