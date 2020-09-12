<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
    protected $fillable = [
        'user_id', 'classroom_id', 'name', 'type', 'join_code', 'path'
    ];

    protected $hidden = ['assignments', 'posts'];

    public function posts()
    {
        return $this->hasMany('App\Post');
    }

    public function assignments()
    {
        return $this->hasMany('App\Assignment');
    }

    public function assignmentsconnect()
    {
        return $this->hasMany('App\Assignment');
    }

    public function members()
    {
        return $this->belongsToMany('App\User');
    }

    public function classroom()
    {
        return $this->belongsTo('App\Classroom');
    }
}
