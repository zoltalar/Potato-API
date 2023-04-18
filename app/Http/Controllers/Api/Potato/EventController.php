<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Potato;

use App\Http\Controllers\Controller;
use App\Http\Requests\Potato\EventDescriptionUpdateRequest;
use App\Http\Requests\Potato\EventGeneralInformationUpdateRequest;
use App\Http\Requests\Potato\EventStoreRequest;
use App\Http\Resources\Potato\EventResource;
use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\Event;
use App\Models\Unit;
use App\Services\Ics;
use App\Services\Request\CountryRequestHeader;
use App\Services\Request\LimitRequestVar;
use Exception;
use Illuminate\Http\Request;

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

    public function index(Request $request)
    {
        $country = (new CountryRequestHeader())->get();
        $scope = $request->get('scope', Event::SCOPE_FUTURE);
        $limit = (new LimitRequestVar())->get();

        $events = Event::query()
            ->with([
                'addresses',
                'addresses.state.country'
            ])
            ->approved()
            ->whereHas('addresses.state.country', function($query) use ($country) {
                $query->where('code', $country);
            })
            ->when($scope, function($query) use ($scope) {
                if ($scope == Event::SCOPE_FUTURE) {
                    return $query->future();
                } elseif ($scope == Event::SCOPE_PAST) {
                    return $query->past();
                }
            })
            ->orderBy('start_date')
            ->take($limit)
            ->get();

        return EventResource::collection($events);
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

    public function locate(float $latitude, float $longitude)
    {
        $code = (new CountryRequestHeader())->get();
        $abbreviation = Unit::abbreviation($code, Unit::TYPE_LENGTH);
        $limit = (new LimitRequestVar())->get();

        $events = Event::query()
            ->with([
                'addresses' => function($query) use ($latitude, $longitude, $abbreviation) {
                    $query->select();
                    $query->haversine($latitude, $longitude, $abbreviation);
                    $query->where('type', Address::TYPE_LOCATION);
                },
                'addresses.state'
            ])
            ->future()
            ->approved()
            ->whereHas('addresses', function($query) use ($latitude, $longitude, $abbreviation) {
                $query
                    ->haversine($latitude, $longitude, $abbreviation)
                    ->where('type', Address::TYPE_LOCATION)
                    ->havingRaw('distance < ?', [Address::radius($abbreviation)]);
            })
            ->orderBy('start_date', 'asc')
            ->take($limit)
            ->get();

        return EventResource::collection($events);
    }

    public function search(Request $request)
    {
        $scope = (int) $request->scope;
        $keyword = $request->keyword;
        $location = $request->location;
        $city = null;
        $cityId = $request->get('city_id', 0);
        $countryCode = (new CountryRequestHeader())->get();
        $abbreviation = Unit::abbreviation($countryCode, Unit::TYPE_LENGTH);
        $radius = Address::radius($abbreviation, (int) $request->radius);
        $limit = (new LimitRequestVar())->get();

        // Attempt to find the city
        if (empty($cityId) && ! empty($location)) {
            $city = City::query()
                ->search(['name', 'name_ascii'], $location)
                ->whereHas('state.country', function($query) use ($countryCode) {
                    $query->where('code', $countryCode);
                })
                ->first();
        } elseif ( ! empty($cityId)) {
            $city = City::find($cityId);
        }

        // We have the city model
        if ($city !== null) {
            $events = Event::query()
                ->with([
                    'addresses' => function($query) use ($city, $abbreviation) {
                        $query->select();
                        $query->haversine($city->latitude, $city->longitude, $abbreviation);
                        $query->where('type', Address::TYPE_LOCATION);
                    },
                    'addresses.state.country'
                ])
                ->approved()
                ->when($keyword, function($query) use ($keyword) {
                    return $query->where(function($query) use ($keyword) {
                        $query->search(['title', 'description'], $keyword);
                    });
                })
                ->where(function($query) use ($city, $abbreviation, $radius, $location) {
                    $query
                        ->whereHas('addresses', function($query) use ($city, $abbreviation, $radius) {
                            $query
                                ->haversine($city->latitude, $city->longitude, $abbreviation)
                                ->where('type', Address::TYPE_LOCATION)
                                ->havingRaw('distance < ?', [$radius]);
                        })
                        ->orWhereHas('addresses', function($query) use ($location) {
                            $query
                                ->search(['city'], $location)
                                ->where('type', Address::TYPE_LOCATION);
                        });
                })
                ->when($scope, function($query) use ($scope) {
                    if ($scope === Event::SCOPE_FUTURE) {
                        return $query->future();
                    } elseif ($scope === Event::SCOPE_PAST) {
                        return $query->past();
                    }
                })
                ->orderBy('start_date')
                ->paginate($limit);
        } else {
            $events = Event::query()
                ->with([
                    'addresses' => function($query) {
                        $query->where('type', Address::TYPE_LOCATION);
                    },
                    'addresses.state.country'
                ])
                ->approved()
                ->when($keyword, function($query) use ($keyword) {
                    return $query->where(function($query) use ($keyword) {
                        $query->search(['title', 'description'], $keyword);
                    });
                })
                ->where(function($query) use ($location) {
                    $query
                        ->search(['city'], $location)
                        ->where('type', Address::TYPE_LOCATION);
                })
                ->when($scope, function($query) use ($scope) {
                    if ($scope === Event::SCOPE_FUTURE) {
                        return $query->future();
                    } elseif ($scope === Event::SCOPE_PAST) {
                        return $query->past();
                    }
                })
                ->orderBy('start_date')
                ->paginate($limit);
        }

        return EventResource::collection($events);
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

    public function calendar(Event $event)
    {
        $event->load(['addresses']);

        return response()->streamDownload(function() use ($event) {
            echo (new Ics($event));
        });
    }

    public function meta()
    {
        $meta = [
            'statuses' => Event::statuses(),
            'scopes' => Event::scopes()
        ];

        return response()->json($meta);
    }
}
