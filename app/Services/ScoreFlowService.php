<?php

namespace App\Services;

use App\Models\ScoreFlow;
use Cache;
use Mail;
use DB;

/**
 * Class ScoreFlowService
 * @package App\Services
 */
class ScoreFlowService
{

    /**
     * @var ScoreFlow
     */
    public $scoreFlow;

    /**
     * ScoreFlowService constructor.
     * @param ScoreFlow $scoreFlow
     */
    public function __construct(ScoreFlow $scoreFlow)
    {
        $this->scoreFlow = $scoreFlow;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function query()
    {
        return $this->scoreFlow->query();
    }

    /**
     * @param $scoreFlow
     * @param bool $withRelations
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findScoreFlowById($scoreFlow)
    {
        $query = $this->query();

        return $query->find($scoreFlow);
    }

    /**
     * @param array $conditions
     * @param bool $withRelations
     * @return $this|\Illuminate\Database\Eloquent\Builder
     */
    public function list(array $conditions = [])
    {
        $search = data_get($conditions, 'search', false);
        $type = data_get($conditions, 'type', false);
        $taskId = data_get($conditions, 'taskId', false);
        $rewardId = data_get($conditions, 'rewardId', false);
        $userId = data_get($conditions, 'userId', false);

        $query = $this->query();

        if ($search) {
            $query = $query->where('text', 'like', "%{$search}%");
        }

        if ($type) {
            $query = $query->where('type', $type);
        }

        if ($taskId) {
            $query = $query->whereDate('task_id', $taskId);
        }

        if ($rewardId) {
            $query = $query->whereDate('reward_id', $rewardId);
        }

        if ($userId) {
            $query = $query->whereDate('user_id', $userId);
        }

        return $query;
    }

    /**
     * @param ScoreFlow $scoreFlow
     * @return bool
     */
    public function create(ScoreFlow $scoreFlow)
    {
        return $scoreFlow->save();
    }

    /**
     * @param ScoreFlow $scoreFlow
     * @return bool
     */
    public function update(ScoreFlow $scoreFlow)
    {
        return $this->create($scoreFlow);
    }

    /**
     * @param ScoreFlow $scoreFlow
     * @return bool|null
     */
    public function delete(ScoreFlow $scoreFlow)
    {
        return $scoreFlow->delete();
    }
}
