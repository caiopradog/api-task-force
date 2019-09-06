<?php

namespace App\Services;

use App\Models\Task;
use Cache;
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
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findTaskById($task)
    {
        return $this->query()->find($task);
    }

    /**
     * @param array $search
     * @return $this|\Illuminate\Database\Eloquent\Builder
     */
    public function list(array $conditions = [])
    {
        $search = data_get($conditions, 'search', false);
        $status = data_get($conditions, 'status', false);
        $category = data_get($conditions, 'category', false);
        $deadlineStart = data_get($conditions, 'deadlineStart', false);
        $deadlineEnd = data_get($conditions, 'deadlineEnd', false);
        $devUserID = data_get($conditions, 'devUser_id', false);
        $qaUserID = data_get($conditions, 'qaUser_id', false);
        $projectID = data_get($conditions, 'project_id', false);
        $epicID = data_get($conditions, 'epic_id', false);
        $sprintID = data_get($conditions, 'sprint_id', false);

        $query = $this->query()
            ->with('sprint:id,name')
            ->with('epic:id,name')
            ->with('project:id,name');

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
