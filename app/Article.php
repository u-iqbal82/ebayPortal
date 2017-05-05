<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ArticleComment;

class Article extends Model
{
    public function batch()
    {
        return $this->belongsTo('App\Batch');
    }
    
    public function detail()
    {
        return $this->hasOne('App\ArticleDetail');
    }
    
    public function user()
    {
        return $this->belongsToMany('App\User');
    }
    
    public function comments()
    {
        return $this->hasMany('App\ArticleComment');
    }
}
