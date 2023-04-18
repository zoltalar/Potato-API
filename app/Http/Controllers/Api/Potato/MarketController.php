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
use App\Models\Market;
use App\Models\Unit;
use App\Services\Parameter\CountryHeader;
use App\Services\Parameter\LanguageHeader;
use App\Services\Parameter\LimitVar;
use App\Services\Search\MarketsSearch;
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
        $country = (new CountryHeader())->get();
        $limit = (new LimitVar())->get();
        $promote = $request->promote;

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

    public function show(int $id)
    {
        $language = (new LanguageHeader())->get();

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

    public function locate(float $latitude, float $longitude)
    {
        $code = (new CountryHeader())->get();
        $abbreviation = Unit::abbreviation($code, Unit::TYPE_LENGTH);
        $limit = (new LimitVar())->get();

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

    public function browse(float $latitude, float $longitude)
    {
        $code = (new CountryHeader())->get();
        $abbreviation = Unit::abbreviation($code, Unit::TYPE_LENGTH);
        $limit = (new LimitVar())->get();

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

    public function search()
    {        
        $markets = (new MarketsSearch())->results();

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
