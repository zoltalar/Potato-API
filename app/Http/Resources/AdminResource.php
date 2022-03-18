<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray($request)
    {
        $resource = $this->resource;

        if ($resource !== null) {
            return $resource->toArray();
        }

        return [];
    }
}
