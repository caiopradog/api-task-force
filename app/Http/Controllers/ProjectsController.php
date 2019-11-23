<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Services\ProjectService;
use App\Constants\DefaultStatusConstant;

class ProjectsController extends Controller
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
            'deadline' => 'required|date',
            'description' => 'required|string'
        ], [
            'required' => 'Este campo é obrigatório.',
            'date' => 'Este campo deve ser uma data.'
        ]);
    }

    public function projects(ProjectService $projectService)
    {
        $perPage = request('perPage');

        $projects = $projectService->list(request()->toArray());

        if ($perPage > 0) {
            $projects = $projects->paginate($perPage);
        } else {
            $projects = $projects->get();
        }

        return response()
            ->json($projects);
    }

    public function project($id, ProjectService $projectService)
    {
        $project = $projectService->findProjectById($id);

        return response()
            ->json($project);
    }

    public function project_statuses()
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

    public function add_project(Request $request, ProjectService $projectService) {
        $this->validator($request);

        $project = new Project();

        $project->name = $request->get('name');
        $project->status = $request->get('status');
        $project->deadline = Carbon::parse($request->get('deadline'));
        $project->description = $request->get('description');
        $project->user_created_id = Auth::user()->id;

        if ($projectService->create($project)) {
            return response()
                ->json(['msg' => "Projeto cadastrado com sucesso!", 'project' => $project], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível fazer o cadastro, tente novamente mais tarde."], 400);
        }
    }

    public function edit_project($id, Request $request, ProjectService $projectService) {
        $this->validator($request);

        $project = $projectService->findProjectById($id);

        $project->name = $request->get('name');
        $project->status = $request->get('status');
        $project->deadline = Carbon::parse($request->get('deadline'));
        $project->description = $request->get('description');
        $project->user_created_id = Auth::user()->id;

        if ($projectService->update($project)) {
            return response()
                ->json(['msg' => "Projeto cadastrado com sucesso!", 'project' => $project], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível fazer o cadastro, tente novamente mais tarde."], 400);
        }
    }

    public function delete_project($id, ProjectService $projectService) {
        $project = $projectService->findProjectById($id);

        if ($projectService->delete($project)) {
            return response()
                ->json(['msg' => "Projeto deletado com sucesso!"], 200);
        } else {
            return response()
                ->json(['msg' => "Não foi possível deletar o projeto, tente novamente mais tarde."], 400);
        }
    }
}
