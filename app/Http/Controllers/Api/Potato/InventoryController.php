<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InventoryStoreRequest;
use App\Http\Requests\Admin\InventoryUpdateRequest;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use Exception;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);
        $language = $request->get('language');
        $country = $request->get('country');

        $inventory = Inventory::query()
            ->with(['translations' => function($query) use ($language) {
                $query->whereHas('language', function($query) use ($language) {
                    $query->where('code', $language);
                });
            }])
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query
                        ->search(['name'], $search)
                        ->orWhereHas('translations', function($query) use ($search) {
                            $query->search(['name'], $search);
                        });
                });
            })
            ->when($country, function($query) use ($country) {
                return $query->whereHas('countries', function($query) use ($country) {
                    $query->where('code', $country);
                });
            })
            ->orders('name', 'asc')
            ->take($limit)
            ->get()
            ->sortBy('translations.0.name');

        return InventoryResource::collection($inventory);
    }
}
