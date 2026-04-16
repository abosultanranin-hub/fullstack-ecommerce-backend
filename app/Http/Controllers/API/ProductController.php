<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // جلب كل المنتجات
    public function index()
    {
        $products = Products::with('category')->get();

        if ($products->isEmpty()) {
            app(DatabaseSeeder::class)->run();
            $products = Products::with('category')->get();
        }

        return response()->json([
            'products' => $products,
        ]);
    }

    // جلب منتج واحد
    public function show(Products $product)
    {
        return response()->json([
            'product' => $product,
        ]);
    }

    // إضافة منتج
    public function store(Request $request)
    {
        $product = Products::create([
            'name'  => $request->name,
            'price' => $request->price,
        ]);

        return response()->json([
            'message' => 'Product created',
            'product' => $product,
        ]);
    }
}
