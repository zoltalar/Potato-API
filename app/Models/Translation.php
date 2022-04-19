<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Translation extends Base
{
    const TYPE_CATEGORY = 'c';
    const TYPE_INVENTORY = 'i';

    protected $fillable = [
        'name',
        'language_id'
    ];

    public $timestamps = false;

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public static function types(): array
    {
        return [
            self::TYPE_CATEGORY => __('phrases.category'),
            self::TYPE_INVENTORY => __('phrases.inventory')
        ];
    }
}
