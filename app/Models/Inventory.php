<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Storage;
use Str;

final class Inventory extends Base
{
    protected $table = 'inventory';

    protected $fillable = [
        'name',
        'category_id',
        'photo',
        'system'
    ];

    protected $casts = ['system' => 'integer'];

    public $timestamps = false;

    // --------------------------------------------------
    // Accessors and Mutators
    // --------------------------------------------------

    public function setPhotoAttribute($value): void
    {
        $photo = $value;

        if ($photo instanceof UploadedFile && $photo->isValid()) {
            $oldPhoto = $this->attributes['photo'] ?? null;

            if ( ! empty($oldPhoto)) {
                $path = "inventory/{$oldPhoto}";

                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            $extension = $photo->guessExtension();
            $value = self::randomFileName() . '.' . $extension;

            $photo->storeAs('inventory', $value, 'public');
        }

        $this->attributes['photo'] = $value;
    }

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }

    public function translation(Language $language): ?object
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

    public static function randomFileName(): string
    {
        return md5(Str::uuid()->getHex()->toString());
    }
}
