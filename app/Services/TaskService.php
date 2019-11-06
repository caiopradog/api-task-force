<?php

namespace App\Services;

use App\Helper;
use App\Models\Task;
use Cache;
use Carbon\Carbon;
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

        if (!Carbon::hasFormat($task->deadline, 'Y-m-d')) {
            $task->deadline = Carbon::createFromTimeString($task->deadline)->format('Y-m-d');
        }

        return $task->save();
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function update(Task $task)
    {
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
}
