<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderAddress extends Model
{
    use HasFactory;

    protected $table = 'order_addresses';
  protected $fillable = [
        'order_id',
        'type',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'street_address',
        'city',
        'postal_code',
        'state',
        'country',
    ];
    public $timestamps = false;

   public function order()
{
    return $this->belongsTo(orders::class, 'order_id');
}

}
