<?php

declare(strict_types = 1);

namespace App\Http\Resources\Potato;

use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'favoriteable_id' => $this->favoriteable_id,
            'favoriteable_type' => $this->favoriteable_type,
            'favoriteable' => $this->when(
                $this->relationLoaded('favoriteable'),
                function() {
                    return new FarmResource($this->favoriteable);
                }
            )
        ];
    }
}
