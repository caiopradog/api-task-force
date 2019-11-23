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
    Route::get('default_statuses', function() {
        $statuses = \App\Constants\DefaultStatusConstant::getConstants()->map(function ($item) {
            return [
                'name' => $item,
                'value' => $item,
            ];
        })->values();

        return response()
            ->json($statuses);
    });

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
    Route::get('/calendar', 'TasksController@calendar');
    Route::get('/task_categories', "TasksController@task_categories");
    Route::get('/task_statuses', "TasksController@task_statuses");
    Route::get('/task_comments/{id}', "TasksController@task_comments");
    //Rotas para add/edit
    Route::put('/task', "TasksController@add_task");
    Route::put('/task/{id?}', "TasksController@edit_task");
    Route::delete('/task/{id}', "TasksController@delete_task");
    Route::post('/update_status/{id}', "TasksController@update_status");
    Route::post('/approve_task/{id}', "TasksController@approve_task");
    Route::post('/reprove_task/{id}', "TasksController@reprove_task");
    Route::put('/add_task_comment/{id}', "TasksController@add_comment");

    Route::get('/project/{id}', "ProjectsController@project");
    Route::get('/projects', "ProjectsController@projects");
    //Rotas para add/edit
    Route::put('/project', "ProjectsController@add_project");
    Route::put('/project/{id?}', "ProjectsController@edit_project");
    Route::delete('/project/{id}', "ProjectsController@delete_project");

    Route::get('/epic/{id}', "EpicsController@epic");
    Route::get('/epics', "EpicsController@epics");
    //Rotas para add/edit
    Route::put('/epic', "EpicsController@add_epic");
    Route::put('/epic/{id?}', "EpicsController@edit_epic");
    Route::delete('/epic/{id}', "EpicsController@delete_epic");

    Route::get('/sprint/{id}', "SprintsController@sprint");
    Route::get('/sprints', "SprintsController@sprints");
    //Rotas para add/edit
    Route::put('/sprint', "SprintsController@add_sprint");
    Route::put('/sprint/{id?}', "SprintsController@edit_sprint");
    Route::delete('/sprint/{id}', "SprintsController@delete_sprint");
});