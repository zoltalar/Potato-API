<?php

declare(strict_types = 1);

namespace App\Models;

final class Language extends Base
{
    const NAME_ENGLISH = 'English';
    const NAME_POLISH = 'Polish';

    const CODE_EN = 'en';
    const CODE_PL = 'pl';

    protected $fillable = [
        'name',
        'native',
        'code',
        'system',
        'active'
    ];

    protected $casts = [
        'system' => 'integer',
        'active' => 'integer'
    ];

    public $timestamps = false;

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public static function codes(): array
    {
        return [self::CODE_EN, self::CODE_PL];
    }

    public static function diacritics(): string
    {
        return self::polishDiacritics();
    }

    public static function polishDiacritics(): string
    {
        return 'ąćęłńóśźż';
    }
}
