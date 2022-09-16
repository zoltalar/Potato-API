<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Http\FormRequest;

class OperatingHour extends Base
{
    const TYPE_OPERATABLE_FARM = 'farm';
    const TYPE_OPERATABLE_MARKET = 'market';

    const TYPE_DATES = 1;
    const TYPE_MONTHS = 2;

    protected $fillable = [
        'type',
        'start_date',
        'end_date',
        'start_month',
        'end_month',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
        'exceptions'
    ];

    protected $casts = [
        'monday' => 'array',
        'tuesday' => 'array',
        'wednesday' => 'array',
        'thursday' => 'array',
        'friday' => 'array',
        'saturday' => 'array',
        'sunday' => 'array'
    ];

    // --------------------------------------------------
    // Accessors and Mutators
    // --------------------------------------------------

    public function setExceptionsAttribute($value): void
    {
        if ( ! empty($value)) {
            $value = strip_tags($value);
        }

        $this->attributes['exceptions'] = $value;
    }

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function operatable(): MorphTo
    {
        return $this->morphTo();
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public static function days(): array
    {
        return [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday'
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_DATES => __('phrases.date_based'),
            self::TYPE_MONTHS => __('phrases.month_based')
        ];
    }

    public function fillFromRequest(FormRequest $request): OperatingHour
    {
        foreach ($this->days() as $day) {
            $data = $request->{$day} ?? [];
            $this->{$day} = [];

            if (is_array($data)) {
                $selected = $data['selected'] ?? false;

                if ($selected) {
                    $start = $data['start'] ?? null;
                    $end = $data['end'] ?? null;

                    if ($start && $end) {
                        $this->{$day} = [implode('-', [$start, $end])];
                    }
                }
            }
        }

        return $this;
    }
}
