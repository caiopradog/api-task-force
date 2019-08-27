<?php

namespace App\Observers;

use Illuminate\Support\Facades\DB;
use App\Models\UserGroup;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserGroupObserver
{

    public function saving(UserGroup $userGroup)
    {
        $old = DB::table('user_groups')->find($userGroup->id);

        if ($old){
            if ($userGroup->name != $old->name){
                Role::where('name', $old->name)->update(['name' => $userGroup->name]);
            }
        }
    }

    public function saved(UserGroup $userGroup)
    {
        try {
            $role = Role::create(['name' => $userGroup->name]);
            $role->givePermissionTo(Permission::all());
        } catch (RoleAlreadyExists $alreadyExists) {
            //não fazer nada
        }
    }

}