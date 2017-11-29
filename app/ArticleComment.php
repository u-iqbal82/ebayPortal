<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Article;
use App\User;
use App\CommentAnswers;

class ArticleComment extends Model
{
    public function article()
    {
        return $this->belongsTo('App\Article');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User')->withTrashed();
    }
    
    public function answers()
    {
        return $this->hasMany('App\CommentAnswers');
    }
}
