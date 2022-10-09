<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Unit;

final class Haversine
{
    public static function radius(string $unit = Unit::ABBREVIATION_KILOMETER): int
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

        return $radius;
    }

    public static function sql(): string
    {
        return '? * ACOS(COS(RADIANS(?)) * COS(RADIANS(`latitude`)) * COS(RADIANS(`longitude`) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(`latitude`)))';
    }
}
