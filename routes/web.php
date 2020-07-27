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
    Route::get('/', 'HomeController@index')->name('index');

    Route::prefix('library/{library:slug}')->as('library.')->group(function () {
        Route::get('/', 'LibraryController@index')->name('index');
        Route::post('/', 'LibraryController@books')->name('books');

        Route::post('search', 'LibraryController@search')->name('search');
        Route::post('details', 'LibraryController@details')->name('details');
        Route::post('cover', 'LibraryController@cover')->name('cover');

        Route::resource('books', 'Library\BookController', [
            'except' => 'index',
        ]);

        Route::resource('categories', 'Library\CategoryController');
    });

    Route::prefix('admin')->namespace('Admin')->as('admin.')->middleware('admin')->group(function () {
        Route::get('/', 'HomeController@index')->name('index');

        Route::resource('users', 'UserController');
        Route::put('users/{user}/promote', 'UserController@promote')->name('users.promote');

        Route::resource('libraries', 'LibraryController');
        Route::put('libraries/{library}/members', 'LibraryController@members')->name('library.members');
    });
});
