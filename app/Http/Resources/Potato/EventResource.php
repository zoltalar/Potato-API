<?php

declare(strict_types = 1);

namespace App\Http\Resources\Potato;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'website' => $this->website,
            'phone' => $this->phone,
            'email' => $this->when(
                auth()->check() && auth()->id() === $this->eventable->user_id,
                function() {
                    return $this->email;
                }
            ),
            'start_date' => $this->start_date,
            'start_time' => $this->start_time,
            'end_date' => $this->end_date,
            'end_time' => $this->end_time,
            'description' => $this->description,
            'status' => $this->status,
            'addresses' => $this->when(
                $this->relationLoaded('addresses'),
                function() {
                    $addresses = $this->addresses;
                    return BaseResource::collection($addresses);
                }
            ),
            'eventable' => $this->when(
                $this->relationLoaded('eventable'),
                function() {
                    $eventable = $this->eventable;
                    return new BaseResource($eventable);
                }
            ),
            'eventable_type' => $this->eventable_type,
            'eventable_id' => $this->eventable_id
        ];
    }
}
