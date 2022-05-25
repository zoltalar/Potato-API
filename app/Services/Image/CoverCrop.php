<?php

declare(strict_types = 1);

namespace App\Services\Image;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

class CoverCrop extends BaseCrop
{
    protected function crop($path, $file): void
    {
        $image = (new Imagine())->open($path . $file);
        $width = $image->getSize()->getWidth();
        $height = $image->getSize()->getHeight();

        if ($height > 330) {
            $info = pathinfo($path . $file);
            $file = $info['filename'] . '-' . $width . '-' . 330 . '.' . $info['extension'];

            $start = new Point(0, ($height - 330) / 2);
            $size = new Box($width, 300);

            $image->crop($start, $size);
            $image->save($path . $file);

            $this->file = $file;
        }
    }
}
