<?php

declare(strict_types = 1);

namespace App\Models;

final class Currency extends Base
{
    const NAME_US_DOLLAR = 'US Dollar';
    const NAME_POLISH_ZLOTY = 'Polish Zloty';

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'number'
    ];

    protected $casts = ['number' => 'integer'];

    public $timestamps = false;
}
