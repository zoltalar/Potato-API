<?php

declare(strict_types = 1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

final class AddressCoordinatesResolver
{
    public function resolve(string $address): array
    {
        $location = Http::get($this->url($address))->json('results.0.geometry.location');

        if ($location !== null) {
            return [$location['lat'], $location['lng']];
        }

        return [null, null];
    }

    protected function key(): string
    {
        return config('services.google.key');
    }

    protected function url(string $address): string
    {
        return sprintf(
            'https://maps.google.com/maps/api/geocode/json?address=%s&key=%s',
            urlencode($address),
            urlencode($this->key())
        );
    }
}
