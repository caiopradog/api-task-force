<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;

use App\Observers\DeleteObserver;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\UserStatusConstant;
use App\Services\UserService;
use Mail;
use Storage;

/**
 * Class User
 * @package App\Models
 */
class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasRoles;

    public static function boot() {
        parent::boot();
        self::observe(new DeleteObserver());
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'user_group_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(UserGroup::class);
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
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        app(UserService::class)->sendResetPasswordEmail($this, $token);
    }

    /**
     * @return string
     */
    public function badge()
    {
        switch ($this->status) {
            case UserStatusConstant::ACTIVE:
                return 'badge badge-success';
                break;

            case UserStatusConstant::INACTIVE:
                return 'badge badge-warning';
                break;

            default:
                return 'badge badge-success';
                break;
        }
    }

}
