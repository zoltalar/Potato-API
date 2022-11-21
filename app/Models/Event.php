<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Event extends Base
{
    const TYPE_EVENTABLE_FARM = 'farm';
    const TYPE_EVENTABLE_MARKET = 'market';

    const STATUS_DRAFT = 1;
    const STATUS_AWAITING_APPROVAL = 2;
    const STATUS_APPROVED = 3;
    const STATUS_DECLINED = 4;

    protected $fillable = [
        'title',
        'type',
        'website',
        'phone',
        'email',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'description',
        'status'
    ];

    protected $casts = [
        'type' => 'integer',
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'status' => 'integer'
    ];

    // --------------------------------------------------
    // Scopes
    // --------------------------------------------------

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function eventable(): MorphTo
    {
        return $this->morphTo();
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public static function eventableTypes(): array
    {
        return [
            self::TYPE_EVENTABLE_FARM => __('phrases.farm'),
            self::TYPE_EVENTABLE_MARKET => __('phrases.market')
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => __('phrases.draft'),
            self::STATUS_AWAITING_APPROVAL => __('phrases.awaiting_approval'),
            self::STATUS_APPROVED => __('phrases.approved'),
            self::STATUS_DECLINED => __('phrases.declined')
        ];
    }
}
