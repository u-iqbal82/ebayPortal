<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use SoftDeletes { restore as private restoreB; }
    use EntrustUserTrait { restore as private restoreA; }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    protected $dates = ['deleted_at'];
    
    public function restore()
    {
        $this->restoreA();
        $this->restoreB();
    }
    
    public function batches()
    {
        return $this->hasMany('App\Batches');
    }
    
    public function articles()
    {
        return $this->belongsToMany('App\Article');
    }
    
    public function is($roleName)
    {
        foreach ($this->roles()->get() as $role)
        {
            if ($role->name == $roleName)
            {
                return true;
            }
        }

        return false;
    }
}
