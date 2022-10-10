<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Models\Category;
use App\Models\Country;
use App\Models\Language;
use App\Services\Response\CategoryInventory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);
        $language = $request->header('X-language', Language::CODE_PL);

        if ($limit > 10) {
            $limit = 10;
        }

        $query = Category::query()
            ->with([
                'translations' => function($query) use ($language) {
                    $query->whereHas('language', function($query) use ($language) {
                        $query->where('code', $language);
                    });
                }
            ])
            ->active()
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query
                        ->search(['name'], $search)
                        ->orWhereHas('translations', function($query) use ($search) {
                            $query->search(['name'], $search);
                        });
                });
            })
            ->orders('list_order', 'asc');

        $categories = ($request->all ? $query->get() : $query->paginate($limit));

        return BaseResource::collection($categories);
    }

    public function inventory(Request $request)
    {
        $country = $request->header('X-country', Country::CODE_PL);

        $categories = Category::query()
            ->with([
                'inventory.translations.language',
                'translations.language'
            ])
            ->when($country, function($query) use ($country) {
                return $query->whereHas('inventory.countries', function($query) use ($country) {
                    $query->where('code', $country);
                });
            })
            ->orderBy('list_order')
            ->get();

        $inventory = new CategoryInventory();
        $inventory->setCollection($categories);
        $inventory->setRequest($request);

        return response()->json($inventory->json());
    }
}
