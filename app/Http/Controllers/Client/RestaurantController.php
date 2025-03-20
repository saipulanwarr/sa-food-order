<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Product;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;

class RestaurantController extends Controller
{
    public function AllMenu(){
        $menu = Menu::latest()->get();
        return view('client.backend.menu.all_menu', compact('menu'));
    } 

    public function AddMenu(){
        return view('client.backend.menu.add_menu');
    } 

    public function StoreMenu(Request $request){

        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/menu'), $filename);
            $save_url = 'upload/menu/'.$filename;

            Menu::create([
                'menu_name' => $request->menu_name,
                'image' => $save_url, 
            ]);
        }

        $notification = array(
            'message' => 'Menu Inserted successfully',
            'alert-type' => 'success',
        );

        return redirect()->route('all.menu')->with($notification);
    }

    public function EditMenu($id){
        $menu = Menu::find($id);
        return view('client.backend.menu.edit_menu', compact('menu'));
    }

    public function UpdateMenu(Request $request){
        $menu_id = $request->id;
        $data = Menu::find($menu_id);

        $oldPhoto = $data->image;

        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/menu'), $filename);
            $save_url = 'upload/menu/'.$filename;

            if($oldPhoto && $oldPhoto !== $filename){
                $this->deleteOldImage($oldPhoto);
            }

            Menu::find($menu_id)->update([
                'menu_name' => $request->menu_name,
                'image' => $save_url,
            ]);

            $notification = array(
                'message' => 'Menu Updated Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->route('all.menu')->with($notification);
        }else{
            
            Menu::find($menu_id)->update([
                'menu_name' => $request->menu_name, 
            ]); 
            $notification = array(
                'message' => 'Menu Updated Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->route('all.menu')->with($notification);
        }
    }

    private function deleteOldImage(string $oldPhotoPath): void {
        $fullPath = public_path($oldPhotoPath);
        
        if(file_exists($fullPath)){
            unlink($fullPath);
        }
    }

    public function DeleteMenu($id){
        $item = Menu::find($id);
        $img = $item->image;
        unlink($img);

        Menu::find($id)->delete();

        $notification = array(
            'message' => 'Menu Delete Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function AllProduct(){
        $product = Product::latest()->get();
        return view('client.backend.product.all_product', compact('product'));
    }

    public function AddProduct(){
        $category = Category::latest()->get();
        $city = City::latest()->get();
        $menu = Menu::latest()->get();
        return view('client.backend.product.add_product', compact('category','city','menu'));
    } 

    public function StoreProduct(Request $request){

        $pcode = IdGenerator::generate(['table' => 'products','field' => 'code', 'length' => 5, 'prefix' => 'PC']);  

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/product'), $filename);
            $save_url = 'upload/product/'.$filename;

            Product::create([
                'name' => $request->name,
                'slug' => strtolower(str_replace(' ','-',$request->name)),
                'category_id' => $request->category_id,
                'city_id' => $request->city_id,
                'menu_id' => $request->menu_id,
                'code' => $pcode,
                'qty' => $request->qty,
                'size' => $request->size,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'client_id' => Auth::guard('client')->id(),
                'most_populer' => $request->most_populer,
                'best_seller' => $request->best_seller,
                'status' => 1,
                'created_at' => Carbon::now(),
                'image' => $save_url, 
            ]); 
        } 

        $notification = array(
            'message' => 'Product Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.product')->with($notification);        
    }

    public function EditProduct($id){
        $category = Category::latest()->get();
        $city = City::latest()->get();
        $menu = Menu::latest()->get();
        $product = Product::find($id);
        return view('client.backend.product.edit_product', compact('category','city','menu','product'));
    } 

    public function UpdateProduct(Request $request){
        $pro_id = $request->id;
        $data = Product::find($pro_id);

        $oldPhoto = $data->image;
         
        if ($request->file('image')) {

            $file = $request->file('image');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/product'), $filename);
            $save_url = 'upload/product/'.$filename;

            if($oldPhoto && $oldPhoto !== $filename){
                $this->deleteOldImage($oldPhoto);
            }

            Product::find($pro_id)->update([
                'name' => $request->name,
                'slug' => strtolower(str_replace(' ','-',$request->name)),
                'category_id' => $request->category_id,
                'city_id' => $request->city_id,
                'menu_id' => $request->menu_id, 
                'qty' => $request->qty,
                'size' => $request->size,
                'price' => $request->price,
                'discount_price' => $request->discount_price, 
                'most_populer' => $request->most_populer,
                'best_seller' => $request->best_seller, 
                'created_at' => Carbon::now(),
                'image' => $save_url, 
            ]); 

            $notification = array(
                'message' => 'Product Updated Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->route('all.product')->with($notification);

        }else{

            Product::find($pro_id)->update([
                'name' => $request->name,
                'slug' => strtolower(str_replace(' ','-',$request->name)),
                'category_id' => $request->category_id,
                'city_id' => $request->city_id,
                'menu_id' => $request->menu_id, 
                'qty' => $request->qty,
                'size' => $request->size,
                'price' => $request->price,
                'discount_price' => $request->discount_price, 
                'most_populer' => $request->most_populer,
                'best_seller' => $request->best_seller, 
                'created_at' => Carbon::now(), 
            ]); 
            
            $notification = array(
                'message' => 'Product Updated Successfully',
                'alert-type' => 'success'
            );
    
            return redirect()->route('all.product')->with($notification);
        }
    }

    public function DeleteProduct($id){
        $item = Product::find($id);
        $img = $item->image;
        unlink($img);

        Product::find($id)->delete();

        $notification = array(
            'message' => 'Product Delete Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

    }
}
