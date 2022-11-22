<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\EventStoreRequest;
use App\Http\Resources\BaseResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user', 'scope:potato'])->only(['store']);
    }

    public function store(EventStoreRequest $request)
    {
        $event = null;
        $eventable = null;

        $id = $request->eventable_id;
        $type = $request->eventable_type;

        if ($type === Event::TYPE_EVENTABLE_FARM) {
            $eventable = auth()
                ->user()
                ->farms()
                ->find($id);
        } else if ($type === Event::TYPE_EVENTABLE_MARKET) {
            $eventable = auth()
                ->user()
                ->markets()
                ->find($id);
        }

        if ($eventable !== null) {
            $event = new Event();
            $event->fill($request->only($event->getFillable()));
            $event->status = Event::STATUS_DRAFT;

            $eventable->events()->save($event);
        }

        return new BaseResource($event);
    }

    public function show(int $id)
    {
        $event = Event::query()
            ->with([
                'addresses',
                'addresses.state.country',
                'images' => function($query) {
                    $query
                        ->orderBy('primary', 'desc')
                        ->orderBy('cover', 'desc')
                        ->orderBy('id', 'asc');
                },
            ])
            ->findOrFail($id);

        return new BaseResource($event);
    }

    public function meta()
    {
        return response()->json(['statuses' => Event::statuses()]);
    }
}
