<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\ProductsRequest;
use App\Http\Requests\Potato\ProductStoreRequest;
use App\Http\Requests\Potato\ProductUpdateRequest;
use App\Http\Resources\BaseResource;
use App\Models\Address;
use App\Models\Product;
use App\Models\Unit;
use App\Services\Haversine;
use App\Services\Parameter\CountryHeader;
use App\Services\Response\GrowingArea;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato'])->except([
            'growingArea',
            'topGrowingAreas',
            'topSellingAreas'
        ]);
    }
    
    public function store(ProductStoreRequest $request, string $type, int $id)
    {
        $product = null;
        $productable = null;

        if ($type === Product::TYPE_PRODUCTABLE_FARM) {
            $productable = auth()
                ->user()
                ->farms()
                ->find($id);
        } elseif ($type === Product::TYPE_PRODUCTABLE_MARKET) {
            $productable = auth()
                ->user()
                ->markets()
                ->find($id);
        }
        
        if ($productable !== null) {
            $product = new Product();
            $product->fill($request->only($product->getFillable()));
            
            $productable->products()->save($product);
        }
        
        return new BaseResource($product);
    }
    
    public function update(ProductUpdateRequest $request, int $id, string $type, int $productableId)
    {
        $product = null;
        $productable = null;
        
        if ($type === Product::TYPE_PRODUCTABLE_FARM) {
            $productable = auth()
                ->user()
                ->farms()
                ->with(['products'])
                ->find($productableId);
        } elseif ($type === Product::TYPE_PRODUCTABLE_MARKET) {
            $productable = auth()
                ->user()
                ->markets()
                ->with(['products'])
                ->find($productableId);
        }
        
        if ($productable !== null) {
            
            if ($productable->products->contains('id', $id)) {
                $product = $productable
                    ->products
                    ->filter(function($product) use ($id) {
                        return $product->getKey() === $id;
                    })
                    ->first();
                    
                if ($product !== null) {
                    $product->fill($request->only($product->getFillable()));
                    $product->update();
                }
            }
        }
        
        return new BaseResource($product);
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
    
    public function destroy(int $id, string $type, int $productableId)
    {
        $status = 403;
        $productable = null;
        
        if ($type === Product::TYPE_PRODUCTABLE_FARM) {
            $productable = auth()
                ->user()
                ->farms()
                ->with(['products'])
                ->find($productableId);
        } elseif ($type === Product::TYPE_PRODUCTABLE_MARKET) {
            $productable = auth()
                ->user()
                ->markets()
                ->with(['products'])
                ->find($productableId);
        }
        
        if ($productable !== null) {
            $product = $productable
                ->products
                ->filter(function($product) use ($id) {
                    return $product->getKey() === $id;
                })
                ->first();
                
            if ($product !== null) {
                
                if ($product->delete()) {
                    $status = 204;
                }
            }
        }
        
        return response()->json(null, $status);
    }

    public function growingArea(Request $request, float $latitude, float $longitude)
    {
        $country = (new CountryHeader())->get();
        $abbreviation = Unit::abbreviation($country, Unit::TYPE_LENGTH);
        $radius = Address::radius($abbreviation);

        $products = Product::query()
            ->select([
                'products.id',
                'products.inventory_id'
            ])
            ->with([
                'inventory.category.translations.language',
                'inventory.translations.language'
            ])
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
            ->season()
            ->where('addresses.type', Address::TYPE_LOCATION)
            ->where(function($query) use ($latitude, $longitude, $abbreviation, $radius) {
                $query->whereRaw(Haversine::sql() . ' < ' . $radius, [
                    Haversine::radius($abbreviation),
                    $latitude,
                    $longitude,
                    $latitude
                ]);
            })
            ->where('productable_type', Product::TYPE_PRODUCTABLE_FARM)
            ->get();

        $area = new GrowingArea();
        $area->setCollection($products);
        $area->setRequest($request);

        return response()->json($area->json());
    }

    public function topGrowingAreas(Request $request, int $id)
    {
        $country = (new CountryHeader())->get();

        $areas = Product::query()
            ->selectRaw(
                'COUNT(addresses.id) AS count,
                addresses.city AS city,
                addresses.city_id,
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
            ->join('countries', function($join) {
                $join->on('countries.id', '=', 'states.country_id');
            })
            ->where('products.productable_type', Product::TYPE_PRODUCTABLE_FARM)
            ->where('products.inventory_id', $id)
            ->where('addresses.type', Address::TYPE_LOCATION)
            ->when($country, function($query) use ($country) {
                return $query->where('countries.code', $country);
            })
            ->groupBy('city', 'city_id', 'state_id')
            ->orderBy('count', 'desc')
            ->get();

        return BaseResource::collection($areas);
    }

    public function topSellingAreas(Request $request, int $id)
    {
        $country = (new CountryHeader())->get();

        $areas = Product::query()
            ->selectRaw(
                'COUNT(addresses.id) AS count,
                addresses.city AS city,
                addresses.city_id,
                addresses.state_id AS state_id,
                states.name AS state_name'
            )
            ->join('markets', function($join) {
                $join
                    ->on('products.productable_id', '=', 'markets.id')
                    ->where('products.productable_type', Product::TYPE_PRODUCTABLE_MARKET);
            })
            ->leftJoin('addresses', function($join) {
                $join
                    ->on('addresses.addressable_id', '=', 'markets.id')
                    ->where('addresses.addressable_type', Address::TYPE_ADDRESSABLE_MARKET);
            })
            ->join('states', function($join) {
                $join->on('states.id', '=', 'addresses.state_id');
            })
            ->join('countries', function($join) {
                $join->on('countries.id', '=', 'states.country_id');
            })
            ->where('products.productable_type', Product::TYPE_PRODUCTABLE_MARKET)
            ->where('products.inventory_id', $id)
            ->where('addresses.type', Address::TYPE_LOCATION)
            ->when($country, function($query) use ($country) {
                return $query->where('countries.code', $country);
            })
            ->groupBy('city', 'city_id', 'state_id')
            ->orderBy('count', 'desc')
            ->get();

        return BaseResource::collection($areas);
    }
}
