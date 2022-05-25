<?php

declare(strict_types = 1);

namespace App\Models;

use Arr;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\UploadedFile;

final class Image extends Base
{
    const TYPE_IMAGEABLE_FARM = 'farm';

    protected $fillable = [
        'title',
        'file',
        'variations',
        'mime',
        'size',
        'primary',
        'cover'
    ];

    protected $casts = [
        'variations' => 'array',
        'primary' => 'integer',
        'cover' => 'integer'
    ];

    // --------------------------------------------------
    // Accessors and Mutators
    // --------------------------------------------------

    public function setTitleAttribute($value): void
    {
        if ( ! empty($value)) {
            $value = strip_tags($value);
        }

        $this->attributes['title'] = $value;
    }

    public function setVariationsAttribute($value): void
    {
        $this->attributes['variations'] = json_encode($value);
    }

    // --------------------------------------------------
    // Relationships
    // --------------------------------------------------

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    // --------------------------------------------------
    // Other
    // --------------------------------------------------

    public static function imageableTypes(): array
    {
        return [
            self::TYPE_IMAGEABLE_FARM
        ];
    }

    public function variation($name, $key = null)
    {
        $path = $name . ($key !== null ? '.' . $key : '');

        return Arr::get($this->variations, $path);
    }
}
