<?php

declare(strict_types = 1);

namespace App\Models;

final class Language extends Base
{
    const NAME_ENGLISH = 'English';
    const NAME_POLISH = 'Polish';

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
}
