<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use Notifiable;
       use HasFactory;
 protected $fillable = [
        'name',
        'email',
        'password',
        'super_admin',
        'status'
    ];

    /**
     * الحقول التي يجب إخفاؤها عند التحويل إلى مصفوفة أو JSON
     */
    protected $hidden = [
        'password',
    ];
        protected $table = 'admins'; // اسم الجدول الحقيقي

}