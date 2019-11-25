<?php

namespace App\Services;

use App\Models\Reward;
use Cache;
use Mail;
use DB;

/**
 * Class RewardService
 * @package App\Services
 */
class RewardService
{

    /**
     * @var Reward
     */
    public $reward;

    /**
     * RewardService constructor.
     * @param Reward $reward
     */
    public function __construct(Reward $reward)
    {
        $this->reward = $reward;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function query()
    {
        return $this->reward->query();
    }

    /**
     * @param $reward
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findRewardById($reward)
    {
        return $this->query()->find($reward);
    }

    /**
     * @param array $conditions
     * @param boolean $withRelations
     * @return $this|\Illuminate\Database\Eloquent\Builder
     */
    public function list($conditions)
    {
        $search = data_get($conditions, 'search', false);
        $status = data_get($conditions, 'status', false);

        $query = $this->query();

        if ($search) {
            $query = $query->where('name', 'like', "%{$search}%");
        }

        if ($status) {
            $query = $query->where('status', $status);
        }

        return $query;
    }

    /**
     * @param Reward $reward
     * @return bool
     */
    public function create(Reward $reward)
    {
        return $reward->save();
    }

    /**
     * @param Reward $reward
     * @return bool
     */
    public function update(Reward $reward)
    {
        return $this->create($reward);
    }

    /**
     * @param Reward $reward
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Reward $reward)
    {
        return $reward->delete();
    }

}
