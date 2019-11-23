<?php

namespace App\Services;

use App\Models\Sprint;
use Cache;
use Mail;
use DB;

/**
 * Class SprintService
 * @package App\Services
 */
class SprintService
{

    /**
     * @var Sprint
     */
    public $sprint;

    /**
     * SprintService constructor.
     * @param Sprint $sprint
     */
    public function __construct(Sprint $sprint)
    {
        $this->sprint = $sprint;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function query()
    {
        return $this->sprint->query();
    }

    /**
     * @param $sprint
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findSprintById($sprint)
    {
        return $this->query()->find($sprint);
    }

    /**
     * @param array $conditions
     * @param boolean $withRelations
     * @return $this|\Illuminate\Database\Eloquent\Builder
     */
    public function list($conditions, $withRelations)
    {
        $search = data_get($conditions, 'search', false);
        $status = data_get($conditions, 'status', false);
        $deadlineStart = data_get($conditions, 'deadlineStart', false);
        $deadlineEnd = data_get($conditions, 'deadlineEnd', false);
        $projectID = data_get($conditions, 'project_id', false);

        $query = $this->query();

        if ($withRelations) {
            $query = $query
                ->with('project');
        }

        if ($search) {
            $query = $query->where('name', 'like', "%{$search}%");
        }

        if ($status) {
            $query = $query->where('status', $status);
        }

        if ($deadlineStart && $deadlineEnd) {
            $query = $query->whereBetween('deadline', [$deadlineStart, $deadlineEnd]);
        }

        if ($projectID) {
            $query = $query->where('project_id', $projectID);
        }

        return $query;
    }

    /**
     * @param Sprint $sprint
     * @return bool
     */
    public function create(Sprint $sprint)
    {
        return $sprint->save();
    }

    /**
     * @param Sprint $sprint
     * @return bool
     */
    public function update(Sprint $sprint)
    {
        return $this->create($sprint);
    }

    /**
     * @param Sprint $sprint
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Sprint $sprint)
    {
        return $sprint->delete();
    }

}
