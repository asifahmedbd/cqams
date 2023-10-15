<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\AcademicSession;
use App\Libraries\Common;
use Carbon\Carbon;
use Session;

class AcademicSessionController extends Controller
{
    private $viewFolderPath = 'admin/academic_session/';

    public function index(){
        $breadcrumb = ['Manage Session','Manage Session'];
        $pageName   = "Manage Session";
        $common_lib = new Common();
        $admin_info = Session::get('admin_info');
        $all_session = AcademicSession::all();
        $month = $common_lib->month_array;
        return view($this->viewFolderPath.'session_lists',compact('pageName','breadcrumb','all_session','admin_info','month'));
    }

    public function storeAcademicSession(Request $request){

        if ($request->isMethod('post')) {
            $academic_session_year = $request->academic_session_year;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $month = $request->month;

            $AcademicSession = new AcademicSession();

            $AcademicSession->academic_session_year = $academic_session_year;
            $AcademicSession->month = $month;
            $AcademicSession->start_date = Carbon::createFromFormat('Y-m-d', $start_date);
            $AcademicSession->end_date = Carbon::createFromFormat('Y-m-d', $end_date);
            $AcademicSession->created_by = Auth::id();
            $AcademicSession->created_at = Carbon::now();
            $AcademicSession->save();
            
            toast('Academic Session has been added successfully','success');
        }

        return redirect()->route('manage-session');
    }

}
