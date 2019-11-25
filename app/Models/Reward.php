<?php

namespace App\Models;

use App\Constants\DefaultStatusConstant;
use App\Observers\DeleteObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
{
    protected $appends = [
        'formatted_price',
        'status_color'
    ];

    use SoftDeletes;

    public static function boot() {
        parent::boot();
        self::observe(new DeleteObserver());
    }

    public function getFormattedPriceAttribute() {
        return number_format($this->price, 0, '', '.');
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
