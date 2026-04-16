<?php

namespace App\Http\Controllers;
use App\Models\Carts;
use App\Models\Products;
use App\Http\Middleware\EncryptCookies
;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;


class CarttController extends Controller
{
    

    public function view()
    {
        $products = Products::all();
        $count = Carts::count(); // أو أي طريقة أخرى تحصل بها على عدد العناصر
       // return
        // return view('cart.cart', compact('products', 'count'));
    }
public function store()
{
   
    
    return Carts::savec();
 
}
}


