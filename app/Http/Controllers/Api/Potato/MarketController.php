<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\MarketContactInformationUpdateRequest;
use App\Http\Requests\Potato\MarketDescriptionUpdateRequest;
use App\Http\Requests\Potato\MarketSocialMediaUpdateRequest;
use App\Http\Requests\Potato\MarketStoreRequest;
use App\Http\Resources\Potato\MarketResource;
use App\Models\Language;
use App\Models\Market;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato'])->except(['show']);
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
