<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Category;
use App\Models\orders;
use Illuminate\Support\Str;


class Products extends Model
{

    use HasFactory;  
    //
        protected $table = 'products';

    protected $appends = [
        'image_url',
    ];

     // تحديد الحقول القابلة للتعيين جماعيًا
     protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
         'image',
        'stock_quantity',

      

        
    ];
    // في App\Models\Product
public function store()
{
    return $this->belongsTo(Store::class);
}
 public function orders()
    {
        return $this->belongsToMany(orders::class, 'order_items')
                    ->withPivot(['quantity', 'price', 'totall']);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id','id');
    }
    public function carts()
{
    return $this->hasMany(Cart::class);
}

    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        $path = ltrim($value, '/');

        if (Str::startsWith($path, ['assets/', 'images/', 'img/'])) {
            return asset($path);
        }

        if (! Str::startsWith($path, 'storage/')) {
            $path = 'storage/' . $path;
        }

        return asset($path);
    }

    public function getImageUrlAttribute()
    {
        return $this->image;
    }
    // هاد اسم الجدول  اذا غيرت اسمالجدول
}

                                                                                
