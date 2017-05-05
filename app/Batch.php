<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Article;
use App\User;

class Batch extends Model
{
    public function articles()
    {
        return $this->hasMany('App\Article');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User', 'upload_user_id');
    }
}
