<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function Index(){
        return view('frontend.index');
    }

    public function ProfileStore(Request $request){
        $id = Auth::user()->id;
        $data = User::find($id);

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        $oldPhoto = $data->photo;

        if($request->hasFile('photo')){
            $file = $request->file('photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/user_images'), $filename);
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
        $fullPath = public_path('upload/user_images/'.$oldPhotoPath);
        
        if(file_exists($fullPath)){
            unlink($fullPath);
        }
    }

    public function UserLogout(){
        Auth::guard('web')->logout();

        return redirect()->route('login')->with('success', 'Logout Successfully');
    }

    public function ChangePassword(){
        return view('frontend.dashboard.change_password');
    }

    public function UserPasswordUpdate(Request $request){
        $user = Auth::guard('web')->user();
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        if(!Hash::check($request->old_password, $user->password)){
            $notification = array(
                'message' => 'Old Password does not match',
                'alert-type' => 'error'
            );

            return back()->with($notification);
        }

        User::whereId($user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $notification = array(
            'message' => 'Password change successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
}
