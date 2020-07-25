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

        Route::post('/search', 'LibraryController@search')->name('search');

        Route::resource('books', 'Library\BookController', [
            'except' => 'index',
        ]);
    });

    Route::get('/search', function () {
        return view('search');
    });

    /*
    Route::resource('invite', 'InviteController')->only([
        'store', 'show', 'destroy',
    ]);
    */

    Route::prefix('/admin')->namespace('Admin')->as('admin.')->middleware('admin')->group(function () {
        Route::get('/', 'HomeController@index')->name('index');

        Route::resource('users', 'UserController');
        Route::put('users/{user}/promote', 'UserController@promote')->name('users.promote');

        Route::resource('libraries', 'LibraryController');
        Route::put('libraries/{library}/members', 'LibraryController@members')->name('library.members');
    });
});
