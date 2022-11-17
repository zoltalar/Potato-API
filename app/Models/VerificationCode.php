<?php

declare(strict_types = 1);

namespace App\Models;

use App\Services\VerificationCode\Generator;
use Illuminate\Database\Eloquent\Builder;

final class VerificationCode extends Base
{
    protected $fillable = [
        'code',
        'verifiable',
        'expires_at'
    ];

    protected $hidden = ['code'];

    protected $casts = ['expires_at' => 'datetime'];

    // --------------------------------------------------
    // Scopes
    // --------------------------------------------------

    public function scopeNotExpired($query): Builder
    {
        return $query->where('expires_at', '>=', now());
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public static function createFor(string $verifiable): string
    {
        self::create([
            'code' => ($code = (new Generator())->generate()),
            'verifiable' => $verifiable
        ]);

        return $code;
    }

    public function expireHours(): int
    {
        return 1;
    }

    public function maxCodes(): int
    {
        return 1;
    }
}
