<?php

namespace App\Http\Controllers\Api;

use App\Services\UserGroupService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserGroupController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserGroupService $useGrouprService)
    {
        $users = $useGrouprService->list()->get();
        
        return response()
            ->json($users);
    }
}
