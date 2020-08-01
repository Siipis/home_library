<?php

namespace App\Providers;

use App\Facades\Classes\Alert as AlertClass;
use App\Facades\Classes\Env as EnvClass;
use Env;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;
use URL;
use Validator;

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

        $this->app->singleton('env', function () {
            return new EnvClass();
        });

        $this->app->bind('alert', function () {
            return new AlertClass();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Env::production()) {
            URL::forceScheme('https');
        }

        Builder::defaultStringLength(191);

        Validator::extend('slug', function ($attribute, $value, $parameters, $validator) {
            return $value === \Str::slug($value);
        });
    }
}
