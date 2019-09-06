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

Route::get('/task_categories', "TasksController@task_categories");
Route::get('/task_statuses', "TasksController@task_statuses");
Route::get('/teste', "TasksController@teste");