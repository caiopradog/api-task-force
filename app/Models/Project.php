<?php

namespace App\Models;

use App\Constants\DefaultStatusConstant;
use App\Observers\DeleteObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    protected $appends = [
        'status_color'
    ];

    protected $dates = [
        'deadline'
    ];

    use SoftDeletes;

    public static function boot() {
        parent::boot();
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
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
