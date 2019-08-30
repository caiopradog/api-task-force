<?php

namespace App\Http\Controllers;

use App\Services\UserService;

class AuthController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if (auth()->attempt(['email' => request('email'), 'password' => request('password')])) {
            return auth()->user();
        }

        return response()->json([
            'msg' => 'Usuário ou senha inválidos',
        ], 401);
    }
}
