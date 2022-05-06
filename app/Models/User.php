<?php

declare(strict_types = 1);

namespace App\Models;

use App\Contracts\Namable as NamableContract;
use App\Traits\Namable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public function preferredLocale(): ?string
    {
        return $this->country->code ?? null;
    }

    public function sendEmailVerificationNotification(): void
    {
        $url = $this->verificationUrl();

        $notification = new VerifyEmail();
        $notification->createUrlUsing(function() use ($url) {
            return $url;
        });

        $this->notify($notification);
    }

    public function verificationUrl(): string
    {
        $url = $this->potatoAppBaseUrl();
        $locale = $this->preferredLocale();
        $id = $this->getKey();
        $email = encrypt($this->getEmailForVerification());

        return sprintf('%s/%s/email/verify/%d/%s', $url, $locale, $id, $email);
    }

    public function potatoAppBaseUrl(): string
    {
        return config('app.nuxt_app_url');
    }
}
