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
        } elseif ($type === Product::TYPE_PRODUCTABLE_MARKET) {
            $productable = auth()
                ->user()
                ->markets()
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

        $areas = Product::query()
            ->selectRaw(
                'COUNT(addresses.id) AS count,
                addresses.city AS city,
                addresses.state_id AS state_id,
                states.name AS state_name'
            )
            ->join('farms', function($join) {
                $join
                    ->on('products.productable_id', '=', 'farms.id')
                    ->where('products.productable_type', Product::TYPE_PRODUCTABLE_FARM);
            })
            ->leftJoin('addresses', function($join) {
                $join
                    ->on('addresses.addressable_id', '=', 'farms.id')
                    ->where('addresses.addressable_type', Address::TYPE_ADDRESSABLE_FARM);
            })
            ->join('states', function($join) {
                $join->on('states.id', '=', 'addresses.state_id');
            })
            ->where('products.inventory_id', $id)
            ->where('addresses.type', Address::TYPE_LOCATION)
            ->when($country, function($query) use ($country) {
                return $query->whereHas('productable.addresses.state.country', function($query) use ($country) {
                    $query->where('code', $country);
                });
            })
            ->groupBy('city', 'state_id')
            ->orderBy('count', 'desc')
            ->get();

        return BaseResource::collection($areas);
    }
}
