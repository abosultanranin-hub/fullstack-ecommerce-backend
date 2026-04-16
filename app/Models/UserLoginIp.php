<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLoginIp extends Model
{
    protected $fillable = ['user_id', 'ip', 'last_used_at'];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
