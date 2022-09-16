<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\OperatingHoursBatchRequest;
use App\Http\Requests\Potato\OperatingHoursRequest;
use App\Http\Resources\BaseResource;
use App\Models\Farm;
use App\Models\Market;
use App\Models\OperatingHour;

class OperatingHourController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato'])->except(['meta']);
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
                $hours->exceptions = $request->exceptions;

                $operatable->operatingHours()->save($hours);
            } else {
                $hours->fillFromRequest($request);
                $hours->exceptions = $request->exceptions;
                $hours->update();
            }
        }

        return new BaseResource($hours);
    }

    public function saveBatch(OperatingHoursBatchRequest $request, string $type, int $id)
    {
        $operatable = null;

        if ($type === OperatingHour::TYPE_OPERATABLE_MARKET) {
            $operatable = Market::query()
                ->with(['operatingHours'])
                ->find($id);
        }

        if ($operatable !== null) {
            $hours = $request->get('hours', []);

            if (count($hours) == 0) {
                $operatable->operatingHours()->delete();
            } else {
                $operatingHours = $operatable->operatingHours;

                foreach ($hours as $i => $attributes) {
                    $hour = $operatingHours[$i] ?? null;
                }

                /*
                foreach ($products as $attributes) {
                    $product = $productable
                        ->products
                        ->filter(function($product) use ($attributes) {
                            return $product->inventory_id == $attributes['inventory_id'];
                        })
                        ->first();

                    if ($product !== null) {
                        $product->update($attributes);
                    } else {
                        $productable->products()->save(new Product($attributes));
                    }
                }
                */
            }
        }
    }

    public function meta()
    {
        return response()->json(['types' => OperatingHour::types()]);
    }
}
