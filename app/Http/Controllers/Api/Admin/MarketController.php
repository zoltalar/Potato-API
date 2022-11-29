<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FarmUpdateRequest;
use App\Http\Requests\Admin\MarketUpdateRequest;
use App\Http\Resources\BaseResource;
use App\Models\Farm;
use App\Models\Market;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = Market::query()
            ->with([
                'addresses',
                'addresses.state',
                'addresses.state.country',
                'images' => function($query) {
                    $query
                        ->orderBy('primary', 'desc')
                        ->orderBy('cover', 'desc');
                },
                'user'
            ])
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query->search([
                        'name',
                        'first_name',
                        'last_name',
                        'phone',
                        'fax',
                        'email',
                        'website',
                        'facebook',
                        'twitter',
                        'pinterest',
                        'instagram'
                    ], $search);
                });
            })
            ->orders('id', 'desc');

        $markets = $query->paginate($limit);

        return BaseResource::collection($markets);
    }

    public function update(MarketUpdateRequest $request, Market $market)
    {
        $market->fill($request->only($market->getFillable()));
        $market->update();

        return new BaseResource($market);
    }

    public function activate(Market $market)
    {
        $market->active = 1;
        $market->update();

        return new BaseResource($market);
    }
}
