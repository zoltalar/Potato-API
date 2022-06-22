<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\ProductsRequest;
use App\Http\Resources\BaseResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato']);
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
}
