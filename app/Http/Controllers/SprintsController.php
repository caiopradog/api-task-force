<?php

namespace App\Http\Controllers;

use App\Constants\DefaultStatusConstant;
use App\Services\SprintService;

class SprintsController extends Controller
{
    public function sprints(SprintService $taskService)
    {
        $perPage = request('perPage');

        $sprints = $taskService->list(request()->toArray());

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
}
