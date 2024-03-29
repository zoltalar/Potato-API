<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Address;
use App\Models\Farm;
use App\Models\Inventory;
use App\Models\Product;
use App\Services\Parameter\CountryHeader;
use App\Services\Parameter\LanguageHeader;
use App\Services\Parameter\LimitVar;
use App\Services\Response\InventoryCategory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $limit = (new LimitVar(max: 200))->get(); // Limit of 200 needed to fetch products page
        $language = (new LanguageHeader())->get();
        $country = (new CountryHeader())->get();
        $countryable = $request->countryable;
        $categoryId = $request->category_id;
        $type = $request->type;
        $productableId = $request->productable_id;
        $inventoryId = $request->inventory_id;

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
                        ->search(['name'], $search, '%')
                        ->orWhereHas('translations', function($query) use ($search) {
                            $query->search(['name'], $search, '%');
                        });
                });
            })
            ->when($country && $countryable, function($query) use ($country) {
                return $query->whereHas('countries', function($query) use ($country) {
                    $query->where('code', $country);
                });
            })
            ->when($categoryId, function($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($inventoryId, function($query) use ($inventoryId) {
                return $query->where('id', $inventoryId);
            })
            ->orders('name', 'asc')
            ->take($limit)
            ->get()
            ->sortBy('translations.0.name');

        return BaseResource::collection($inventory);
    }

    public function categories(Request $request)
    {
        $language = (new LanguageHeader())->get();
        $country = (new CountryHeader())->get();
        $categoryId = $request->category_id;

        $inventory = Inventory::query()
            ->with([
                'category',
                'category.translations' => function($query) use ($language) {
                    $query->whereHas('language', function($query) use ($language) {
                        $query->where('code', $language);
                    });
                },
                'category.translations.language',
                'translations' => function($query) use ($language) {
                    $query->whereHas('language', function($query) use ($language) {
                        $query->where('code', $language);
                    });
                },
                'translations.language'
            ])
            ->when($country, function($query) use ($country) {
                return $query->whereHas('countries', function($query) use ($country) {
                    $query->where('code', $country);
                });
            })
            ->when($categoryId, function($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->orders('name', 'asc')
            ->get();

        $categories = new InventoryCategory();
        $categories->setCollection($inventory);
        $categories->setRequest($request);

        return response()->json($categories->json());
    }

    public function show(Request $request, int $id)
    {
        $language = (new LanguageHeader())->get();

        $inventory = Inventory::query()
            ->with([
                'category',
                'category.translations' => function($query) use ($language) {
                    $query->whereHas('language', function($query) use ($language) {
                        $query->where('code', $language);
                    });
                },
                'substances',
                'substances.translations' => function ($query) use ($language) {
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
            ->find($id);

        return new BaseResource($inventory);
    }
}
