<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\City;

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

    public function DeleteCategory($id){
        $item = Category::find($id);
        $img = $item->image;
        unlink($img);

        Category::find($id)->delete();

        $notification = array(
            'message' => 'Category Deleted successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }

    public function AllCity(){
        $city = City::latest()->get();

        return view('admin.backend.city.all_city', compact('city'));
    }

    public function StoreCity(Request $request){
        City::create([
            'city_name' => $request->city_name,
            'city_slug' => strtolower(str_replace(' ', '-', $request->city_name)),
        ]);

        $notification = array(
            'message' => 'City Inserted successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }

    public function EditCity($id){
        $city = City::find($id);

        return response()->json($city);
    }

    public function UpdateCity(Request $request){
        $cat_id = $request->cat_id;

        City::find($cat_id)->update([
            'city_name' => $request->city_name,
            'city_slug' => strtolower(str_replace(' ', '-', $request->city_name)),
        ]);

        $notification = array(
            'message' => 'City Updated successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }

    public function DeleteCity($id){
        City::find($id)->delete();

        $notification = array(
            'message' => 'City Deleted successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }
}
