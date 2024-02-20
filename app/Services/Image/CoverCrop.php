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

        if ($height > 340) {
            $info = pathinfo($path . $file);
            $file = $info['filename'] . '-' . $width . '-' . 340 . '.' . $info['extension'];

            $start = new Point(0, ($height - 340) / 2);
            $size = new Box($width, 340);

            $image->crop($start, $size);
            $image->save($path . $file);

            $this->file = $file;
        }
    }
}
