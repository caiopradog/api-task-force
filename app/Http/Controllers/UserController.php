<?php

namespace App\Http\Controllers;

use App\Models\UserSkill;
use App\Services\UserSkillService;
use Auth;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Constants\UserStatusConstant;

class UserController extends Controller
{
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function createValidator(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'status' => 'required|string',
            'password' => 'required|string',
            'user_group_id' => 'required',
        ], [
            'required' => 'Este campo é obrigatório.',
            'date' => 'Este campo deve ser uma data.',
            'email' => 'Este campo deve ter um formato de e-mail.'
        ]);
    }
    protected function editValidator(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'status' => 'required|string',
            'user_group_id' => 'required',
        ], [
            'required' => 'Este campo é obrigatório.',
            'date' => 'Este campo deve ser uma data.',
            'email' => 'Este campo deve ter um formato de e-mail.'
        ]);
    }

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

    public function add_user(Request $request, UserService $userService, UserSkillService $userSkillService) {
        $this->createValidator($request);

        $user = new User();

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->status = $request->get('status');
        $user->password = $request->get('password');
        $user->user_group_id = $request->get('user_group_id');

        if ($userService->create($user)) {
            $skills = $request->get('skills');

            foreach ($skills as $skill => $level) {
                $userSkill = new UserSkill();

                $userSkill->user_id = $user->id;
                $userSkill->skill = $skill;
                $userSkill->level = $level;

                $userSkillService->create($userSkill);
            }

            return response()
                ->json(['msg' => "Usuário cadastrado com sucesso!", 'user' => $user], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível fazer o cadastro, tente novamente mais tarde."], 400);
        }
    }

    public function edit_user($id, Request $request, UserService $userService, UserSkillService $userSkillService) {
        $this->editValidator($request);

        $user = $userService->findUserById($id);

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->status = $request->get('status');
        $user->user_group_id = $request->get('user_group_id');

        if ($userService->update($user)) {
            $skills = $request->get('skills');

            foreach ($skills as $skill => $level) {
                $userSkill = $userSkillService->list(['userId' => $user->id, 'skill' => $skill])->first();
                if (!$userSkill) {
                    $userSkill = new UserSkill();
                }

                $userSkill->user_id = $user->id;
                $userSkill->skill = $skill;
                $userSkill->level = $level;

                $userSkillService->update($userSkill);
            }

            return response()
                ->json(['msg' => "Usuário atualizado com sucesso!", 'user' => $user], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível fazer o cadastro, tente novamente mais tarde."], 400);
        }
    }

    public function user_statuses()
    {
        $statuses = UserStatusConstant::getConstants()->map(function ($item) {
            return [
                'name' => $item,
                'value' => $item,
            ];
        })->values();

        return response()
            ->json($statuses);
    }

    public function user_skills($id = false, UserService $userService)
    {
        if (!$id) {
            $user = Auth::user();
        } else {
            $user = $userService->findUserById($id);
        }

        return response()
            ->json($user->user_skills->pluck('level', 'skill'));
    }
}
