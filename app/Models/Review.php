<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Review extends Base
{
    const TYPE_RATEABLE_FARM = 'farm';
    const TYPE_RATEABLE_MARKET = 'market';

    protected $fillable = [
        'title',
        'content',
        'rating',
        'active',
        'user_id'
    ];

    protected $casts = [
        'content' => 'encrypted',
        'rating' => 'integer',
        'active' => 'integer'
    ];

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function rateable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // --------------------------------------------------
    // Accessors and Mutators
    // --------------------------------------------------

    public function getTitleAttribute($value): ?string
    {
        if (empty($value)) {
            $value = sprintf('(%s)', mb_strtolower(__('phrases.no_title')));
        }

        return $value;
    }
}
