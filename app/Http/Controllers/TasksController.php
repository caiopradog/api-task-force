<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Faker\Generator as Faker;
use App\Constants\TasksCategoryConstant;
use App\Constants\TasksStatusConstant;
use App\Services\TaskService;

class TasksController extends Controller
{
    public function tasks(TaskService $taskService)
    {
        $perPage = request('perPage');

        $tasks = $taskService->list(request()->toArray());

        if ($perPage > 0) {
            $tasks = $tasks->paginate($perPage);
        } else {
            $tasks = $tasks->get();
        }

        $tasks->transform(function ($task) {
            $task->status_badge = $task->badge();
            return $task;
        });

        return response()
            ->json($tasks);
    }

    public function task($id, TaskService $taskService)
    {
        $task = $taskService->findTaskById($id);

        return response()
            ->json($task);
    }

    public function task_categories()
    {
        $categories = TasksCategoryConstant::getConstants()->map(function ($item) {
            return [
                'name' => $item,
                'value' => $item,
            ];
        })->values();

        return response()
            ->json($categories);
    }

    public function task_statuses()
    {
        $statuses = TasksStatusConstant::getConstants()->map(function ($item) {
            return [
                'name' => $item,
                'value' => $item,
            ];
        })->values();

        return response()
            ->json($statuses);
    }

    public function add_task(TaskService $taskService) {
        $task = new Task();

        $task->name = request('name');
        $task->status = request('status');
        $task->category = request('category');
        $task->deadline = request('deadline');
        $task->project_id = request('project_id');
        $task->sprint_id = request('sprint_id');
        $task->epic_id = request('epic_id');
        $task->time_planned = request('time_planned');
        $task->time_used = request('time_used');
        $task->priority = request('priority');
        $task->qa_user_id = request('qa_user_id');
        $task->dev_user_id = request('dev_user_id');
        $task->description = request('description');

        if ($taskService->create($task)) {
            return response()
                ->json(['msg' => "Tarefa cadastrada com sucesso!"]);
        } else {
            return response()
                ->json(['msg' => "Não foi possível fazer o cadastro, tente novamente mais tarde."], 400);
        }
    }

    public function edit_task($id, TaskService $taskService) {
        $task = $taskService->findTaskById($id);

        $task->name = request('name');
        $task->status = request('status');
        $task->category = request('category');
        $task->deadline = request('deadline');
        $task->project_id = request('project_id');
        $task->sprint_id = request('sprint_id');
        $task->epic_id = request('epic_id');
        $task->time_planned = request('time_planned');
        $task->time_used = request('time_used');
        $task->priority = request('priority');
        $task->qa_user_id = request('qa_user_id');
        $task->dev_user_id = request('dev_user_id');
        $task->description = request('description');

        if ($taskService->update($task)) {
            return response()
                ->json(['msg' => "Tarefa cadastrada com sucesso!"]);
        } else {
            return response()
                ->json(['msg' => "Não foi possível fazer o cadastro, tente novamente mais tarde."], 400);
        }
    }
}
