<?php

namespace App\Services;

use App\Models\Project;
use Cache;
use Mail;
use DB;

/**
 * Class ProjectService
 * @package App\Services
 */
class ProjectService
{

    /**
     * @var Project
     */
    public $project;

    /**
     * ProjectService constructor.
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function query()
    {
        return $this->project->query();
    }

    /**
     * @param $project
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findProjectById($project)
    {
        return $this->query()->find($project);
    }

    /**
     * @param array $search
     * @return $this|\Illuminate\Database\Eloquent\Builder
     */
    public function list(array $conditions = [])
    {
        $search = data_get($conditions, 'search', false);
        $status = data_get($conditions, 'status', false);
        $deadlineStart = data_get($conditions, 'deadlineStart', false);
        $deadlineEnd = data_get($conditions, 'deadlineEnd', false);

        $query = $this->query();

        if ($search) {
            $query = $query->where('name', 'like', "%{$search}%");
        }

        if ($status) {
            $query = $query->where('status', $status);
        }

        if ($deadlineStart && $deadlineEnd) {
            $query = $query->whereBetween('deadline', [$deadlineStart, $deadlineEnd]);
        }

        return $query;
    }

    /**
     * @param Project $project
     * @return bool
     */
    public function create(Project $project)
    {
        return $project->save();
    }

    /**
     * @param Project $project
     * @return bool
     */
    public function update(Project $project)
    {
        return $this->create($project);
    }

    /**
     * @param Project $project
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Project $project)
    {
        return $project->delete();
    }

}
