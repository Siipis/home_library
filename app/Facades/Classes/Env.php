<?php


namespace App\Facades\Classes;

use App;

class Env
{
    public function production()
    {
        return App::environment() == 'production';
    }

    public function testing()
    {
        return App::environment() == 'testing';
    }

    public function development()
    {
        return App::environment() == 'development';
    }

    public function notLive()
    {
        return !$this->production();
    }
}
