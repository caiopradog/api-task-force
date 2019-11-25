<?php

namespace App\Http\Controllers;

use App\Constants\DefaultStatusConstant;
use App\Models\Sprint;
use App\Services\SprintService;
use Carbon\Carbon;
use Auth;
use Illuminate\Http\Request;

class SprintsController extends Controller
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
            'project_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'description' => 'required|string'
        ], [
            'required' => 'Este campo é obrigatório.',
            'date' => 'Este campo deve ser uma data.'
        ]);
    }
    public function sprints(SprintService $taskService)
    {
        $perPage = request('perPage');

        $sprints = $taskService->list(request()->toArray(), true);

        if ($perPage > 0) {
            $sprints = $sprints->paginate($perPage);
        } else {
            $sprints = $sprints->get();
        }

        return response()
            ->json($sprints);
    }

    public function sprint($id, SprintService $sprintService)
    {
        $sprint = $sprintService->findSprintById($id);

        return response()
            ->json($sprint);
    }

    public function sprint_statuses()
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

    public function add_sprint(Request $request, SprintService $sprintService) {
        $this->validator($request);

        $sprint = new Sprint();

        $sprint->name = $request->get('name');
        $sprint->status = $request->get('status');
        $sprint->project_id = $request->get('project_id');
        $sprint->start_date = Carbon::parse($request->get('start_date'));
        $sprint->end_date = Carbon::parse($request->get('end_date'));
        $sprint->description = $request->get('description');
        $sprint->user_created_id = Auth::user()->id;

        if ($sprintService->create($sprint)) {
            return response()
                ->json(['msg' => "Sprint cadastrada com sucesso!", 'sprint' => $sprint], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível fazer o cadastro, tente novamente mais tarde."], 400);
        }
    }

    public function edit_sprint($id, Request $request, SprintService $sprintService) {
        $this->validator($request);

        $sprint = $sprintService->findSprintById($id);

        $sprint->name = $request->get('name');
        $sprint->status = $request->get('status');
        $sprint->project_id = $request->get('project_id');
        $sprint->start_date = Carbon::parse($request->get('start_date'));
        $sprint->end_date = Carbon::parse($request->get('end_date'));
        $sprint->description = $request->get('description');
        $epic->user_updated_id = Auth::user()->id;

        if ($sprintService->update($sprint)) {
            return response()
                ->json(['msg' => "Sprint atualizada com sucesso!", 'sprint' => $sprint], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível fazer a atualização, tente novamente mais tarde."], 400);
        }
    }

    public function delete_sprint($id, SprintService $sprintService) {
        $sprint = $sprintService->findSprintById($id);

        if ($sprintService->delete($sprint)) {
            return response()
                ->json(['msg' => "Sprint deletada com sucesso!"], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível deletar o projeto, tente novamente mais tarde."], 400);
        }
    }
}
