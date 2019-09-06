<?php

namespace App\Http\Controllers;

use App\Constants\DefaultStatusConstant;
use App\Services\ProjectService;

class ProjectsController extends Controller
{
    public function projects(ProjectService $taskService)
    {
        $perPage = request('perPage');

        $projects = $taskService->list(request()->toArray());

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
}
