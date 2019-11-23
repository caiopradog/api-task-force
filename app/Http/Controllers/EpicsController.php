<?php

namespace App\Http\Controllers;

use App\Constants\DefaultStatusConstant;
use App\Models\Epic;
use App\Services\EpicService;
use Illuminate\Http\Request;
use Auth;

class EpicsController extends Controller
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
            'description' => 'required|string'
        ], [
            'required' => 'Este campo é obrigatório.',
            'date' => 'Este campo deve ser uma data.'
        ]);
    }

    public function epics(EpicService $taskService)
    {
        $perPage = request('perPage');

        $epics = $taskService->list(request()->toArray(), true);

        if ($perPage > 0) {
            $epics = $epics->paginate($perPage);
        } else {
            $epics = $epics->get();
        }

        return response()
            ->json($epics);
    }

    public function epic($id, EpicService $epicService)
    {
        $epic = $epicService->findEpicById($id);

        return response()
            ->json($epic);
    }

    public function epic_statuses()
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

    public function add_epic(Request $request, EpicService $epicService) {
        $this->validator($request);

        $epic = new Epic();

        $epic->name = $request->get('name');
        $epic->status = $request->get('status');
        $epic->project_id = $request->get('project_id');
        $epic->description = $request->get('description');
        $epic->user_created_id = Auth::user()->id;

        if ($epicService->create($epic)) {
            return response()
                ->json(['msg' => "Épico cadastrado com sucesso!", 'epic' => $epic], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível fazer o cadastro, tente novamente mais tarde."], 400);
        }
    }

    public function edit_epic($id, Request $request, EpicService $epicService) {
        $this->validator($request);

        $epic = $epicService->findEpicById($id);

        $epic->name = $request->get('name');
        $epic->status = $request->get('status');
        $epic->project_id = $request->get('project_id');
        $epic->description = $request->get('description');
        $epic->user_created_id = Auth::user()->id;

        if ($epicService->update($epic)) {
            return response()
                ->json(['msg' => "Épico atualizado com sucesso!", 'epic' => $epic], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível fazer o cadastro, tente novamente mais tarde."], 400);
        }
    }

    public function delete_epic($id, EpicService $epicService) {
        $epic = $epicService->findEpicById($id);

        if ($epicService->delete($epic)) {
            return response()
                ->json(['msg' => "Épico deletado com sucesso!"], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível deletar o projeto, tente novamente mais tarde."], 400);
        }
    }
}
