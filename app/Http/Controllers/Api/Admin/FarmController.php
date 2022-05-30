<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FarmUpdateRequest;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Resources\FarmResource;
use App\Http\Resources\UserResource;
use App\Models\Farm;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = Farm::query()
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

        $farms = $query->paginate($limit);

        return FarmResource::collection($farms);
    }

    public function update(FarmUpdateRequest $request, Farm $farm)
    {
        $farm->fill($request->only($farm->getFillable()));
        $farm->update();

        return new FarmResource($farm);
    }

    public function activate(Farm $farm)
    {
        $farm->active = 1;
        $farm->deactivation_reason = null;
        $farm->deactivated_at = null;
        $farm->update();

        return new FarmResource($farm);
    }
}
