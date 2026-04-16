<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginSession extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'ip', 'created_at'];
}
