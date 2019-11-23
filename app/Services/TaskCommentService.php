<?php

namespace App\Services;

use App\Helper;
use App\Models\TaskComment;
use Cache;
use Carbon\Carbon;
use Mail;
use DB;

/**
 * Class TaskCommentService
 * @package App\Services
 */
class TaskCommentService
{

    /**
     * @var TaskComment
     */
    public $taskComment;

    /**
     * TaskCommentService constructor.
     * @param TaskComment $taskComment
     */
    public function __construct(TaskComment $taskComment)
    {
        $this->taskComment = $taskComment;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function query()
    {
        return $this->taskComment->query();
    }

    /**
     * @param $taskComment
     * @param bool $withRelations
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findTaskCommentById($taskComment)
    {
        $query = $this->query();

        return $query->find($taskComment);
    }

    /**
     * @param array $conditions
     * @param bool $withRelations
     * @return $this|\Illuminate\Database\Eloquent\Builder
     */
    public function list(array $conditions = [])
    {
        $search = data_get($conditions, 'search', false);
        $type = data_get($conditions, 'type', false);
        $taskId = data_get($conditions, 'taskId', false);

        $query = $this->query();

        if ($search) {
            $query = $query->where('comment', 'like', "%{$search}%");
        }

        if ($type) {
            $query = $query->where('type', $type);
        }

        if ($taskId) {
            $query = $query->whereDate('task_id', $taskId);
        }

        return $query;
    }

    /**
     * @param TaskComment $taskComment
     * @return bool
     */
    public function create(TaskComment $taskComment)
    {
        if ($taskComment->time && strpos($taskComment->time, ':') !== false) {
            $taskComment->time = Helper::convertTimeToSec($taskComment->time);
        } else {
            $taskComment->time = 0;
        }

        return $taskComment->save();
    }

    /**
     * @param TaskComment $taskComment
     * @return bool
     */
    public function update(TaskComment $taskComment)
    {
        return $this->create($taskComment);
    }

    /**
     * @param TaskComment $taskComment
     * @return bool|null
     */
    public function delete(TaskComment $taskComment)
    {
        return $taskComment->delete();
    }
}
