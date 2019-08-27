<?php

namespace App\Models;

use App\Observers\DeleteObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
{
    use SoftDeletes;

    public static function boot() {
        parent::boot();
        self::observe(new DeleteObserver());
    }
    //
}
