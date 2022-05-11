<?php

declare(strict_types = 1);

namespace App\Models;

use App\Contracts\Namable as NamableContract;
use App\Traits\Namable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Str;

final class Farm extends Base implements NamableContract
{
    use Namable;

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
        'operating_hours',
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

    public function setFaxAttribute($value): void
    {
        if ( ! empty($value)) {
            $value = Str::stripNonDigits($value);
        }

        $this->attributes['fax'] = $value;
    }

    public function setOperatingHoursAttribute($value): void
    {
        if ( ! empty($value)) {
            $value = strip_tags($value);
        }

        $this->attributes['operating_hours'] = $value;
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
