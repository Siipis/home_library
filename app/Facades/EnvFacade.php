<?php


namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class EnvFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'env';
    }
}
