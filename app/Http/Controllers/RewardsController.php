<?php

namespace App\Http\Controllers;

use App\Constants\DefaultStatusConstant;
use App\Models\Reward;
use App\Models\ScoreFlow;
use App\Services\RewardService;
use App\Services\ScoreFlowService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Auth;

class RewardsController extends Controller
{

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'status' => 'required|string',
            'price' => 'required',
            'description' => 'required|string'
        ], [
            'required' => 'Este campo é obrigatório.',
            'date' => 'Este campo deve ser uma data.'
        ]);
    }

    public function rewards(RewardService $taskService)
    {
        $perPage = request('perPage');

        $rewards = $taskService->list(request()->toArray(), true);

        if ($perPage > 0) {
            $rewards = $rewards->paginate($perPage);
        } else {
            $rewards = $rewards->get();
        }

        return response()
            ->json($rewards);
    }

    public function reward($id, RewardService $rewardService)
    {
        $reward = $rewardService->findRewardById($id);

        return response()
            ->json($reward);
    }

    public function reward_statuses()
    {
        $statuses = DefaultStatusConstant::getConstants()->map(function ($item) {
            return [
                'name' => $item,
                'value' => $item,
            ];
        })->values();

        return response()
            ->json($statuses);
    }

    public function buy_reward(Request $request, RewardService $rewardService, UserService $userService, ScoreFlowService $scoreFlowService) {
        $user = $userService->findUserById($request->get('user_id'));
        $reward = $rewardService->findRewardById($request->get('reward_id'));

        if ($reward->price <= $user->current_score) {
            $user->current_score -= $reward->price;

            $scoreFlow = new ScoreFlow();

            $scoreFlow->task_id = null;
            $scoreFlow->text = "O usuário {$user->name} resgatou a recompensa: {$reward->name}!";
            $scoreFlow->score = $reward->price*-1;
            $scoreFlow->type = 2;
            $scoreFlow->user_id = Auth::user()->id;

            if ($scoreFlowService->create($scoreFlow)) {
                $userService->update($user);
                return response()
                    ->json(['msg' => "Recompensa resgatada com sucesso!", 'user' => $user], 200);
            } else {
                return response()
                    ->json(['msg' => "Não foi possível resgatar a recompensa, tente novamente mais tarde."], 400);
            }
        }
    }

    public function add_reward(Request $request, RewardService $rewardService) {
        $this->validator($request);

        $reward = new Reward();

        $reward->name = $request->get('name');
        $reward->status = $request->get('status');
        $reward->price = $request->get('price');
        $reward->description = $request->get('description');
        $reward->user_created_id = Auth::user()->id;

        if ($rewardService->create($reward)) {
            return response()
                ->json(['msg' => "Recompensa cadastrada com sucesso!", 'reward' => $reward], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível fazer o cadastro, tente novamente mais tarde."], 400);
        }
    }

    public function edit_reward($id, Request $request, RewardService $rewardService) {
        $this->validator($request);

        $reward = $rewardService->findRewardById($id);

        $reward->name = $request->get('name');
        $reward->status = $request->get('status');
        $reward->price = $request->get('price');
        $reward->description = $request->get('description');
        $reward->user_updated_id = Auth::user()->id;

        if ($rewardService->update($reward)) {
            return response()
                ->json(['msg' => "Recompensa atualizada com sucesso!", 'reward' => $reward], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível fazer a atualização, tente novamente mais tarde."], 400);
        }
    }

    public function delete_reward($id, RewardService $rewardService) {
        $reward = $rewardService->findRewardById($id);

        if ($rewardService->delete($reward)) {
            return response()
                ->json(['msg' => "Recompensa deletada com sucesso!"], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível deletar o projeto, tente novamente mais tarde."], 400);
        }
    }
}
