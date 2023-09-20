<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;

class AdminDashboardController extends Controller
{
    
    public function index(){

        $id = Auth::user()->id;
        $admin_info = User::find($id);
        session(['admin_info' => $admin_info]);
        
        return view('admin.dashboard', compact('admin_info'));
    }

    public function adminlogout(Request $request){
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
