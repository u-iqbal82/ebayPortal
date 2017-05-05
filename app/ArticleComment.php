<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Article;
use App\User;

class ArticleComment extends Model
{
    public function article()
    {
        return $this->belongsTo('App\Article');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
