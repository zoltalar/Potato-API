<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Message extends Base
{
    const TYPE_MESSAGEABLE_FARM = 'farm';

    protected $fillable = [
        'subject',
        'content',
        'sender_id',
        'recipient_id'
    ];

    protected $casts = [
        'content' => 'encrypted',
        'read_at' => 'datetime'
    ];

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
