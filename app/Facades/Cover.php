<?php


namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class Cover extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cover';
    }
}
