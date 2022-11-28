<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\EventDescriptionUpdateRequest;
use App\Http\Requests\Potato\EventGeneralInformationUpdateRequest;
use App\Http\Requests\Potato\EventStoreRequest;
use App\Http\Resources\Potato\EventResource;
use App\Models\Event;
use Exception;

class EventController extends Controller
{
    public function __construct()
    {
        $this
            ->middleware(['auth:user', 'scope:potato'])
            ->only([
                'store',
                'updateGeneralInformation',
                'updateDescription',
                'destroy'
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

        return new EventResource($event);
    }

    public function show(int $id)
    {
        $event = Event::query()
            ->with([
                'addresses',
                'addresses.state.country',
                'eventable' => function($query) {
                    $query->select([
                        'id',
                        'name',
                        'user_id'
                    ]);
                }
            ])
            ->findOrFail($id);

        return new EventResource($event);
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

            if ($event->status === Event::STATUS_DRAFT) {
                $event->status = Event::STATUS_AWAITING_APPROVAL;
            }

            $event->update();
        }

        return new EventResource($event);
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

        return new EventResource($event);
    }

    public function destroy(int $id)
    {
        $status = 403;

        $event = Event::query()
            ->whereHas('eventable', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->find($id);

        if ($event !== null) {

            try {
                if ($event->delete()) {
                    $status = 204;
                }
            } catch (Exception $e) {}
        }

        return response()->json(null, $status);
    }

    public function meta()
    {
        return response()->json(['statuses' => Event::statuses()]);
    }
}
