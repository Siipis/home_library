<?php

namespace App\Providers;

use App\Facades\Classes\Alert;
use Validator;
use App\Facades\Classes\Env;
use Illuminate\Support\ServiceProvider;

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
            return new Env();
        });

        $this->app->bind('alert', function () {
            return new Alert();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('slug', function ($attribute, $value, $parameters, $validator) {
            return $value === \Str::slug($value);
        });
    }
}
