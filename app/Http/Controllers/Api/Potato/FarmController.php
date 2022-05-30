<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\FarmContactInformationUpdateRequest;
use App\Http\Requests\Potato\FarmDeactivateRequest;
use App\Http\Requests\Potato\FarmDescriptionUpdateRequest;
use App\Http\Requests\Potato\FarmOperatingHoursUpdateRequest;
use App\Http\Requests\Potato\FarmSocialMediaUpdateRequest;
use App\Http\Requests\Potato\FarmStoreRequest;
use App\Http\Resources\FarmResource;
use App\Models\Farm;
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
            ->with([
                'addresses',
                'addresses.state',
                'images' => function($query) {
                    $query
                        ->orderBy('primary', 'desc')
                        ->orderBy('cover', 'desc');
                }
            ])
            ->find($id);

        return new FarmResource($farm);
    }

    public function updateContactInformation(FarmContactInformationUpdateRequest $request, int $id)
    {
        $farm = auth()
            ->user()
            ->farms()
            ->find($id);

        if ($farm !== null) {
            $farm->fill($request->only($farm->getFillable()));
            $farm->update();
        }

        return new FarmResource($farm);
    }

    public function updateDescription(FarmDescriptionUpdateRequest $request, int $id)
    {
        $farm = auth()
            ->user()
            ->farms()
            ->find($id);

        if ($farm !== null) {
            $farm->update(['description' => $request->description]);
        }

        return new FarmResource($farm);
    }

    public function updateOperatingHours(FarmOperatingHoursUpdateRequest $request, int $id)
    {
        $farm = auth()
            ->user()
            ->farms()
            ->find($id);

        if ($farm !== null) {
            $farm->update(['operating_hours' => $request->operating_hours]);
        }

        return new FarmResource($farm);
    }

    public function updateSocialMedia(FarmSocialMediaUpdateRequest $request, int $id)
    {
        $farm = auth()
            ->user()
            ->farms()
            ->find($id);

        if ($farm !== null) {
            $farm->fill($request->only($farm->getFillable()));
            $farm->update();
        }

        return new FarmResource($farm);
    }

    public function deactivate(FarmDeactivateRequest $request, int $id)
    {
        $farm = auth()
            ->user()
            ->farms()
            ->active()
            ->find($id);

        if ($farm !== null) {
            $farm->fill($request->only($farm->getFillable()));
            $farm->active = 0;
            $farm->deactivated_at = $farm->freshTimestamp();
            $farm->update();
        }

        return new FarmResource($farm);
    }
}
