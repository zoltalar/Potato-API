<?php

declare(strict_types = 1);

namespace App\Models;

use App\Contracts\Namable as NamableContract;
use App\Traits\Namable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\RoutesNotifications;
use Laravel\Passport\HasApiTokens;
use Str;

final class Admin extends Base implements
    AuthenticatableContract,
    AuthorizableContract,
    NamableContract
{
    use Authenticatable,
        Authorizable,
        RoutesNotifications,
        HasApiTokens,
        Namable,
        Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'system',
        'active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'system' => 'integer',
        'active' => 'integer'
    ];

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
    // Other
    // --------------------------------------------------

    public function preferredLocale(): string
    {
        return Language::CODE_EN;
    }
}
