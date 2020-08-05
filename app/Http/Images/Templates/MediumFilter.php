<?php


namespace App\Http\Images\Templates;


use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class MediumFilter implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->resize(280, null, function ($c) {
            $c->aspectRatio();
        });
    }
}
