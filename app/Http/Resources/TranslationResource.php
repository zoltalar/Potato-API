<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TranslationResource extends JsonResource
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
