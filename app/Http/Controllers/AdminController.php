<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function AdminLogin(){
        return view('admin.login');
    }

    public function AdminDashboard(){
        return view('admin.admin_dashboard');
    }
}
