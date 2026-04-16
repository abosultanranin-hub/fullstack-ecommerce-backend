<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;  // هذا هو السطر المطلوب إضافته

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;


class CategoryController extends Controller
{
    /*
    public function index($doman)
    {
     $newstore = Auth::User();
   

     $store = $newstore->store->categories; 
        
     return redirect()->route('dashboard', ['category' => $store]);



      /* return response()->json([
        'categories' => $categories,
    ]);*/
    
       // return view('store.show', [
        //    categories->$categories,
           // storeid

        //]);*/
      //  dd('hi ');
  

    /**
     * Show the form for creating a new resource.
     */
   

    /**
     * Store a newly created resource in storage.
     */
/*
    public function store(Request $request)
    {
        


    }

    
    public function show(Category $category)
    {
        //
    }

   
    public function edit(Category $category)
    {
        //
    }

    
    public function update(Request $request, Category $category)
    {
        //
    }

    
    public function destroy(Category $category)
    {
        //
    }


*/
public function index()
{
    $categories = Category::with('parent')->get();
    return view('category.index', compact('categories'));
}
    
  
    
    
    public function store(Request $request)
    {
    
        // التحقق من صحة البيانات بما في ذلك slug
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
    
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
            ]);
    
            return redirect()->route('category.index')
                ->with('success', 'تم إنشاء الفئة بنجاح');
    
    }

public function edit(Category $category)
{
    $categories = Category::where('id', '!=', $category->id)->get();
    return view('categories.edit', compact('category', 'categories'));
}

public function delete($id)
{
Ctegory::Delete($id);

}

}