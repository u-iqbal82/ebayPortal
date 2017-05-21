<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArticleComment;

class CommentAnswers extends Model
{
    public function comment()
    {
        return $this->belongsTo('App\ArticleComment');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
