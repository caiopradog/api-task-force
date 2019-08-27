<?php

namespace App\Models;

use App\Constants\UserGroupStatusConstant;
use App\Observers\DeleteObserver;
use App\Observers\UserGroupObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserGroup
 * @package App\Models
 */
class UserGroup extends Model
{
    use SoftDeletes;

    public static function boot() {
        parent::boot();
        self::observe(new UserGroupObserver());
        self::observe(new DeleteObserver());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return string
     */
    public function label()
    {
        switch ($this->status) {
            case UserGroupStatusConstant::ACTIVE:
                return 'label label-success';
                break;

            case UserGroupStatusConstant::INACTIVE:
                return 'label label-warning';
                break;

            default:
                return 'label label-success';
                break;
        }
    }
}
