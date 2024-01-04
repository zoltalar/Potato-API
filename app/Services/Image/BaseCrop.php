<?php

declare(strict_types = 1);

namespace App\Services\Image;

use App\Models\Image;

abstract class BaseCrop
{
    /** @var Image */
    protected $image;

    /** @var string */
    protected $file = '';

    public function __construct(Image $image)
    {
        $this->image = $image;
        $this->process();
    }

    protected function process(): void
    {
        $file = $this->image->file;
        $path = $this->path();

        $this->crop($path, $file);
    }

    abstract protected function crop($path, $file): void;

    public function path(): string
    {
        $path = '';
        $type = $this->morphClass();

        if ($type == Image::TYPE_IMAGEABLE_FARM) {
            $path = storage_path('app/public/farms/');
        } elseif ($type == Image::TYPE_IMAGEABLE_MARKET) {
            $path = storage_path('app/public/markets/');
        }

        return $path;
    }

    public function morphClass(): string
    {
        return $this
            ->image
            ->imageable()
            ->getRelated()
            ->getMorphClass();
    }

    public function file(): string
    {
        return $this->file;
    }

    public function size(): int
    {
        return filesize($this->path() . $this->file());
    }
}
