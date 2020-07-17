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
    'verify' => true
]);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', 'HomeController@index')->name('index');

    Route::get('/search', function () {
        return view('search');
    });

    Route::prefix('/admin')->namespace('Admin')->as('admin.')->middleware('admin')->group(function () {
        Route::get('/', 'HomeController@index')->name('index');

        Route::resource('users', 'UserController');
        Route::put('users/{user}/promote', 'UserController@promote')->name('users.promote');

        Route::resource('libraries', 'LibraryController');
    });
});
