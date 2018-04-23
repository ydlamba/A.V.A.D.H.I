<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('image', 'UserController@image');
Route::post('login', 'LoginRegisterController@login_api');
Route::get('leaderboard', 'UserController@leaderboard');
Route::get('online', 'UserController@allOnline');
Route::get('time/{id}', 'UserController@parseGraph');
Route::get('user/list', 'UserController@userList');
