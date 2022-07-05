<?php

declare(strict_types = 1);

namespace App\Models;

use App\Contracts\Namable as NamableContract;
use App\Notifications\VerifyEmail;
use App\Traits\Namable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\RoutesNotifications;
use Laravel\Passport\HasApiTokens;
use Str;

final class User extends Base implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    HasLocalePreference,
    MustVerifyEmailContract,
    NamableContract
{
    use Authenticatable,
        Authorizable,
        CanResetPassword,
        MustVerifyEmail,
        RoutesNotifications,
        HasApiTokens,
        Namable,
        Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'language_id',
        'country_id',
        'email',
        'email_verified_at',
        'phone',
        'password',
        'active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = ['active' => 'integer'];

    protected $constraints = ['farms'];

    // --------------------------------------------------
    // Accessors and Mutators
    // --------------------------------------------------

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = Str::stripNonDigits($value);
    }

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function farms(): HasMany
    {
        return $this->hasMany(Farm::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public function preferredLocale(): ?string
    {
        return $this->language->code ?? null;
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail());
    }

    public function verificationUrl(): string
    {
        $url = $this->potatoAppBaseUrl();
        $id = $this->getKey();
        $email = encrypt($this->getEmailForVerification());

        return sprintf('%s/email/verify/%d/%s', $url, $id, $email);
    }

    public function potatoAppBaseUrl(): string
    {
        return config('app.nuxt_app_url');
    }
}
