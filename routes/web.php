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

Route::name('item.')->group(function(){
    Route::get('/', 'ItemController@index')->name('index');
    Route::get('/item/{item}', 'ItemController@show')->name('show');
});

Route::name('cartitem.')->group(function(){
    Route::post('/cartitem', 'CartItemController@store')->name('store')->middleware('auth');
    Route::get('/cartitem', 'CartItemController@index')->name('index')->middleware('auth');
    Route::delete('/cartitem/{cartItem}', 'CartItemController@destroy')->name('destroy')->middleware('auth');
    Route::put('/cartitem/{cartItem}', 'CartItemController@update')->name('update')->middleware('auth');
});

Route::get('/buy', 'BuyController@index')->middleware('auth');
Route::post('/buy', 'BuyController@store')->middleware('auth');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
