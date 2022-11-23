<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\EventDescriptionUpdateRequest;
use App\Http\Requests\Potato\EventGeneralInformationUpdateRequest;
use App\Http\Requests\Potato\EventStoreRequest;
use App\Http\Resources\BaseResource;
use App\Models\Event;

class EventController extends Controller
{
    public function __construct()
    {
        $this
            ->middleware(['auth:user', 'scope:potato'])
            ->only([
                'store',
                'updateGeneralInformation',
                'updateDescription'
            ]);
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
                'eventable' => function($query) {
                    $query->select([
                        'id',
                        'name',
                        'user_id'
                    ]);
                }
            ])
            ->findOrFail($id);

        return new BaseResource($event);
    }

    public function updateGeneralInformation(EventGeneralInformationUpdateRequest $request, int $id)
    {
        $event = Event::query()
            ->whereHas('eventable', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->find($id);

        if ($event !== null) {
            $event->fill($request->only($event->getFillable()));
            $event->update();
        }

        return new BaseResource($event);
    }

    public function updateDescription(EventDescriptionUpdateRequest $request, int $id)
    {
        $event = Event::query()
            ->whereHas('eventable', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->find($id);

        if ($event !== null) {
            $event->update(['description' => $request->description]);
        }

        return new BaseResource($event);
    }

    public function meta()
    {
        return response()->json(['statuses' => Event::statuses()]);
    }
}
