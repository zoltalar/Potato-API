<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\ProductsRequest;
use App\Http\Resources\ProductResource;
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
                ->find($id);
        }

        if ($productable !== null) {
            $products = $request->get('products', []);

            if (count($products) == 0) {
                $productable->products()->delete();
            } else {
                foreach ($products as $product) {
                    $product['inventory_id'] = $product['id'];
                    unset($product['id']);
                    $productable->products()->save(new Product($product));
                }
            }

            return ProductResource::collection($productable->products()->get());
        }

        return response()->json(null, 204);
    }
}
