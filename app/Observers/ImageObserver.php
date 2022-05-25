<?php

declare(strict_types = 1);

namespace App\Observers;

use App\Models\Farm;
use App\Models\Image;
use App\Services\Image\CoverCrop;
use App\Services\Image\PrimaryCrop;
use Storage;

class ImageObserver
{
    public function created(Image $image)
    {
        $variations = [];

        $crop = new PrimaryCrop($image);
        $file = $crop->file();

        if ( ! empty($file)) {
            $variations['primary'] = [
                'file' => $file,
                'mime' => $image->mime,
                'size' => $crop->size()
            ];
        }

        $crop = new CoverCrop($image);
        $file = $crop->file();

        if ( ! empty($file)) {
            $variations['cover'] = [
                'file' => $file,
                'mime' => $image->mime,
                'size' => $crop->size()
            ];
        }

        $image->variations = $variations;
        $image->update();
    }

    public function saving(Image $image)
    {
        $class = $image->imageable()->getRelated()->getMorphClass();

        if ( ! empty($image->file)) {
            $path = '';

            if ($class == Farm::class) {
                $path = "farms/{$image->file}";
            }

            if (Storage::disk('public')->exists($path)) {
                $image->size = Storage::disk('public')->size($path);
                $image->mime = Storage::disk('public')->mimeType($path);
            }
        }
    }

    public function deleted(Image $image)
    {
        $class = $image->imageable()->getRelated()->getMorphClass();

        if ( ! empty($image->file)) {
            $path = '';

            if ($class == Farm::class) {
                $path = "farms/{$image->file}";
            }

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        if ( ! empty($image->variations)) {
            $variations = $image->variations;

            foreach ($variations as $variation) {
                $path = '';

                if ($class == Farm::class) {
                    $path = "farms/{$variation['file']}";
                }

                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }
    }
}
