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

    public function EditCategory($id){
        $category = Category::find($id);

        return view('admin.backend.category.edit_category', compact('category'));
    }

    public function UpdateCategory(Request $request){
        $cat_id = $request->id;
        $data = Category::find($cat_id);

        $oldPhoto = $data->image;

        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/category'), $filename);
            $save_url = 'upload/category/'.$filename;

            if($oldPhoto && $oldPhoto !== $filename){
                $this->deleteOldImage($oldPhoto);
            }

            Category::find($cat_id)->update([
                'category_name' => $request->category_name,
                'image' => $save_url,
            ]);

            $notification = array(
                'message' => 'Category Inserted successfully',
                'alert-type' => 'success',
            );
    
            return redirect()->route('all.category')->with($notification);
        }else{
            Category::find($cat_id)->update([
                'category_name' => $request->category_name,
            ]);

            $notification = array(
                'message' => 'Category Updated without image successfully',
                'alert-type' => 'success',
            );
    
            return redirect()->route('all.category')->with($notification);
        }
    }

    private function deleteOldImage(string $oldPhotoPath): void {
        $fullPath = public_path($oldPhotoPath);
        
        if(file_exists($fullPath)){
            unlink($fullPath);
        }
    }
}
