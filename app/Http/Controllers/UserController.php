<?php

namespace App\Http\Controllers;

use Auth;
use App\Services\UserService;

class UserController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserService $userService)
    {
        $users = $userService->list()->get();

        return response()
            ->json($users);
    }

    public function user($id = false, UserService $userService)
    {
        if (!$id) {
            $user = Auth::user();
        } else {
            $user = $userService->findUserById($id);
        }

        return response()
            ->json($user);
    }
}
