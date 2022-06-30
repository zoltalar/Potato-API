<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Unit as UnitModel;

final class Unit
{
    public static function unitAbbreviation($code)
    {
        $abbreviation = UnitModel::ABBREVIATION_KILOMETER;

        $country = Country::query()
            ->with(['units'])
            ->where('code', $code)
            ->first();

        if ($country !== null) {
            $unit = $country
                ->units
                ->filter(function($unit) {
                    return $unit->type === UnitModel::TYPE_LENGTH;
                })
                ->first();

            if ($unit !== null) {
                $abbreviation = $unit->abbreviation;
            }
        }

        return $abbreviation;
    }
}
