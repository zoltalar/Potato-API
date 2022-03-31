<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

final class Category extends Base
{
    const TYPE_INVENTORY = 1;

    protected $fillable = [
        'name',
        'type',
        'list_order',
        'system',
        'active'
    ];

    protected $casts = [
        'type' => 'integer',
        'list_order' => 'integer',
        'system' => 'integer',
        'active' => 'integer'
    ];

    public $timestamps = false;

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function translation(Language $language)
    {
        return $this
            ->translations()
            ->where('language_id', $language->id)
            ->first();
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public static function types(): array
    {
        return [self::TYPE_INVENTORY => __('phrases.inventory')];
    }
}
