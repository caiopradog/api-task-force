<?php

namespace App\Observers;

use Auth;
use Illuminate\Database\Eloquent\Model;

class DeleteObserver
{

    public function deleted(Model $model)
    {
        if (!$model->isForceDeleting() and Auth::check()){
            $model->user_deleted_id = Auth::user()->id;
            $model->save();
        }
    }
}