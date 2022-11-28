<?php

declare(strict_types = 1);

namespace App\Models;

use App\Contracts\Messageable as MessageableContract;
use App\Contracts\Namable as NamableContract;
use App\Traits\Namable;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Notifications\Notifiable;
use Str;

final class Farm extends Base implements
    NamableContract,
    HasLocalePreference,
    MessageableContract
{
    use Namable,
        Notifiable;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'phone',
        'publish_phone',
        'publish_address',
        'publish_mailing_address',
        'fax',
        'email',
        'website',
        'description',
        'facebook',
        'twitter',
        'pinterest',
        'instagram',
        'shipping_pickup',
        'shipping_delivery',
        'shipping_delivery_radius',
        'promote',
        'active',
        'deactivation_reason',
        'deactivated_at',
        'user_id',
    ];

    protected $casts = [
        'publish_phone' => 'integer',
        'publish_address' => 'integer',
        'publish_mailing_address' => 'integer',
        'shipping_pickup' => 'integer',
        'shipping_delivery' => 'integer',
        'shipping_delivery_radius' => 'integer',
        'promote' => 'integer',
        'active' => 'integer',
        'deactivated_at' => 'datetime'
    ];

    protected $appends = [
        'average_rating',
        'reviews_count',
        'facebook_url',
        'twitter_url',
        'pinterest_url',
        'instagram_url'
    ];

    // --------------------------------------------------
    // Scopes
    // --------------------------------------------------

    public function scopePromote(Builder $query): Builder
    {
        return $query->where('promote', 1);
    }

    public function scopePublishAddress(Builder $query): Builder
    {
        return $query->where('publish_address', 1);
    }

    public function scopePublishMailingAddress(Builder $query): Builder
    {
        return $query->where('publish_mailing_address', 1);
    }

    // --------------------------------------------------
    // Accessors and Mutators
    // --------------------------------------------------

    public function getAverageRatingAttribute($value): float
    {
        return $this->averageRating();
    }

    public function getReviewsCountAttribute($value): int
    {
        return $this->reviewsCount();
    }

    public function getFacebookUrlAttribute($value): ?string
    {
        $facebook = $this->attributes['facebook'] ?? null;

        if ( ! empty($facebook)) {
            return sprintf('https://facebook.com/%s', $facebook);
        }

        return null;
    }

    public function getTwitterUrlAttribute($value): ?string
    {
        $twitter = $this->attributes['twitter'] ?? null;

        if ( ! empty($twitter)) {
            return sprintf('https://twitter.com/%s', $twitter);
        }

        return null;
    }

    public function getPinterestUrlAttribute($value): ?string
    {
        $pinterest = $this->attributes['pinterest'] ?? null;

        if ( ! empty($pinterest)) {
            return sprintf('https://pinterest.com/%s', $pinterest);
        }

        return null;
    }

    public function getInstagramUrlAttribute($value): ?string
    {
        $instagram = $this->attributes['instagram'] ?? null;

        if ( ! empty($instagram)) {
            return sprintf('https://instagram.com/%s', $instagram);
        }

        return null;
    }

    public function setDescriptionAttribute($value): void
    {
        if ( ! empty($value)) {
            $value = strip_tags($value);
        }

        $this->attributes['description'] = $value;
    }

    public function setFaxAttribute($value): void
    {
        if ( ! empty($value)) {
            $value = Str::stripNonDigits($value);
        }

        $this->attributes['fax'] = $value;
    }

    public function setPhoneAttribute($value): void
    {
        if ( ! empty($value)) {
            $value = Str::stripNonDigits($value);
        }

        $this->attributes['phone'] = $value;
    }

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function events(): MorphMany
    {
        return $this->morphMany(Event::class, 'eventable');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function operatingHours(): MorphOne
    {
        return $this->morphOne(OperatingHour::class, 'operatable');
    }

    public function products(): MorphMany
    {
        return $this->morphMany(Product::class, 'productable');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'rateable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public function preferredLocale(): string
    {
        $this->load(['user.language']);
        $code = $this->user->language->code;

        if (empty($code) || ! in_array($code, Language::codes())) {
            $code = Language::CODE_PL;
        }

        return $code;
    }

    public function recipient(): ?User
    {
        return $this->user ?? null;
    }

    public function averageRating(): float
    {
        return (float) $this
            ->reviews()
            ->active()
            ->average('rating');
    }

    public function favorite(User $user): ?object
    {
        return $this
            ->favorites()
            ->where('user_id', $user->id)
            ->first();
    }

    public function publishAddress(): bool
    {
        return $this->publish_address == 1;
    }

    public function publishMailingAddress(): bool
    {
        return $this->publish_mailing_address == 1;
    }

    public function publishPhone(): bool
    {
        return $this->publish_phone == 1;
    }

    public function review(User $user): ?object
    {
        return $this
            ->reviews()
            ->where('user_id', $user->id)
            ->first();
    }

    public function reviewsCount(): int
    {
        return $this
            ->reviews()
            ->active()
            ->count();
    }
}
