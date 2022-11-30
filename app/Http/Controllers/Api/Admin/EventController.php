<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CityStoreRequest;
use App\Http\Requests\Admin\CityUpdateRequest;
use App\Http\Resources\BaseResource;
use App\Models\City;
use App\Models\Event;
use Exception;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'scope:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $limit = $request->get('limit', 10);

        $query = Event::query()
            ->with([
                'addresses.state.country',
                'eventable' => function($query) {
                    $query->select([
                        'id',
                        'name'
                    ]);
                }
            ])
            ->when($search, function($query) use ($search) {
                return $query->where(function($query) use ($search) {
                    $query
                        ->search(['website', 'phone', 'email', 'description'], $search)
                        ->orWhereHas('eventable', function($query) use ($search) {
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
                });
            })
            ->orders('id', 'desc');

        $events = $query->paginate($limit);

        return BaseResource::collection($events);
    }

    public function meta()
    {
        return response()->json(['statuses' => Event::statuses()]);
    }
}
