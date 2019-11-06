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

Route::post('/assign_tasks', 'TasksController@assign_tasks');
Route::middleware('auth:api')->group(function () {
    Route::get('/user', "UserController@user");
    Route::get('/user/{id}', "UserController@user");
    Route::get('/users', "UserController@index");
    Route::get('/user_statuses', "UserController@user_statuses");
    Route::get('/user_skills/{id}', "UserController@user_skills");
    //Rotas para add/edit
    Route::put('/user', "UserController@add_user");
    Route::put('/user/{id?}', "UserController@edit_user");
    Route::delete('/user/{id}', "UserController@delete_user");

    Route::get('/user_group', "UserGroupController@userGroup");
    Route::get('/user_group/{id}', "UserGroupController@userGroup");
    Route::get('/user_groups', "UserGroupController@index");

    Route::get('/task/{id}', "TasksController@task");
    Route::get('/tasks', "TasksController@tasks");
    Route::get('/task_categories', "TasksController@task_categories");
    Route::get('/task_statuses', "TasksController@task_statuses");
    Route::get('/calendar', 'TasksController@calendar');
    //Rotas para add/edit
    Route::put('/task', "TasksController@add_task");
    Route::put('/task/{id?}', "TasksController@edit_task");
    Route::delete('/task/{id}', "TasksController@delete_task");

    Route::get('/project/{id}', "ProjectsController@project");
    Route::get('/projects', "ProjectsController@projects");

    Route::get('/epic/{id}', "EpicsController@epic");
    Route::get('/epics', "EpicsController@epics");

    Route::get('/sprint/{id}', "SprintsController@sprint");
    Route::get('/sprints', "SprintsController@sprints");
});