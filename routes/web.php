<?php

use App\Events\TestEvent;
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

Route::get('/', function () {
    broadcast(new TestEvent('hello'));
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/chats', 'ChatsController@index')->name('chats.index');
Route::get('/messages', 'ChatsController@fetchMessages')->name('chats.fetchMessages');
Route::post('/messages', 'ChatsController@sendMessage')->name('chats.sendMessage');
