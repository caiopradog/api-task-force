<?php

namespace App\Http\Controllers;

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
}
