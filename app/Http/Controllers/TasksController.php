<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Models\Task;
use App\Constants\TasksCategoryConstant;
use App\Constants\TasksStatusConstant;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class TasksController extends Controller
{
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'status' => 'required|string',
            'category' => 'required|string',
            'deadline' => 'required|date',
            'project_id' => 'required',
            'sprint_id' => 'required',
            'epic_id' => 'required',
            'time_planned' => 'required',
            'time_used' => 'required',
            'priority' => 'required',
            'qa_user_id' => 'required',
            'dev_user_id' => 'required',
            'description' => 'required|string'
        ], [
            'required' => 'Este campo é obrigatório.',
            'date' => 'Este campo deve ser uma data.'
        ]);
    }

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
            $task->status_color = $task->status_color();
            return $task;
        });

        return response()
            ->json($tasks);
    }

    public function calendar(TaskService $taskService)
    {
        $dateField = request()->get('dateField', 'start_date');
        $tasks = $taskService->list(request()->toArray())->orderBy($dateField, 'asc')->get();

        $calendar = [];
        $tasks->each(function ($task) use ($dateField, &$calendar) {
            $date = $task->{$dateField};
            if ($task->status == 'Finalizado') {
                $class = 'calendar-done';
            } else if ($date < date('Y-m-d')) {
                $class = 'calendar-danger';
            } else if ($task->status == 'Pendente') {
                $class = 'calendar-todo';
            } else if ($task->status == 'Em Andamento') {
                $class = 'calendar-doing';
            } else if ($task->status == 'Qualidade') {
                $class = 'calendar-quality';
            } else {
                $class = 'calendar-backlog';
            }
            $timeRemaining = $task->time_planned - $task->time_used;
            $timeRemaining = substr(Helper::convertSecToTime($timeRemaining), 0, -3);
            $title = $timeRemaining.' | '.$task->name;
            $calendar[] = [
                'start' => $date.'T00:00:00',
                'title' => $title,
                'id' => $task->id,
                'classNames' => $class,
                'allDay' => true,
            ];
        });

        return response()
            ->json($calendar);
    }

    public function task($id, TaskService $taskService)
    {
        $withRelations = request()->get('withRelations', false);
        $task = $taskService->findTaskById($id, $withRelations);

        $task->status_color = $task->status_color();

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

    public function add_task(Request $request, TaskService $taskService) {
        $this->validator($request);

        $task = new Task();

        $task->name = $request->get('name');
        $task->status = $request->get('status');
        $task->category = $request->get('category');
        $task->deadline = $request->get('deadline');
        $task->project_id = $request->get('project_id');
        $task->sprint_id = $request->get('sprint_id');
        $task->epic_id = $request->get('epic_id');
        $task->time_planned = $request->get('time_planned');
        $task->time_used = $request->get('time_used');
        $task->priority = $request->get('priority');
        $task->qa_user_id = $request->get('qa_user_id');
        $task->dev_user_id = $request->get('dev_user_id');
        $task->description = $request->get('description');

        if ($taskService->create($task)) {
            return response()
                ->json(['msg' => "Tarefa cadastrada com sucesso!", 'task' => $task], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível fazer o cadastro, tente novamente mais tarde."], 400);
        }
    }

    public function edit_task($id, Request $request, TaskService $taskService) {
        $this->validator($request);

        $task = $taskService->findTaskById($id);

        $task->name = $request->get('name');
        $task->status = $request->get('status');
        $task->category = $request->get('category');
        $task->deadline = $request->get('deadline');
        $task->project_id = $request->get('project_id');
        $task->sprint_id = $request->get('sprint_id');
        $task->epic_id = $request->get('epic_id');
        $task->time_planned = $request->get('time_planned');
        $task->time_used = $request->get('time_used');
        $task->priority = $request->get('priority');
        $task->qa_user_id = $request->get('qa_user_id');
        $task->dev_user_id = $request->get('dev_user_id');
        $task->description = $request->get('description');

        if ($taskService->update($task)) {
            return response()
                ->json(['msg' => "Tarefa modificada com sucesso!", 'task' => $task], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível modificar a tarefa, tente novamente mais tarde."], 400);
        }
    }

    public function delete_task($id, TaskService $taskService) {
        $task = $taskService->findTaskById($id);

        if ($taskService->delete($task)) {
            return response()
                ->json(['msg' => "Tarefa deletada com sucesso!"], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível deletar a tarefa, tente novamente mais tarde."], 400);
        }
    }

    public function assign_tasks() {
        Artisan::queue('assign:tasks');

        return response()
            ->json(['msg' => "As tarefas estão sendo analisadas e atribuídas, por favor, aguarde um momento."], 200);
    }
}
