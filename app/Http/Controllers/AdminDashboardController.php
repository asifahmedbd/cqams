<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use Session;

class AdminDashboardController extends Controller
{
    
    public function index(){

        $admin_info = Session::get('admin_info'); 
        return view('admin.dashboard', compact('admin_info'));
    }

    public function adminlogout(Request $request){
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
