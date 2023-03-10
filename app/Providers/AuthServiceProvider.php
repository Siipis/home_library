<?php

namespace App\Providers;

use App\Category;
use App\Library;
use App\Policies\BookPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\LibraryPolicy;
use App\Policies\UserPolicy;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Book::class => BookPolicy::class,
        Category::class => CategoryPolicy::class,
        Library::class => LibraryPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('access-backend', function (User $user) {
            return $user->isAdmin();
        });
    }
}
