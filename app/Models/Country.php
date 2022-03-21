<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Country extends Base
{
    const NAME_POLAND = 'Poland';
    const NAME_UNITED_STATES = 'United States';

    protected $fillable = [
        'name',
        'native',
        'code',
        'date_format',
        'time_format',
        'system',
        'active'
    ];

    protected $casts = [
        'system' => 'integer',
        'active' => 'integer'
    ];

    public $timestamps = false;

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class);
    }
}
