<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\MarketContactInformationUpdateRequest;
use App\Http\Requests\Potato\MarketDescriptionUpdateRequest;
use App\Http\Requests\Potato\MarketSocialMediaUpdateRequest;
use App\Http\Requests\Potato\MarketStoreRequest;
use App\Http\Resources\Potato\MarketResource;
use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\Inventory;
use App\Models\Language;
use App\Models\Market;
use App\Models\Unit;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato'])->except(['show', 'search']);
    }

    public function store(MarketStoreRequest $request)
    {
        $market = new Market();
        $market->fill($request->only($market->getFillable()));
        $market->active = 1;

        auth()
            ->user()
            ->markets()
            ->save($market);

        return new MarketResource($market);
    }

    public function show(Request $request, int $id)
    {
        $language = $request->header('X-language', Language::CODE_PL);

        $market = Market::query()
            ->with([
                'addresses',
                'addresses.state.country',
                'images' => function($query) {
                    $query
                        ->orderBy('primary', 'desc')
                        ->orderBy('cover', 'desc')
                        ->orderBy('id', 'asc');
                },
                'operatingHours',
                'products.inventory.category.translations' => function($query) use ($language) {
                    $query->when($language, function($query) use ($language) {
                        return $query->whereHas('language', function($query) use ($language) {
                            $query->where('code', $language);
                        });
                    });
                },
                'products.inventory.translations' => function($query) use ($language) {
                    $query->when($language, function($query) use ($language) {
                        return $query->whereHas('language', function($query) use ($language) {
                            $query->where('code', $language);
                        });
                    });
                },
                'reviews' => function($query) {
                    $query
                        ->active()
                        ->orderBy('created_at', 'desc');
                },
                'reviews.user'
            ])
            ->find($id);

        if ($market !== null) {

            if (auth()->check()) {
                $market->load([
                    'favorites' => function($query) {
                        $query->where('user_id', auth()->id());
                    }
                ]);
            }
        }

        return new MarketResource($market);
    }

    public function search(Request $request)
    {
        $item = $request->item;
        $inventoryId = $request->get('inventory_id', 0);

        // Attempt to find the inventory item
        if (empty($inventoryId) && ! empty($item)) {
            $inventory = Inventory::query()
                ->whereHas('translations', function($query) use ($item) {
                    $query->search(['name'], $item);
                })
                ->first();

            if ($inventory !== null) {
                $inventoryId = $inventory->id;
            }
        }

        $location = $request->location;
        $city = null;
        $cityId = $request->get('city_id', 0);
        $countryCode = $request->header('X-country', Country::CODE_PL);
        $abbreviation = Unit::unitAbbreviation($countryCode, Unit::TYPE_LENGTH);
        $radius = Address::radius($abbreviation, (int) $request->radius);

        // Attempt to find the city
        if (empty($cityId) && ! empty($location)) {
            $city = City::query()
                ->search(['name', 'name_ascii'], $location)
                ->whereHas('state.country', function($query) use ($countryCode) {
                    $query->where('code', $countryCode);
                })
                ->first();
        } elseif ( ! empty($cityId)) {
            $city = City::find($cityId);
        }

        // We have the city model
        if ($city !== null) {
            $markets = Market::query()
                ->with([
                    'addresses' => function($query) use ($city, $abbreviation) {
                        $query->select();
                        $query->haversine($city->latitude, $city->longitude, $abbreviation);
                        $query->where('type', Address::TYPE_LOCATION);
                    },
                    'addresses.state.country',
                    'images' => function($query) {
                        $query->primary();
                    },
                    'products.inventory.translations'
                ])
                ->active()
                ->where(function($query) use ($city, $abbreviation, $radius, $location) {
                    $query->whereHas('addresses', function($query) use ($city, $abbreviation, $radius) {
                        $query
                            ->haversine($city->latitude, $city->longitude, $abbreviation)
                            ->where('type', Address::TYPE_LOCATION)
                            ->havingRaw('distance < ?', [$radius]);
                        })
                        ->orWhereHas('addresses', function($query) use ($location) {
                            $query
                                ->search(['city'], $location)
                                ->where('type', Address::TYPE_LOCATION);
                        });
                })
                ->when( ! empty($inventoryId), function($query) use ($inventoryId) {
                    return $query->where(function($query) use ($inventoryId) {
                        $query->whereHas('products', function($query) use ($inventoryId) {
                            $query
                                ->season()
                                ->where('inventory_id', $inventoryId);
                        });
                    });
                })
                ->orderBy('promote', 'desc')
                ->get();
        } else {
            $markets = Market::query()
                ->with([
                    'addresses' => function($query) {
                        $query->where('type', Address::TYPE_LOCATION);
                    },
                    'addresses.state.country',
                    'images' => function($query) {
                        $query->primary();
                    },
                    'products.inventory.translations'
                ])
                ->active()
                ->whereHas('addresses', function($query) use ($location) {
                    $query
                        ->search(['city'], $location)
                        ->where('type', Address::TYPE_LOCATION);
                })
                ->when( ! empty($inventoryId), function($query) use ($inventoryId) {
                    return $query->where(function($query) use ($inventoryId) {
                        $query->whereHas('products', function($query) use ($inventoryId) {
                            $query
                                ->season()
                                ->where('inventory_id', $inventoryId);
                        });
                    });
                })
                ->orderBy('promote', 'desc')
                ->get();
        }

        return MarketResource::collection($markets);
    }

    public function updateContactInformation(MarketContactInformationUpdateRequest $request, int $id)
    {
        $market = auth()
            ->user()
            ->markets()
            ->find($id);

        if ($market !== null) {
            $market->fill($request->only($market->getFillable()));
            $market->update();
        }

        return new MarketResource($market);
    }

    public function updateDescription(MarketDescriptionUpdateRequest $request, int $id)
    {
        $market = auth()
            ->user()
            ->markets()
            ->find($id);

        if ($market !== null) {
            $market->update(['description' => $request->description]);
        }

        return new MarketResource($market);
    }

    public function updateSocialMedia(MarketSocialMediaUpdateRequest $request, int $id)
    {
        $market = auth()
            ->user()
            ->markets()
            ->find($id);

        if ($market !== null) {
            $market->fill($request->only($market->getFillable()));
            $market->update();
        }

        return new MarketResource($market);
    }
}
