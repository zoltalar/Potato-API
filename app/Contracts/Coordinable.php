<?php

declare(strict_types = 1);

namespace App\Contracts;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;

interface Coordinable
{
    public function scopeHaversine(Builder $query, float $latitude, float $longitude, string $unit = Unit::ABBREVIATION_KILOMETER): Builder;
}
