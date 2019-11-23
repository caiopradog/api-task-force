<?php

namespace App\Http\Controllers;

use App\Models\TaskComment;
use App\Models\User;
use App\Services\TaskCommentService;
use Auth;
use App\Helper;
use App\Models\Task;
use App\Constants\TasksCategoryConstant;
use App\Constants\TasksStatusConstant;
use App\Services\TaskService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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
//            'qa_user_id' => 'required',
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
                'start' => $date->format('Y-m-d H:i:s'),
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
        $task->deadline = Carbon::parse($request->get('deadline'));
        $task->start_date = Carbon::parse($request->get('start_date'));
        $task->project_id = $request->get('project_id');
        $task->sprint_id = $request->get('sprint_id');
        $task->epic_id = $request->get('epic_id');
        $task->time_planned = $request->get('time_planned');
        $task->time_used = $request->get('time_used');
        $task->priority = $request->get('priority');
        $task->qa_user_id = $request->get('qa_user_id');
        $task->dev_user_id = $request->get('dev_user_id');
        $task->description = $request->get('description');
        $task->user_created_id = Auth::user()->id;

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
        $task->deadline = Carbon::parse($request->get('deadline'));
        $task->start_date = Carbon::parse($request->get('start_date'));
        $task->project_id = $request->get('project_id');
        $task->sprint_id = $request->get('sprint_id');
        $task->epic_id = $request->get('epic_id');
        $task->time_planned = $request->get('time_planned');
        $task->time_used = $request->get('time_used');
        $task->priority = $request->get('priority');
        $task->qa_user_id = $request->get('qa_user_id');
        $task->dev_user_id = $request->get('dev_user_id');
        $task->description = $request->get('description');
        $task->user_updated_id = Auth::user()->id;

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

    public function add_comment($taskId, TaskService $taskService, TaskCommentService $taskCommentService) {
        $task = $taskService->findTaskById($taskId);
        $taskComment = new TaskComment();

        $taskComment->task_id = $taskId;
        $taskComment->comment = request()->get('comment');
        $taskComment->time = request()->get('time', 0);
        $taskComment->type = 1;
        $taskComment->user_created_id = Auth::user()->id;

        if ($taskCommentService->create($taskComment)) {
            $task->time_used = $task->time_used + $taskComment->time;
            $taskService->update($task);
            return response()
                ->json(['msg' => "Comentário adicionado com sucesso!", 'comment' => $taskComment], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível adicionar um comentário, tente novamente mais tarde."], 400);
        }
    }

    public function update_status($id, TaskService $taskService, TaskCommentService $taskCommentService) {
        $task = $taskService->findTaskById($id);

        $status = collect(TasksStatusConstant::getConstants())->values();
        $statusIndex = $status->search($task->status);
        $newStatus = $status[$statusIndex+1];

        if (request()->filled('status'))
            $newStatus = request()->get('status');

        $task->status = $newStatus;
        $task->user_updated_id = Auth::user()->id;

        $taskComment = new TaskComment();

        $taskComment->task_id = $id;
        $taskComment->comment = "Status alterado para: ".$newStatus;
        $taskComment->time = 0;
        $taskComment->type = 2;
        $taskComment->user_created_id = Auth::user()->id;

        if ($taskService->update($task)) {
            $taskCommentService->create($taskComment);
            $taskComment->user = Auth::user()->name;
            return response()
                ->json(['msg' => "Status atualizado com sucesso!", 'task' => $task, 'comment' => $taskComment], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível atualizar o status, tente novamente mais tarde."], 400);
        }
    }

    public function reprove_task($id, TaskService $taskService, TaskCommentService $taskCommentService) {
        $task = $taskService->findTaskById($id);

        $task->status = TasksStatusConstant::PENDING;
        $task->user_updated_id = Auth::user()->id;

        $taskComment = new TaskComment();

        $taskComment->task_id = $id;
        $taskComment->comment = "Tarefa Reprovada";
        $taskComment->time = 0;
        $taskComment->type = 3;
        $taskComment->user_created_id = Auth::user()->id;

        if ($taskService->update($task)) {
            $taskCommentService->create($taskComment);
            $taskComment->user = Auth::user()->name;
            return response()
                ->json(['msg' => "Status atualizado com sucesso!", 'task' => $task, 'comment' => $taskComment], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível atualizar o status, tente novamente mais tarde."], 400);
        }
    }

    public function approve_task($id, TaskService $taskService, TaskCommentService $taskCommentService) {
        $task = $taskService->findTaskById($id);

        if (!request()->expectsJson()) {
            $user = User::first();
        } else {
            $user = Auth::user();
        }

        $task->status = TasksStatusConstant::DONE;
        $task->user_updated_id = $user->id;

        $taskComment = new TaskComment();

        $taskComment->task_id = $id;
        $taskComment->comment = "Tarefa Aprovada!";
        $taskComment->time = 0;
        $taskComment->type = 4;
        $taskComment->user_created_id = $user->id;

        if ($taskService->update($task)) {
//            $taskCommentService->create($taskComment);
            $taskComment->user = $user->name;
            return response()
                ->json(['msg' => "Status atualizado com sucesso!", 'task' => $task, 'comment' => $taskComment], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível atualizar o status, tente novamente mais tarde."], 400);
        }
    }

    public function assign_tasks() {
        Artisan::queue('assign:tasks');

        return response()
            ->json(['msg' => "As tarefas estão sendo analisadas e atribuídas, por favor, aguarde um momento."], 200);
    }
}
