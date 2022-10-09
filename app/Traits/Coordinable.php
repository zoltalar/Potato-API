<?php

declare(strict_types = 1);

namespace App\Traits;

use App\Models\Unit;
use App\Services\Haversine;
use Illuminate\Database\Eloquent\Builder;

trait Coordinable
{
    public function scopeHaversine(Builder $query, float $latitude, float $longitude, string $unit = Unit::ABBREVIATION_KILOMETER): Builder
    {
        $sql = sprintf('%s AS `distance`', Haversine::sql());
        $radius = Haversine::radius($unit);

        return $query->selectRaw($sql, [$radius, $latitude, $longitude, $latitude]);
    }
}
