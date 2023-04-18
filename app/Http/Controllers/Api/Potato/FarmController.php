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
use App\Models\Farm;
use App\Models\Inventory;
use App\Models\Unit;
use App\Services\Request\CountryRequestHeader;
use App\Services\Request\FarmsSearchRequest;
use App\Services\Request\LanguageRequestHeader;
use App\Services\Request\LimitRequestVar;
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
        $country = (new CountryRequestHeader())->get();
        $limit = (new LimitRequestVar())->get();
        $promote = $request->promote;

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

    public function show(int $id)
    {
        $language = (new LanguageRequestHeader())->get();

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
        $code = (new CountryRequestHeader())->get();
        $abbreviation = Unit::abbreviation($code, Unit::TYPE_LENGTH);
        $limit = (new LimitRequestVar())->get();

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

    public function browse(float $latitude, float $longitude)
    {
        $code = (new CountryRequestHeader())->get();
        $abbreviation = Unit::abbreviation($code, Unit::TYPE_LENGTH);
        $limit = (new LimitRequestVar())->get();

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

    public function search()
    {
        $farms = (new FarmsSearchRequest())->get();

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
