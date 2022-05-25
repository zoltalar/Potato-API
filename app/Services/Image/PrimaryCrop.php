<?php

declare(strict_types = 1);

namespace App\Services\Image;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

final class PrimaryCrop extends BaseCrop
{
    protected function crop($path, $file): void
    {
        $image = (new Imagine())->open($path . $file);
        $width = $image->getSize()->getWidth();
        $height = $image->getSize()->getHeight();

        if ($height != $width) {
            $info = pathinfo($path . $file);
            $dimension = min($height, $width);
            $file = $info['filename'] . '-' . $dimension . '-' . $dimension . '.' . $info['extension'];

            $start = new Point(($width - $dimension) / 2, ($height - $dimension) / 2);
            $size = new Box($dimension, $dimension);

            $image->crop($start, $size);
            $image->save($path . $file);

            $this->file = $file;
        }
    }
}
