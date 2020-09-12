<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    //
    protected $fillable = [
        'user_id', 'name',  'join_code', 'year', 'semester', 'grade'
    ];

    protected $hidden = ['assignments', 'posts'];

    public function posts()
    {
        return $this->hasMany('App\Post');
    }

    public function group()
    {
        return $this->hasMany('App\Group');
    }

    public function assignments()
    {
        return $this->hasMany('App\Assignment');
    }

    public function members()
    {
        return $this->belongsToMany('App\User');
    }
}
