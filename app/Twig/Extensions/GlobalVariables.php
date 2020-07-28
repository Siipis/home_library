<?php
// App Specific Twig Helpers
// @link https://twig.symfony.com/doc/3.x/advanced.html#creating-an-extension

namespace App\Twig\Extensions;


use Route;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class GlobalVariables extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals()
    {
        return array_merge([],
            $this->getAppVariables(),
            $this->getUserVariables(),
            $this->getRouteVariables()
        );
    }

    private function getAppVariables()
    {
        return [
            'app_name' => config('app.name'),
            'app_locale' => str_replace('_', '-', config('app.locale')),
            'app_timezone' => config('app.timezone'),
        ];
    }

    private function getUserVariables()
    {
        return [
            'auth' => \Auth::user(),
        ];
    }

    private function getRouteVariables()
    {
        return Route::current()->parameters;
    }
}
