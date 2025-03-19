<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

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
}
