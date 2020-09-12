<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class LinkedSocialAccount extends Model
{
    protected $fillable = [
        'provider_name',
        'provider_id',
    ];
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
