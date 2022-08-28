<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\OperatingHoursRequest;
use App\Http\Resources\BaseResource;
use App\Models\Farm;
use App\Models\OperatingHour;

class OperatingHourController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato']);
    }

    public function save(OperatingHoursRequest $request, string $type, int $id)
    {
        $hours = null;
        $operatable = null;

        if ($type === OperatingHour::TYPE_OPERATABLE_FARM) {
            $operatable = Farm::query()
                ->with(['operatingHours'])
                ->find($id);
        }

        if ($operatable !== null) {
            $hours = $operatable->operatingHours;

            if ($hours === null) {
                $hours = new OperatingHour();
                $hours->fillFromRequest($request);

                $operatable->operatingHours()->save($hours);
            } else {
                $hours->fillFromRequest($request);
                $hours->update();
            }
        }

        return new BaseResource($hours);
    }
}
