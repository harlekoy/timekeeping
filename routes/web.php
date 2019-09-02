<?php

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

Route::get('/', 'HomeController@index')->name('home');

Route::resource('post', 'PostController', [
    'only' => ['index', 'show']
]);

Route::group(['middleware' => 'auth'], function () {
    Route::resource('post', 'PostController', [
        'only' => ['store', 'update', 'destroy'],
    ]);
});

Auth::routes();
