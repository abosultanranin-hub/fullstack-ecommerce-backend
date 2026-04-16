<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\classes\storeclothes\storeclothes;
use App\Models\Products;

class ProductController extends Controller
{
    public function index($idcategory)
    {
      
        $catrgoryproduct= categoey::findorfail($idcategory);
    $product=    $catrgoryproduct->product()->get();

        return view('products.index',component($product));
 //لعرض المنتجات



    }




     // حفظ منتج جديد
   
     public function create(Request $request,$idcategory)
     {
       $request->require();

      
Products::create([$request->all()]);


    }

    public function delete($id)
    {
Products::Delete($id);

}
public function search(Request $request)
{
    $keyword = $request->get('search', '');

    $products = Products::where('name', 'LIKE', "%{$keyword}%")
                       ->get(['id', 'name']);  // ترجع فقط الـ id و name
    return response()->json($products);
}

}
