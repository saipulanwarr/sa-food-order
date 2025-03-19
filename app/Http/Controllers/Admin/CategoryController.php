<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class CategoryController extends Controller
{
    public function AllCategory(){
        $category = Category::latest()->get();

        return view('admin.backend.category.all_category', compact('category'));
    }

    public function AddCategory(){
        return view('admin.backend.category.add_category');
    }

    public function StoreCategory(Request $request){

        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/category'), $filename);
            $save_url = 'upload/category/'.$filename;

            Category::create([
                'category_name' => $request->category_name,
                'image' => $save_url,
            ]);
        }

        $notification = array(
            'message' => 'Category Inserted successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.category')->with($notification);
    }
}
