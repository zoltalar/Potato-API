<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Address;
use App\Models\Farm;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);
        $language = $request->header('X-language');
        $country = $request->header('X-country');
        $categoryId = $request->category_id;
        $type = $request->type;
        $productableId = $request->productable_id;

        if ($type === Product::TYPE_PRODUCTABLE_FARM) {
            $farm = Farm::query()
                ->with('addresses.state.country')
                ->find($productableId);

            if ($farm !== null) {
                $address = $farm
                    ->addresses
                    ->filter(function($address) {
                        return $address->type = Address::TYPE_LOCATION;
                    })
                    ->first();

                if ($address !== null) {
                    $country = $address->state->country->code;
                }
            }
        }

        $inventory = Inventory::query()
            ->with([
                'category',
                'category.translations' => function($query) use ($language) {
                    $query->whereHas('language', function($query) use ($language) {
                        $query->where('code', $language);
                    });
                },
                'translations' => function($query) use ($language) {
                    $query->whereHas('language', function($query) use ($language) {
                        $query->where('code', $language);
                    });
                }
            ])
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
            ->when($categoryId, function($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->orders('name', 'asc')
            ->take($limit)
            ->get()
            ->sortBy('translations.0.name');

        return BaseResource::collection($inventory);
    }
}
