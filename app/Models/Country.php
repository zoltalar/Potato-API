<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Country extends Base
{
    const NAME_POLAND = 'Poland';
    const NAME_UNITED_STATES = 'United States';

    const CODE_PL = 'pl';
    const CODE_US = 'us';

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

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class);
    }
}
