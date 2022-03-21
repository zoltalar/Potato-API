<?php

declare(strict_types = 1);

namespace App\Models;

final class Unit extends Base
{
    const ABBREVIATION_KILOMETER = 'km';
    const ABBREVIATION_KILOGRAM = 'kg';
    const ABBREVIATION_LITER = 'l';
    const ABBREVIATION_MILE = 'mi';
    const ABBREVIATION_POUND = 'lb';
    const ABBREVIATION_GALLON = 'gal';

    const TYPE_LENGTH = 1;
    const TYPE_VOLUME = 2;
    const TYPE_AREA = 3;
    const TYPE_WEIGHT = 4;

    const SYSTEM_METRIC = 1;
    const SYSTEM_IMPERIAL = 2;

    public $timestamps = false;

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public static function types(): array
    {
        return [
            self::TYPE_LENGTH => __('phrases.length'),
            self::TYPE_VOLUME => __('phrases.volume'),
            self::TYPE_AREA => __('phrases.area'),
            self::TYPE_WEIGHT => __('phrases.weight')
        ];
    }

    public static function systems(): array
    {
        return [
            self::SYSTEM_METRIC => __('phrases.metric'),
            self::SYSTEM_IMPERIAL => __('phrases.imperial')
        ];
    }
}
