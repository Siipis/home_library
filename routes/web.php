<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'verify' => true,
    'register' => false,
]);

Route::middleware(['auth', 'verified'])->group(function () {
    // General routes
    Route::get('/', 'HomeController@index')->name('index');
    Route::get('settings', 'HomeController@settings')->name('settings');
    Route::post('updateAccount', 'HomeController@updateAccount')->name('settings.account');

    // Book specific routes (outside libraries)
    Route::prefix('books')->as('books.')->group(function() {
        Route::post('search', 'ApiController@search')->name('search');
        Route::post('details', 'ApiController@details')->name('details');
        Route::post('cover', 'ApiController@cover')->name('cover');
        Route::get('cover/placeholder/{size?}', 'Library\BookController@noCover')->name('no_cover');
    });

    // Library specific routes
    Route::prefix('library/{library}')->as('library.')->group(function () {
        Route::get('/', 'LibraryController@index')->name('index');
        Route::post('/', 'LibraryController@books')->name('books');

        Route::resource('categories', 'Library\CategoryController');
        Route::resource('books', 'Library\BookController', [
            'except' => 'index',
        ]);
        Route::get('books/{book}/cover/{size?}', 'Library\BookController@cover')->name('books.cover');
    });

    // Admin panel specific routes
    Route::prefix('admin')->namespace('Admin')->as('admin.')->middleware('admin')->group(function () {
        Route::get('/', 'HomeController@index')->name('index');

        Route::resource('users', 'UserController');
        Route::put('users/{user}/promote', 'UserController@promote')->name('users.promote');

        Route::resource('libraries', 'LibraryController');
        Route::put('libraries/{library}/members', 'LibraryController@members')->name('library.members');
    });
});
