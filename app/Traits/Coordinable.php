<?php

declare(strict_types = 1);

namespace App\Traits;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;

trait Coordinable
{
    public function scopeHaversine(Builder $query, float $latitude, float $longitude, string $unit = Unit::ABBREVIATION_KILOMETER): Builder
    {
        switch ($unit) {
            default:
            case Unit::ABBREVIATION_KILOMETER:
                $radius = 6371;
                break;
            case Unit::ABBREVIATION_MILE:
                $radius = 3959;
                break;
        }

        $sql = '? * ACOS(COS(RADIANS(?)) * COS(RADIANS(`latitude`)) * COS(RADIANS(`longitude`) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(`latitude`))) AS `distance`';

        return $query->selectRaw($sql, [$radius, $latitude, $longitude, $latitude]);
    }
}
