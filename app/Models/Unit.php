<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Unit extends Base
{
    const ABBREVIATION_KILOMETER = 'km';
    const ABBREVIATION_KILOGRAM = 'kg';
    const ABBREVIATION_GRAM = 'g';
    const ABBREVIATION_MILIGRAM = 'mg';
    const ABBREVIATION_LITER = 'l';
    const ABBREVIATION_MILE = 'mi';
    const ABBREVIATION_POUND = 'lb';
    const ABBREVIATION_GALLON = 'gal';
    const ABBREVIATION_QUANTITY = 'item';

    const TYPE_LENGTH = 1;
    const TYPE_VOLUME = 2;
    const TYPE_AREA = 3;
    const TYPE_WEIGHT = 4;
    const TYPE_QUANTITY = 5;

    const SYSTEM_METRIC = 1;
    const SYSTEM_IMPERIAL = 2;

    protected $fillable = [
        'abbreviation',
        'name',
        'type',
        'system'
    ];

    protected $casts = [
        'type' => 'integer',
        'system' => 'integer'
    ];

    public $timestamps = false;

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------
    
    public static function abbreviation(string $code, int $type): string
    {
        $abbreviation = Unit::ABBREVIATION_KILOMETER;

        $country = Country::query()
            ->with(['units'])
            ->where('code', $code)
            ->first();

        if ($country !== null) {
            $unit = $country
                ->units
                ->filter(function($unit) use ($type) {
                    return $unit->type === $type;
                })
                ->first();

            if ($unit !== null) {
                $abbreviation = $unit->abbreviation;
            }
        }

        return $abbreviation;
    }

    public static function types(): array
    {
        return [
            self::TYPE_LENGTH => __('phrases.length'),
            self::TYPE_VOLUME => __('phrases.volume'),
            self::TYPE_AREA => __('phrases.area'),
            self::TYPE_WEIGHT => __('phrases.weight'),
            self::TYPE_QUANTITY => __('phrases.quantity')
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
