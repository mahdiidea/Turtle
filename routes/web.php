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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('home', 'HomeController@index')->name('home');
Route::get('auth', 'HomeController@auth')->name('auth');

Route::post('threads/{thread}/messages/markAsRead', 'MessageController@markAsRead');

Route::resource('posts', 'PostController');
Route::resource('threads', 'ThreadController');
Route::resource('contacts', 'ContactController');
Route::resource('attaches', 'AttachController');
Route::resource('threads/{thread}/messages/', 'MessageController');
