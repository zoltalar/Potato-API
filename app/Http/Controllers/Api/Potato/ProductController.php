<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\ProductsRequest;
use App\Http\Resources\BaseResource;
use App\Models\Address;
use App\Models\Country;
use App\Models\Language;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato'])->except(['topGrowingAreas']);
    }

    public function save(ProductsRequest $request, string $type, int $id)
    {
        $productable = null;

        if ($type === Product::TYPE_PRODUCTABLE_FARM) {
            $productable = auth()
                ->user()
                ->farms()
                ->with(['products'])
                ->find($id);
        }

        if ($productable !== null) {
            $products = $request->get('products', []);

            if (count($products) == 0) {
                $productable->products()->delete();
            } else {
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

                $ids = collect($products)
                    ->pluck('inventory_id')
                    ->toArray();

                $productable
                    ->products()
                    ->whereNotIn('inventory_id', $ids)
                    ->delete();
            }

            return BaseResource::collection($productable->products()->get());
        }

        return response()->json(null, 204);
    }

    public function topGrowingAreas(Request $request, int $id)
    {
        $country = $request->header('X-country', Country::CODE_PL);
        $language = $request->header('X-language', Language::CODE_PL);

        $products = Product::query()
            ->select([
                'id',
                'productable_id',
                'productable_type',
                'inventory_id'
            ])
            ->with([
                'inventory' => function($query) {
                    $query->select([
                        'id',
                        'name'
                    ]);
                },
                'inventory.translations' => function($query) use ($language) {
                    $query->when($language, function($query) use ($language) {
                        return $query->whereHas('language', function($query) use ($language) {
                            $query->where('code', $language);
                        });
                    });
                },
                'productable' => function($query) {
                    $query->select(['id']);
                },
                'productable.addresses' => function($query) {
                    $query
                        ->select([
                            'id',
                            'city',
                            'state_id',
                            'addressable_id',
                            'addressable_type'
                        ])
                        ->where('type', Address::TYPE_LOCATION);
                },
                'productable.addresses.state'
            ])
            ->withCount('inventory')
            ->where('inventory_id', $id)
            ->get();

        return BaseResource::collection($products);
    }
}
