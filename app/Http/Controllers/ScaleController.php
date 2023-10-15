<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tqams\Tqams_criteria;
use App\Models\tqams\Tqams_sub_criteria;
use App\Models\tqams\Tqams_field;
use App\Models\Scale_set;
use App\Models\Scale;
use App\Libraries\Common;
use Redirect;
use Alert;
use App\Models\SchoolWiseAcademicSession; 
use Carbon\Carbon;  
use Session;

class ScaleController extends Controller
{
    public function __construct() {
	    //$this->middleware('super-auth');  
	}
	private $viewFolderPath = 'admin/scale/';

    //scale set
    public function index(){
        $breadcrumb = ['Scale Setting','Scale list'];
        $pageName   = "Scale list";
        return view( $this->viewFolderPath.'submenu',compact('breadcrumb','pageName'));
    }
    public function scale(){
        $breadcrumb = ['Scale Setting','Scale list'];
        $pageName   = "Scale list";
        $scale = Scale::get();
        $admin_info = Session::get('admin_info');
        return view( $this->viewFolderPath.'scale_list',compact('breadcrumb','pageName','scale', 'admin_info'));
    }
    public function storeScale(Request $request){
        if(isset($request->sid)){
            $scale = Scale::find($request->sid);
        }else{
            $scale = new Scale();
        }
        $scale->scale_name = $request->name;
        $scale->scale_name_bn = $request->name_bn;
        $scale->scale_point = $request->scales;
        $scale->save();
        Alert::toast('Successfully Added','Success');        
        return redirect('/admin/scale');

    }

    public function deleteScale($id){
        $scale = Scale::find($id)->delete();

        Alert::toast('Successfully deleted','Success');        
        return redirect('/admin/scale');
    }

    public function scaleSetList(){
        $breadcrumb = ['Scale Setting','Scale list'];
        $pageName   = "Scale list";
        $admin_info = Session::get('admin_info');
        $scale = Scale_set::get();
        $scaleList = Scale::get();
        //dd($scale);
        return view( $this->viewFolderPath.'scale_set_list',compact('breadcrumb','pageName','scale','scaleList', 'admin_info'));
    }

    public function storeScaleSet(Request $request){
        if(isset($request->sid)){
            $scale = Scale_set::find($request->sid);
        }else{
            $scale = new Scale_set();
        }
        $scale->name = $request->name;
        $scale->options = json_encode($request->scales);
        $scale->save();
        Alert::toast('Successfully Added','Success');        
        return redirect('/admin/scale-set');

    }

    public function deleteScaleSet($id){
        $scale = Scale_set::find($id)->delete();

        Alert::toast('Successfully deleted','Success');        
        return redirect('/admin/scale-set');
    }
    //end scale set

}
