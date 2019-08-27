<?php

namespace App\Models;

use App\Observers\DeleteObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskTime extends Model
{
    use SoftDeletes;

    public static function boot() {
        parent::boot();
        self::observe(new DeleteObserver());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
