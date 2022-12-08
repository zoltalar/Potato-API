<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\FarmContactInformationUpdateRequest;
use App\Http\Requests\Potato\FarmDeactivateRequest;
use App\Http\Requests\Potato\FarmDescriptionUpdateRequest;
use App\Http\Requests\Potato\FarmSocialMediaUpdateRequest;
use App\Http\Requests\Potato\FarmStoreRequest;
use App\Http\Resources\Potato\FarmResource;
use App\Jobs\SendFarmDeactivationNotificationJob;
use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\Farm;
use App\Models\Inventory;
use App\Models\Language;
use App\Models\Unit;
use Illuminate\Http\Request;

class FarmController extends Controller
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

        $farms = Farm::query()
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

        return FarmResource::collection($farms);
    }

    public function store(FarmStoreRequest $request)
    {
        $farm = new Farm();
        $farm->fill($request->only($farm->getFillable()));
        $farm->active = 1;

        auth()
            ->user()
            ->farms()
            ->save($farm);

        return new FarmResource($farm);
    }

    public function show(Request $request, int $id)
    {
        $language = $request->header('X-language', Language::CODE_PL);

        $farm = Farm::query()
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
                'reviews.comments.user',
                'reviews.user'
            ])
            ->findOrFail($id);

        if ($farm !== null) {

            if (auth()->check()) {
                $farm->load([
                    'favorites' => function($query) {
                        $query->where('user_id', auth()->id());
                    }
                ]);
            }
        }

        return new FarmResource($farm);
    }

    public function locate(Request $request, float $latitude, float $longitude)
    {
        $code = $request->header('X-country', Country::CODE_PL);
        $abbreviation = Unit::unitAbbreviation($code, Unit::TYPE_LENGTH);
        $limit = $request->get('limit', 10);

        if ($limit > 10) {
            $limit = 10;
        }

        $farms = Farm::query()
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

        return FarmResource::collection($farms);
    }

    public function browse(Request $request, float $latitude, float $longitude)
    {
        $code = $request->header('X-country', Country::CODE_PL);
        $abbreviation = Unit::unitAbbreviation($code, Unit::TYPE_LENGTH);
        $limit = $request->get('limit', 10);

        if ($limit > 10) {
            $limit = 10;
        }

        $farms = Farm::query()
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

        return FarmResource::collection($farms);
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
                return FarmResource::collection(collect([]));
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
            $farms = Farm::query()
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
                    $query
                        ->whereHas('addresses', function($query) use ($city, $abbreviation, $radius) {
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
            $farms = Farm::query()
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

        return FarmResource::collection($farms);
    }

    public function updateContactInformation(FarmContactInformationUpdateRequest $request, int $id)
    {
        $farm = auth()
            ->user()
            ->farms()
            ->find($id);

        if ($farm !== null) {
            $farm->fill($request->only($farm->getFillable()));
            $farm->update();
        }

        return new FarmResource($farm);
    }

    public function updateDescription(FarmDescriptionUpdateRequest $request, int $id)
    {
        $farm = auth()
            ->user()
            ->farms()
            ->find($id);

        if ($farm !== null) {
            $farm->update(['description' => $request->description]);
        }

        return new FarmResource($farm);
    }

    public function updateSocialMedia(FarmSocialMediaUpdateRequest $request, int $id)
    {
        $farm = auth()
            ->user()
            ->farms()
            ->find($id);

        if ($farm !== null) {
            $farm->fill($request->only($farm->getFillable()));
            $farm->update();
        }

        return new FarmResource($farm);
    }

    public function deactivate(FarmDeactivateRequest $request, int $id)
    {
        $farm = auth()
            ->user()
            ->farms()
            ->active()
            ->find($id);

        if ($farm !== null) {
            $farm->fill($request->only($farm->getFillable()));
            $farm->active = 0;
            $farm->deactivated_at = $farm->freshTimestamp();

            if ($farm->update()) {
                $this->dispatch(new SendFarmDeactivationNotificationJob($farm));
            }
        }

        return new FarmResource($farm);
    }
}
