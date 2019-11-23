<?php

namespace App\Services;

use App\Helper;
use App\Models\ScoreFlow;
use App\Models\Task;
use App\Models\TaskComment;
use Cache;
use Carbon\Carbon;
use Auth;
use Mail;
use DB;

/**
 * Class TaskService
 * @package App\Services
 */
class TaskService
{

    /**
     * @var Task
     */
    public $task;

    /**
     * TaskService constructor.
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function query()
    {
        return $this->task->query();
    }

    /**
     * @param $task
     * @param bool $withRelations
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findTaskById($task, $withRelations = false)
    {
        $query = $this->query();

        if ($withRelations) {
            $query = $query
                ->with('sprint:id,name')
                ->with('epic:id,name')
                ->with('taskComments')
                ->with('taskComments.createdUser')
                ->with('devUser:id,name')
                ->with('createdUser:id,name')
                ->with('project:id,name');
        }

        return $query->find($task);
    }

    /**
     * @param array $conditions
     * @param bool $withRelations
     * @return $this|\Illuminate\Database\Eloquent\Builder
     */
    public function list(array $conditions = [], $withRelations = true)
    {
        $search = data_get($conditions, 'search', false);
        $status = data_get($conditions, 'status', false);
        $category = data_get($conditions, 'category', false);
        $deadlineStart = data_get($conditions, 'deadlineStart', false);
        $deadlineEnd = data_get($conditions, 'deadlineEnd', false);
        $plannedDateStart = data_get($conditions, 'plannedDateStart', false);
        $plannedDateEnd = data_get($conditions, 'plannedDateEnd', false);
        $devUserID = data_get($conditions, 'devUserId', false);
        $qaUserID = data_get($conditions, 'qaUserId', false);
        $projectID = data_get($conditions, 'projectId', false);
        $epicID = data_get($conditions, 'epicId', false);
        $sprintID = data_get($conditions, 'sprintId', false);

        $query = $this->query();

        if ($withRelations) {
            $query = $query
                ->with('sprint:id,name')
                ->with('epic:id,name')
                ->with('taskComments')
                ->with('taskComments.createdUser')
                ->with('devUser:id,name')
                ->with('createdUser:id,name')
                ->with('project:id,name');
        }

        if ($search) {
            $query = $query->where('name', 'like', "%{$search}%");
        }

        if ($status) {
            $query = $query->where('status', $status);
        }

        if ($category) {
            $query = $query->where('category', $category);
        }

        if ($deadlineStart && $deadlineEnd) {
            $query = $query->whereBetween('deadline', [$deadlineStart, $deadlineEnd]);
        }

        if ($plannedDateStart && $plannedDateEnd) {
            $query = $query->whereBetween('start_date', [$plannedDateStart, $plannedDateEnd]);
        }

        if ($devUserID) {
            $query = $query->where('dev_user_id', $devUserID);
        }

        if ($qaUserID) {
            $query = $query->where('qa_user_id', $qaUserID);
        }

        if ($projectID) {
            $query = $query->where('project_id', $projectID);
        }

        if ($epicID) {
            $query = $query->where('epic_id', $epicID);
        }

        if ($sprintID) {
            $query = $query->where('sprint_id', $sprintID);
        }

        return $query;
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function create(Task $task)
    {
        if ($task->time_planned && strpos($task->time_planned, ':') !== false) {
            $task->time_planned = Helper::convertTimeToSec($task->time_planned);
        }

        if ($task->time_used && strpos($task->time_used, ':') !== false) {
            $task->time_used = Helper::convertTimeToSec($task->time_used);
        }

        return $task->save();
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function update(Task $task)
    {
        if ($task->status == 'Finalizado') {
            $this->calculateTaskScore($task);
        }

        return $this->create($task);
    }

    /**
     * @param Task $task
     * @return bool|null
     */
    public function delete(Task $task)
    {
        return $task->delete();
    }

    public function calculateTaskScore(Task $task)
    {
        $userService = app('App\Services\UserService');
        $taskCommentService = app('App\Services\TaskCommentService');
        $scoreFlowService = app('App\Services\ScoreFlowService');

        $taskComments = $task->taskComments;
        $user = $task->devUser;

        // Pontos base = horas planejadas * 100
        $basePoints = $task->time_planned/36;
        $positive = 1;
        $negative = 1;
        $text = "O usuário {$user->name} finalizou a tarefa {$task->name}!\nPontos base pela tarefa: {$basePoints} (Horas planejadas * 100)\n";

        // Se acertou na estimativa de horas
        if ($task->time_planned == $task->time_used) {
            $positive += 0.25;
            $text .= "Estimativa de horas correta! :D +25%\n";
        }

        if (Carbon::now() <= $task->deadline) { // Se entregou na data de entrega ou antes
            $dateMultiplier = 0.25 + 0.05*$task->deadline->diffInDays(Carbon::now());
            $text .= "Entregue antes da data de entrega! :D +".($dateMultiplier*100)."% (25% + 5% para cada dia adiantado)\n";
            $positive += $dateMultiplier;
        } else { // Se atrasou a tarefa
            $dateMultiplier = 0.05*$task->deadline->diffInDays(Carbon::now());
            $text .= "Tarefa atrasada... :( -".($dateMultiplier*100)."% (-5% para cada dia atrasado)\n";
            $negative -= $dateMultiplier;
        }

        if ($taskComments->where('type', 3)->count() > 0) { // Pra cada vez que a tarefa foi reprovada
            $reproveMultiplier = 0.2*$taskComments->where('type', 3)->count();
            $text .= "Tarefa reprovada... :( -".($reproveMultiplier*100)."% (-20% para reprovação)\n";
            $negative -= $reproveMultiplier;
        }

        $finalScore = $basePoints*$positive*$negative;
        $text .= "Total: {$finalScore}! ({$basePoints}*".($positive*100)."%*".($negative*100)."%)";

        $user->current_score += $finalScore;
        $user->total_score += $finalScore;

        if ($userService->update($user)) {
            $taskComment = new TaskComment();

            $taskComment->task_id = $task->id;
            $taskComment->comment = $text;
            $taskComment->time = 0;
            $taskComment->type = 5;
            $taskComment->user_created_id = Auth::user()->id;

            $taskCommentService->create($taskComment);

            $scoreFlow = new ScoreFlow();

            $scoreFlow->task_id = $task->id;
            $scoreFlow->text = $text;
            $scoreFlow->score = $finalScore;
            $scoreFlow->type = 1;
            $scoreFlow->user_id = Auth::user()->id;

            $scoreFlowService->create($scoreFlow);
        }
    }
}
