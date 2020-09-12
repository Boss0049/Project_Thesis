<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    //
    protected $fillable = [
        'user_id', 'post_id', 'classroom_id', 'title', 'detail', 'due_time', 'role_id', 'group_id'
    ];

    protected $appends = ['type'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function group()
    {
        return $this->belongsTo('App\Group');
    }

    public function groupconnect()
    {
        return $this->belongsToMany('App\Group');
    }

    public function getTypeAttribute()
    {
        return 'assignment';
    }

    public function post()
    {
        return $this->belongsTo('App\Post');
    }

    public function file()
    {
        return $this->hasMany('App\AssignmentFile');
    }

    public function AssignmentCheck()
    {
        return $this->hasMany('App\AssignmentCheck');
    }

    public function reply()
    {
        return $this->hasMany('App\Reply');
    }
}
