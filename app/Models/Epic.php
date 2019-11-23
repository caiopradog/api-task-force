<?php

namespace App\Models;

use App\Constants\DefaultStatusConstant;
use App\Observers\DeleteObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Epic extends Model
{
    protected $appends = [
        'status_color'
    ];

    use SoftDeletes;

    public static function boot() {
        parent::boot();
        self::observe(new DeleteObserver());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return string
     */
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case DefaultStatusConstant::INACTIVE:
                return 'danger';
                break;

            case DefaultStatusConstant::ACTIVE:
            default:
                return 'success';
                break;
        }
    }
}
