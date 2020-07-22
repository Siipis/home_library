<?php
// App Specific Twig Helpers
// @link https://twig.symfony.com/doc/3.x/advanced.html#creating-an-extension

namespace App\Twig\Extensions;


use App\Twig\TokenParser\Token_TokenParser;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Helpers extends AbstractExtension implements GlobalsInterface
{
    public function getFunctions()
    {
        return [
            new TwigFunction('raw_route', function (string $string) {
                $routes = \Route::getRoutes();

                if ($routes->hasNamedRoute($string)) {
                    return "'" . $routes->getByName($string)->uri . "'";
                }

                throw new \Exception("Named route $string was not found.");
            }, ['is_safe' => ['html']]),

            new TwigFunction('unique_id', function ($object) {
                if ($object instanceof Model) {
                    return $object->getTable() . '--' . $object->getKey();
                }

                return uniqid();
            }),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('layout', function (string $string) {
                return config('view.prefixes.layouts') . "." . $string;
            }),

            new TwigFilter('macro', function (string $string) {
                return config('view.prefixes.macros') . "." . $string;
            }),

            new TwigFilter('block', function (string $string) {
                return config('view.prefixes.blocks') . "." . $string;
            }),

            new TwigFilter('model', function (string $string) {
                return "App\\" . $string;
            }),

            new TwigFilter('vue', function ($model) {
                if (is_array($model)) {
                    return json_encode($model);
                }

                if ($model instanceof Model) {
                    return $model->toJson();
                }

                if ($model instanceof Collection) {
                    return $model->toJson();
                }

                if ($model instanceof EloquentCollection) {
                    return $model->toJson();
                }

                throw new \Exception("Unrecognized data type. Must be an instance of " .
                    Model::class . ", " .
                    Collection::class . " " .
                    "or " . EloquentCollection::class . "." .
                    get_class($model) . " given.");
            }, ['is_safe' => ['html']]),
        ];
    }

    public function getGlobals()
    {
        return [
            'app_name' => config('app.name'),
            'app_locale' => str_replace('_', '-', config('app.locale')),
            'app_timezone' => config('app.timezone'),

            'auth' => \Auth::user(),
        ];
    }

    public function getTokenParsers()
    {
        return [
            new Token_TokenParser(),
        ];
    }
}
