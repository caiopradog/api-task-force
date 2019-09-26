<?php

namespace App\Services;

use App\Models\Epic;
use Cache;
use Mail;
use DB;

/**
 * Class EpicService
 * @package App\Services
 */
class EpicService
{

    /**
     * @var Epic
     */
    public $epic;

    /**
     * EpicService constructor.
     * @param Epic $epic
     */
    public function __construct(Epic $epic)
    {
        $this->epic = $epic;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function query()
    {
        return $this->epic->query();
    }

    /**
     * @param $epic
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findEpicById($epic)
    {
        return $this->query()->find($epic);
    }

    /**
     * @param array $search
     * @return $this|\Illuminate\Database\Eloquent\Builder
     */
    public function list(array $conditions = [])
    {
        $search = data_get($conditions, 'search', false);
        $status = data_get($conditions, 'status', false);
        $projectID = data_get($conditions, 'project_id', false);

        $query = $this->query();

        if ($search) {
            $query = $query->where('name', 'like', "%{$search}%");
        }

        if ($status) {
            $query = $query->where('status', $status);
        }

        if ($projectID) {
            $query = $query->where('project_id', $projectID);
        }

        return $query;
    }

    /**
     * @param Epic $epic
     * @return bool
     */
    public function create(Epic $epic)
    {
        return $epic->save();
    }

    /**
     * @param Epic $epic
     * @return bool
     */
    public function update(Epic $epic)
    {
        return $this->create($epic);
    }

    /**
     * @param Epic $epic
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Epic $epic)
    {
        return $epic->delete();
    }

}
