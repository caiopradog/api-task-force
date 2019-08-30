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

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

Route::post('/login', 'AuthController@login');
Route::middleware('auth:api')->group(function () {
    Route::get('/users', "UserController@index");
    Route::get('/user', "UserController@user");
    Route::get('/user/{id}', "UserController@user");
    Route::get('/user_group', "UserGroupController@userGroup");
    Route::get('/user_group/{id}', "UserGroupController@userGroup");
    Route::get('/user_groups', "UserGroupController@index");
});