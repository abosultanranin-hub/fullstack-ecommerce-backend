<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Products;


class Store extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'location',  // عمود الموقع
        'capacity',  // عمود السعة
        'description',
        'user_id',

        
    ];
    public function product()
    {
        return $this->hasMany(Product::class);
    }

    



     public function user()
     {
         return $this->belongsTo(User::class);
     }


     
    // هاد اسم الجدول  اذا غيرت اسمالجدول
    protected $table = 'sub_store';
    
    //
}
