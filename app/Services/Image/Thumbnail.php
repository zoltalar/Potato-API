<?php

declare(strict_types = 1);

namespace App\Services\Image;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

final class Thumbnail extends BaseCrop
{
    protected function crop($path, $file): void
    {
        $image = (new Imagine())->open($path . $file);
        $width = $image->getSize()->getWidth();
        $height = $image->getSize()->getHeight();
        $dimension = 300;
        
        if ($width > $height) {
            $w = $width * ($dimension / $height);
            $h = $dimension;
            $start = new Point((max($w - $dimension, 0)) / 2, 0);
        } else {
            $w = $dimension;
            $h = $height * ($dimension / $width);
            $start = new Point(0, (max($h - $dimension, 0)) / 2);
        }
        
        $info = pathinfo($path . $file);
        $file = $info['filename'] . '-' . $w . '-' . $h . '.' . $info['extension'];
        
        $thumbnail = $image->thumbnail(new Box($w, $h), ImageInterface::THUMBNAIL_OUTBOUND);
        $thumbnail->crop($start, new Box($dimension, $dimension));
        $thumbnail->save($path . $file);
        
        $this->file = $file;
    }
}
