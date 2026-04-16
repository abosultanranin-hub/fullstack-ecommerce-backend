<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class order_items extends Model
{
     protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'options',
      
    ];
        public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
