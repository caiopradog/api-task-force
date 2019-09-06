<?php

namespace App\Models;

use App\Constants\TasksStatusConstant;
use App\Observers\DeleteObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    public static function boot() {
        parent::boot();
        self::observe(new DeleteObserver());
    }

    public function convertedPlannedTime() {
        $time = $this->time_planned;
        $hours = floor($time / 3600);
        $mins = floor($time / 60 % 60);
        $secs = floor($time % 60);

        return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
    }

    public function convertedUsedTime() {
        $time = $this->time_used;
        $hours = floor($time / 3600);
        $mins = floor($time / 60 % 60);
        $secs = floor($time % 60);

        return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function epic()
    {
        return $this->belongsTo(Epic::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sprint()
    {
        return $this->belongsTo(Sprint::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function devUser()
    {
        return $this->belongsTo(User::class, 'dev_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function qaUser()
    {
        return $this->belongsTo(User::class, 'qa_user_id');
    }

    /**
     * @return string
     */
    public function badge()
    {
        switch ($this->status) {
            case TasksStatusConstant::BACKLOG:
                return 'badge badge-secondary';
                break;

            case TasksStatusConstant::PENDING:
                return 'badge badge-warning';
                break;

            case TasksStatusConstant::DEVELOPING:
                return 'badge badge-primary';
                break;

            case TasksStatusConstant::TESTING:
                return 'badge badge-info';
                break;

            case TasksStatusConstant::DONE:
            default:
                return 'badge badge-success';
                break;
        }
    }
}
