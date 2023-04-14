<?php

declare(strict_types = 1);

namespace App\Models;

use Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Image extends Base
{
    const TYPE_IMAGEABLE_FARM = 'farm';
    const TYPE_IMAGEABLE_MARKET = 'market';
    
    const TYPES = [
        self::TYPE_IMAGEABLE_FARM,
        self::TYPE_IMAGEABLE_MARKET
    ];

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

    protected $appends = ['file_url'];

    // --------------------------------------------------
    // Scopes
    // --------------------------------------------------

    public function scopeCover(Builder $query): Builder
    {
        return $query->where('cover', 1);
    }

    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('primary', 1);
    }
    
    public function scopeFile(Builder $query, string $file): Builder
    {
        return $query->where(function($query) use ($file) {
            $query
                ->where('file', $file)
                ->orWhere(function($query) use ($file) {
                    $query
                        ->orWhereJsonContains('variations->cover->file', $file)
                        ->orWhereJsonContains('variations->primary->file', $file);
                });                
        });
    }

    // --------------------------------------------------
    // Accessors and Mutators
    // --------------------------------------------------

    public function getFileUrlAttribute($value): ?string
    {
        $file = $this->attributes['file'] ?? null;

        if ( ! empty($file)) {
            return route('api.potato.images.stream', ['file' => $file]);
        }

        return null;
    }

    public function getTitleAttribute($value): ?string
    {
        if (empty($value)) {
            $value = sprintf('(%s)', mb_strtolower(__('phrases.no_title')));
        }

        return $value;
    }

    public function setTitleAttribute($value): void
    {
        if ( ! empty($value)) {
            $value = strip_tags($value);
        }

        $this->attributes['title'] = $value;
    }

    public function getVariationsAttribute($value): ?array
    {
        if ( ! empty($value)) {
            $value = json_decode($value, true);

            if (is_array($value)) {
                
                foreach ($value as $crop => & $data) {
                    $data['file_url'] = route('api.potato.images.stream', ['file' => $data['file']]);
                }
            }
        }

        return $value;
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
            self::TYPE_IMAGEABLE_FARM,
            self::TYPE_IMAGEABLE_MARKET
        ];
    }

    public function variation($name, $key = null)
    {
        $path = $name . ($key !== null ? '.' . $key : '');

        return Arr::get($this->variations, $path);
    }
}
