<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\MarketContactInformationUpdateRequest;
use App\Http\Requests\Potato\MarketDeactivateRequest;
use App\Http\Requests\Potato\MarketDescriptionUpdateRequest;
use App\Http\Requests\Potato\MarketSocialMediaUpdateRequest;
use App\Http\Requests\Potato\MarketStoreRequest;
use App\Http\Resources\Potato\MarketResource;
use App\Jobs\SendMarketDeactivationNotificationJob;
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
        $this->middleware(['auth:user', 'scope:potato'])->except([
            'index',
            'show',
            'locate',
            'browse',
            'search'
        ]);
    }

    public function index(Request $request)
    {
        $country = $request->header('X-country', Country::CODE_PL);
        $limit = $request->get('limit', 10);
        $promote = $request->promote;

        if ($limit > 10) {
            $limit = 10;
        }

        $farms = Market::query()
            ->with([
                'images' => function($query) {
                    $query->primary();
                }
            ])
            ->active()
            ->whereHas('addresses.state.country', function($query) use ($country) {
                $query->where('code', $country);
            })
            ->when($promote, function($query) {
                return $query->promote();
            })
            ->take($limit)
            ->get()
            ->shuffle();

        return MarketResource::collection($farms);
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
                'events' => function($query) {
                    $query
                        ->future()
                        ->approved()
                        ->orderBy('start_date')
                        ->orderBy('end_date');
                },
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
                'reviews.comments' => function($query) {
                    $query->orderBy('created_at');
                },
                'reviews.comments.user' => function($query) {
                    $query->select([
                        'id',
                        'first_name',
                        'last_name'
                    ]);
                },
                'reviews.user' => function($query) {
                    $query->select([
                        'id',
                        'first_name',
                        'last_name'
                    ]);
                }
            ])
            ->findOrFail($id);

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

    public function locate(Request $request, float $latitude, float $longitude)
    {
        $code = $request->header('X-country', Country::CODE_PL);
        $abbreviation = Unit::unitAbbreviation($code, Unit::TYPE_LENGTH);
        $limit = $request->get('limit', 10);

        if ($limit > 10) {
            $limit = 10;
        }

        $markets = Market::query()
            ->with([
                'addresses' => function($query) use ($latitude, $longitude, $abbreviation) {
                    $query->select();
                    $query->haversine($latitude, $longitude, $abbreviation);
                    $query->where('type', Address::TYPE_LOCATION);
                },
                'images' => function($query) {
                    $query->primary();
                }
            ])
            ->active()
            ->whereHas('addresses', function($query) use ($latitude, $longitude, $abbreviation) {
                $query
                    ->haversine($latitude, $longitude, $abbreviation)
                    ->where('type', Address::TYPE_LOCATION)
                    ->havingRaw('distance < ?', [Address::radius($abbreviation)]);
            })
            ->orderBy('promote', 'desc')
            ->take($limit)
            ->get()
            ->shuffle();

        return MarketResource::collection($markets);
    }

    public function browse(Request $request, float $latitude, float $longitude)
    {
        $code = $request->header('X-country', Country::CODE_PL);
        $abbreviation = Unit::unitAbbreviation($code, Unit::TYPE_LENGTH);
        $limit = $request->get('limit', 10);

        if ($limit > 10) {
            $limit = 10;
        }

        $markets = Market::query()
            ->with([
                'addresses' => function($query) use ($latitude, $longitude, $abbreviation) {
                    $query->select();
                    $query->haversine($latitude, $longitude, $abbreviation);
                    $query->where('type', Address::TYPE_LOCATION);
                },
                'addresses.state.country',
                'images' => function($query) {
                    $query->primary();
                }
            ])
            ->active()
            ->whereHas('addresses', function($query) use ($latitude, $longitude, $abbreviation) {
                $query
                    ->haversine($latitude, $longitude, $abbreviation)
                    ->where('type', Address::TYPE_LOCATION)
                    ->havingRaw('distance < ?', [Address::radius($abbreviation)]);
            })
            ->orderBy('promote', 'desc')
            ->paginate($limit);

        return MarketResource::collection($markets);
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
            } else {
                // Inventory item not found so bail out
                return MarketResource::collection(collect([]));
            }
        }

        $location = $request->location;
        $city = null;
        $cityId = $request->get('city_id', 0);
        $countryCode = $request->header('X-country', Country::CODE_PL);
        $abbreviation = Unit::unitAbbreviation($countryCode, Unit::TYPE_LENGTH);
        $radius = Address::radius($abbreviation, (int) $request->radius);
        $limit = $request->get('limit', 10);

        if ($limit > 10) {
            $limit = 10;
        }

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
                ->paginate($limit);
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
                ->paginate($limit);
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

    public function deactivate(MarketDeactivateRequest $request, int $id)
    {
        $market = auth()
            ->user()
            ->markets()
            ->active()
            ->find($id);

        if ($market !== null) {
            $market->fill($request->only($market->getFillable()));
            $market->active = 0;
            $market->deactivated_at = $market->freshTimestamp();

            if ($market->update()) {
                $this->dispatch(new SendMarketDeactivationNotificationJob($market));
            }
        }

        return new MarketResource($market);
    }
}
