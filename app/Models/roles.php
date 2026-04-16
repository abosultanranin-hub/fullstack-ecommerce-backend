<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class roles extends Model
{
    //
    
      // هنا تحدد الحقول المسموح تعيينها مباشرة (mass assignable)
    protected $fillable = ['name', 'permissions'];

    
    protected $casts = [
    'permissions' => 'array',  // يحول تلقائياً حقل JSON إلى مصفوفة عند الجلب
];
public function users()
{
    return $this->belongsToMany(User::class);
}

}
 