<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $appends = [
        'css_class',
        'html_comment'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdUser()
    {
        return $this->belongsTo(User::class, 'user_created_id');
    }

    public function getCssClassAttribute()
    {
        switch ($this->type) {
            case 2: // Mudança de status
                return 'log-primary';
                break;

            case 3: // Reprovação da tarefa
                return 'log-danger';
                break;

            case 4: // Aprovação da tarefa
                return 'log-success';
                break;

            case 5: // Pontuação
                return 'log-scoring';
                break;

            case 1:
            default:
                return '';
                break;
        }
    }

    public function getHtmlCommentAttribute()
    {
        return nl2br($this->comment);
    }
}
