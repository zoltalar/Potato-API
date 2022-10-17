<?php

declare(strict_types = 1);

namespace App\Models;

final class Currency extends Base
{
    const NAME_EURO = 'Euro';
    const NAME_POLISH_ZLOTY = 'Polish Zloty';

    const CODE_EUR = 'EUR';
    const CODE_PLN = 'PLN';

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'number'
    ];

    protected $casts = ['number' => 'integer'];

    public $timestamps = false;

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public static function codes(): array
    {
        return [self::CODE_EUR, self::CODE_PLN];
    }
}
