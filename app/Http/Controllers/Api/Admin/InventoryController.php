<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InventoryStoreRequest;
use App\Http\Requests\Admin\InventoryUpdateRequest;
use App\Http\Resources\BaseResource;
use App\Models\Inventory;
use Exception;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = Inventory::query()
            ->with(['category', 'countries'])
            ->withCount('translations')
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query
                        ->search(['name'], $search)
                        ->orWhereHas('category', function($query) use ($search) {
                            $query->search(['name'], $search);
                        });
                });
            })
            ->orders('name', 'asc');

        $inventory = ($request->all ? $query->get() : $query->paginate($limit));

        return BaseResource::collection($inventory);
    }

    public function store(InventoryStoreRequest $request)
    {
        $inventory = new Inventory();
        $inventory->fill($request->only($inventory->getFillable()));

        if ($inventory->save()) {
            $countries = $request->countries ?? [];

            if (is_array($countries)) {
                $inventory->countries()->sync($countries);
            }
        }

        return new BaseResource($inventory);
    }

    public function update(InventoryUpdateRequest $request, Inventory $inventory)
    {
        $inventory->fill($request->only($inventory->getFillable()));

        if ( ! empty($request->delete_photo)) {
            $inventory->deletePhoto();
        }

        if ($inventory->update()) {
            $countries = $request->countries ?? [];

            if (is_array($countries)) {
                $inventory->countries()->sync($countries);
            }
        }

        return new BaseResource($inventory);
    }

    public function destroy(Inventory $inventory)
    {
        $status = 403;

        try {
            if ($inventory->delete(true)) {
                $status = 204;
            }
        } catch (Exception $e) {}

        return response()->json(null, $status);
    }
}
