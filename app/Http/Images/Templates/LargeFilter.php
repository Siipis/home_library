<?php


namespace App\Http\Images\Templates;


use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class LargeFilter implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->resize(600, null, function ($c) {
            $c->aspectRatio();
        });
    }
}
