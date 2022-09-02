<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\MarketStoreRequest;
use App\Http\Resources\Potato\MarketResource;
use App\Models\Market;

class MarketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato']);
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
}
