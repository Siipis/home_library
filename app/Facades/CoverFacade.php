<?php


namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class CoverFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cover';
    }
}
