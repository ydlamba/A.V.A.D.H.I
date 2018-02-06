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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/welcome', 'LoginRegisterController@index');
Route::post('/login', 'LoginRegisterController@login');
Route::get('/logout', 'DashboardController@logout');
Route::post('/register', 'LoginRegisterController@register');
Route::get('/dashboard', 'DashboardController@index');
Route::post('/user/mac', 'UserController@registerMAC');
Route::get('/dashboard/bar', 'UserController@barGraph');
Route::get('/user','UserController@index');
