<?php

namespace App\Http\Controllers;

use Auth;
use App\Services\UserGroupService;

class UserGroupController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @param UserGroupService $userGroupService
     * @return \Illuminate\Http\Response
     */
    public function index(UserGroupService $userGroupService)
    {
        $groups = $userGroupService->list()->get();
        
        return response()
            ->json($groups);
    }

    public function userGroup($id = false, UserGroupService $userGroupService)
    {
        if (!$id) {
            $id = Auth::user()->user_group_id;
        }

        $group = $userGroupService->findUserGroupById($id);

        return response()
            ->json($group);
    }
}
