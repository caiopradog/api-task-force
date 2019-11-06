<?php

namespace App\Services;

use App\Models\UserSkill;
use Cache;
use Mail;
use DB;

/**
 * Class UserSkillService
 * @package App\Services
 */
class UserSkillService
{

    /**
     * @var UserSkill
     */
    public $userSkill;

    /**
     * UserSkillService constructor.
     * @param UserSkill $userSkill
     */
    public function __construct(UserSkill $userSkill)
    {
        $this->userSkill = $userSkill;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function query()
    {
        return $this->userSkill->query();
    }

    /**
     * @param array $conditions
     * @return $this|\Illuminate\Database\Eloquent\Builder
     */
    public function list(array $conditions = [])
    {
        $user_id = data_get($conditions, 'userId', false);
        $skill = data_get($conditions, 'skill', false);

        $query = $this->query();

        if ($user_id) {
            $query = $query->where('user_id', $user_id);
        }

        if ($skill) {
            $query = $query->where('skill', $skill);
        }

        return $query;
    }

    /**
     * @param UserSkill $userSkill
     * @return bool
     */
    public function create(UserSkill $userSkill)
    {
        return $userSkill->save();
    }

    /**
     * @param UserSkill $userSkill
     * @return bool
     */
    public function update(UserSkill $userSkill)
    {
        return $this->create($userSkill);
    }

    /**
     * @param UserSkill $userSkill
     * @return bool|null
     */
    public function delete(UserSkill $userSkill)
    {
        return $userSkill->delete();
    }

}
