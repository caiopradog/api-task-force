<?php

namespace App\Http\Controllers;

use App\Constants\DefaultStatusConstant;
use App\Services\EpicService;

class EpicsController extends Controller
{
    public function epics(EpicService $taskService)
    {
        $perPage = request('perPage');

        $epics = $taskService->list(request()->toArray());

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
}
