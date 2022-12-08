<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
final class Comment extends Base
{
    const TYPE_COMMENTABLE_REVIEW = 'review';

    protected $fillable = [
        'content',
        'user_id'
    ];

    protected $casts = ['content' => 'encrypted'];

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
