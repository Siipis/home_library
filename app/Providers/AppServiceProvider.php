<?php

namespace App\Providers;

use App\Facades\Classes\Alert as AlertClass;
use App\Facades\Classes\Cover as CoverClass;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // @link https://github.com/barryvdh/laravel-ide-helper
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->bind('alert', function () {
            return new AlertClass();
        });

        $this->app->singleton('cover', function () {
            return new CoverClass();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment() === 'production') {
            URL::forceScheme('https');
        }

        Builder::defaultStringLength(191);

        Validator::extend('slug', function ($attribute, $value, $parameters, $validator) {
            return $value === Str::slug($value);
        });
    }
}
