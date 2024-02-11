<?php

declare(strict_types = 1);

namespace App\Observers;

use App\Models\Image;
use App\Services\Image\CoverCrop;
use App\Services\Image\PrimaryCrop;
use App\Services\Image\Thumbnail;
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
        
        $crop = new Thumbnail($image);
        $file = $crop->file();
        
        if ( ! empty($file)) {
            $variations['thumbnail'] = [
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
        $type = $image->imageable()->getRelated()->getMorphClass();

        if ( ! empty($image->file)) {
            $path = '';

            if ($type == Image::TYPE_IMAGEABLE_FARM) {
                $path = "farms/{$image->file}";
            } elseif ($type == Image::TYPE_IMAGEABLE_MARKET) {
                $path = "markets/{$image->file}";
            }

            if (Storage::disk('public')->exists($path)) {
                $image->size = Storage::disk('public')->size($path);
                $image->mime = Storage::disk('public')->mimeType($path);
            }
        }
    }

    public function deleted(Image $image)
    {
        $type = $image->imageable()->getRelated()->getMorphClass();

        if ( ! empty($image->file)) {
            $path = '';

            if ($type == Image::TYPE_IMAGEABLE_FARM) {
                $path = "farms/{$image->file}";
            } elseif ($type == Image::TYPE_IMAGEABLE_MARKET) {
                $path = "markets/{$image->file}";
            }

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        if ( ! empty($image->variations)) {
            $variations = $image->variations;

            foreach ($variations as $variation) {
                $path = '';

                if ($type == Image::TYPE_IMAGEABLE_FARM) {
                    $path = "farms/{$variation['file']}";
                } elseif ($type == Image::TYPE_IMAGEABLE_MARKET) {
                    $path = "markets/{$variation['file']}";
                }

                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }
    }
}
