<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;

class PermissionService
{

    /**
     * @var Permission
     */
    private $permission;

    /**
     * PermissionService constructor.
     * @param Permission $permission
     */
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function query()
    {
        return $this->permission->query();
    }

    public function findUserGroupById($userId)
    {
        return $this->query()->find($userId);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function list()
    {
        return $this->query();
    }

}