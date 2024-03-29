<?php

declare(strict_types = 1);

namespace App\Models;

use App\Contracts\Icsable as IcsableContract;
use App\Contracts\Messageable as MessageableContract;
use App\Traits\Icsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Str;

final class Event extends Base implements
    IcsableContract,
    MessageableContract
{
    use Icsable;

    const TYPE_EVENTABLE_FARM = 'farm';
    const TYPE_EVENTABLE_MARKET = 'market';

    const STATUS_DRAFT = 1;
    const STATUS_AWAITING_APPROVAL = 2;
    const STATUS_APPROVED = 3;
    const STATUS_DECLINED = 4;

    const SCOPE_FUTURE = 1;
    const SCOPE_PAST = 2;
    const SCOPE_ALL = 3;

    protected $fillable = [
        'title',
        'website',
        'phone',
        'email',
        'start_date',
        'end_date',
        'start_time',
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

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeFuture(Builder $query): Builder
    {
        $today = date('Y-m-d');

        return $query->where(function($query) use ($today) {
            $query
                ->whereDate('start_date', '>=', $today)
                ->orWhereDate('end_date', '>=', $today);
        });
    }

    public function scopePast(Builder $query): Builder
    {
        $today = date('Y-m-d');

        return $query->where(function($query) use ($today) {
            $query
                ->whereDate('start_date', '<', $today)
                ->orWhereDate('end_date', '<', $today);
        });
    }

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function eventable(): MorphTo
    {
        return $this->morphTo();
    }

    // --------------------------------------------------
    // Accessors and Mutators
    // --------------------------------------------------

    public function setDescriptionAttribute($value): void
    {
        if ( ! empty($value)) {
            $value = strip_tags($value);
        }

        $this->attributes['description'] = $value;
    }

    public function setPhoneAttribute($value): void
    {
        if ( ! empty($value)) {
            $value = Str::stripNonDigits($value);
        }

        $this->attributes['phone'] = $value;
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public function address(): ?Address
    {
        if ($this->relationLoaded('addresses')) {
            return $this
                ->addresses
                ->filter(function($address) {
                    return $address->type === Address::TYPE_LOCATION;
                })
                ->first();
        }

        return null;
    }

    public function recipient(): ?User
    {
        return $this->eventable->user ?? null;
    }

    public static function eventableTypes(): array
    {
        return [
            self::TYPE_EVENTABLE_FARM => __('phrases.farm'),
            self::TYPE_EVENTABLE_MARKET => __('phrases.market')
        ];
    }

    public static function scopes(): array
    {
        return [
            self::SCOPE_FUTURE => __('phrases.upcoming_events'),
            self::SCOPE_PAST => __('phrases.past_events'),
            self::SCOPE_ALL => __('phrases.all_events')
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
