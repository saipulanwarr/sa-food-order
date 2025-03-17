<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 
use App\Models\Admin;

class AdminController extends Controller
{
    public function AdminLogin(){
        return view('admin.login');
    }

    public function AdminDashboard(){
        return view('admin.index');
    }

    public function AdminLoginSubmit(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $check = $request->all();
        $data = [
            'email' => $check['email'],
            'password' => $check['password'],
        ];

        if(Auth::guard('admin')->attempt($data)){
            return redirect()->route('admin.dashboard')->with('success', 'Login Successfully');
        }else{
            return redirect()->route('admin.login')->with('error', 'Invalid Credentials');
        }
    }

    public function AdminLogout(){
        Auth::guard('admin')->logout();

        return redirect()->route('admin.login')->with('success', 'Logout Successfully');
    }

    public function AdminProfile(){
        $id = Auth::guard('admin')->id();
        $profileData = Admin::find($id);

        return view('admin.admin_profile', compact('profileData'));
    }

    public function AdminProfileStore(Request $request){
        $id = Auth::guard('admin')->id();
        $data = Admin::find($id);

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        $oldPhoto = $data->photo;

        if($request->hasFile('photo')){
            $file = $request->file('photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/admin_images'), $filename);
            $data->photo = $filename;

            if($oldPhoto && $oldPhoto !== $filename){
                $this->deleteOldImage($oldPhoto);
            }
        }

        $data->save();

        $notification = array(
            'message' => 'Profile Updated Successfully',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }

    private function deleteOldImage(string $oldPhotoPath): void {
        $fullPath = public_path('upload/admin_images/'.$oldPhotoPath);
        
        if(file_exists($fullPath)){
            unlink($fullPath);
        }
    }
}
