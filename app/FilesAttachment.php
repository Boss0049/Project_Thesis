<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FilesAttachment extends Model
{
    protected $fillable = [
        'name', 'filepath', 'type', 'user_id'
    ];

    protected $hidden = [
        'id', 'user_id'
    ];

    public function posts()
    {
        return $this->belongsToMany('App\Post');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
