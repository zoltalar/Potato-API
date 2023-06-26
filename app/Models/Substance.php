<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

final class Substance extends Base
{
    protected $fillable = [
        'name',
        'list_order',
        'active'
    ];

    protected $casts = [
        'list_order' => 'integer',
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
}
