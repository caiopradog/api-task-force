<?php

namespace App\Services;

use App\Models\UserGroup;
use Cache;
use Mail;
use DB;

/**
 * Class UserGroupService
 * @package App\Services
 */
class UserGroupService
{

    /**
     * @var UserGroup
     */
    public $userGroup;

    /**
     * UserGroupService constructor.
     * @param UserGroup $userGroup
     */
    public function __construct(UserGroup $userGroup)
    {
        $this->user = $userGroup;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function query()
    {
        return $this->user->query();
    }

    /**
     * @param $userGroupId
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findUserGroupById($userGroupId)
    {
        return $this->query()->find($userGroupId);
    }

    /**
     * @param array $search
     * @return $this|\Illuminate\Database\Eloquent\Builder
     */
    public function list(array $search = [])
    {
        $searchString = data_get($search, 'searchString', false);
        $searchStatus = data_get($search, 'searchStatus', false);

        $query = $this->query();

        if ($searchString) {
            $query = $query->where('name', 'like', "%{$searchString}%");
        }

        if ($searchStatus) {
            $query = $query->where('status', $searchStatus);
        }

        return $query;
    }

    /**
     * @param UserGroup $userGroup
     * @return bool
     */
    public function create(UserGroup $userGroup)
    {
        return $userGroup->save();
    }

    /**
     * @param UserGroup $userGroup
     * @return bool
     */
    public function update(UserGroup $userGroup)
    {
        return $this->create($userGroup);
    }

    /**
     * @param UserGroup $userGroup
     * @return bool|null
     */
    public function delete(UserGroup $userGroup)
    {
        return $userGroup->delete();
    }

}
