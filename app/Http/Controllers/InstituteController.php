<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Student;
use App\Models\Admin;
use App\Models\District;
use App\Models\Division;
use App\Models\Upazila;
use App\Models\InstitutionType;
use App\Models\EducationBoard;
use Alert;
use Hash;
use validate;
use Session;

class InstituteController extends Controller
{
    public function __construct() {
	    //$this->middleware('super-auth');  
	}
	private $viewFolderPath = 'admin/institute/';

	public function institutionList(){
		$breadcrumb = ['Institution','Institution List'];
    	$pageName   = "Institution" ;
    	$school = School::orderby('school_name','asc')->get();
    	$admin_info = Session::get('admin_info'); 
    	$institution_types = InstitutionType::get();
    	$instTypes = array();
    	foreach ($institution_types as $data) {
    		$instTypes[$data->id] = $data->name;
    	}
    	//dd($instTypes);
		return view($this->viewFolderPath.'/instituteList',compact('breadcrumb','pageName','school', 'admin_info', 'instTypes'));
	}

	public function addInstitutionForm(){
		$breadcrumb = ['Institution','Add New Institution'];
    	$pageName   = "Institution" ;
    	$division = Division::orderby('name','asc')->get();
    	$eduBoard = EducationBoard::orderby('board_title','asc')->get();
    	$type = InstitutionType::get();
    	$otherSchool = School::where([['is_active',1]])->get();
    	$admin_info = Session::get('admin_info'); 
		return view($this->viewFolderPath.'/instituteForm',compact('breadcrumb','pageName','otherSchool','division','eduBoard', 'admin_info', 'type'));
	}

	public function institutionSave(Request $request){

		$request->validate([
            'name_bn'=>'required',
            'name_en'=>'required',
            'inst_type'=>'required',
            'division'=>'required',
            'district'=>'required',
            'upazila'=>'required',
            'email'=>'required',
            'mobile'=>'required'
        ]);
		$admin_info = Session::get('admin_info'); 
		if (isset($request->osid)) {
			$institute = School::findorfail($request->osid);
			$schoolId = $institute->school_id;
			$institute->updated_by = $admin_info->id;
    		Alert::toast('Institute Information Updated successfully', 'success');
		}else{
			//dd($request);
			$institute = new School();
			$institute->created_by = $admin_info->id;
    		Alert::toast('New institute added successful', 'success');

			if (strlen($request->division)==1) {
				$divisionId = "0".$request->division;
			}else $divisionId = $request->division;
			if (strlen($request->district)==1) {
				$districtId = "0".$request->district;
			}else $districtId = $request->district;
			$oldSchool = School::orderby('created_at','DESC')->first();
			if($oldSchool){
				$sid = intval(substr($oldSchool->school_id,4))+1;
				$totalzeero= 4-strlen($sid);
	            $zero='';
	            if ($totalzeero>0) {
	                for ($i=0; $i <$totalzeero ; $i++) { 
	                    $zero = $zero.'0';
	                }
	            }
	            $sid = $zero.$sid;
			}
			else $sid ="0001";

			$schoolId = $divisionId.$districtId.$sid;
		}
		if ($files = $request->file('logo')) {
	        $path = public_path('images/'.$schoolId.'/school_logo');
	        $file = $schoolId.'.'.$files->getclientoriginalextension();
	        $files->move($path, $file);
	        //$institute->school_logo  = $file;                  
       	}
       	if ($files = $request->file('school_img')) {
	        $path = public_path('images/'.$schoolId.'/school_img');
	        $file = $schoolId.'.'.$files->getclientoriginalextension();
	        $files->move($path, $file);
	        //$institute->school_image  = $file;                  
       }
		$institute->school_id = $schoolId;
		$institute->school_encrypted_row_id = Hash::make($request->name_bn);
		$institute->school_name_bn = $request->name_bn;
		$institute->school_name = $request->name_en;
		$institute->school_mobile_no = $request->mobile;
		$institute->school_phone_no = $request->tel;
		$institute->fax_no = $request->fax;
		$institute->website = $request->website;
		$institute->category = $request->category;
		$institute->school_email = $request->email;
		$institute->school_board = $request->school_board;
		$institute->school_slogan = $request->slogan_en;
		$institute->school_slogan_bangla = $request->slogan_bn;
		$institute->school_address = $request->full_address;
		$institute->school_eiin_id = $request->eiin;
		$institute->division_id = $request->division;
		$institute->district_id = $request->district;
		$institute->upazila_id = $request->upazila;
		$institute->post_code = $request->postal_code;
		$institute->type = json_encode($request->inst_type);
		//$institute->package = $request->package;
		$institute->school_establishment_date = $request->es_date;
		//$institute->parent_institute = $request->branch;
		//$institute->active_date = $request->active_date;
		$institute->save();
		return redirect('/admin/institutionList');

	}

	public function institutionEdit($id){
		$breadcrumb = ['Institution','Institution Edit'];
    	$pageName   = "Institution";
    	$school = School::findorfail($id);
    	$division = Division::orderby('name','asc')->get();
    	$district = District::where('division_id',$school->division_id)->orderby('full_name','asc')->get();
    	$upazila = Upazila::where('district_id',$school->district_id)->orderby('full_name','asc')->get();
    	$eduBoard = EducationBoard::orderby('board_title','asc')->get();
   		$admin_info = Session::get('admin_info'); 
    	$type = InstitutionType::get();
    	$otherSchool = School::where([['is_active',1]])->get();

		return view($this->viewFolderPath.'/instituteForm',compact('breadcrumb','pageName','otherSchool','type','division','eduBoard','school','district','upazila','admin_info'));
		
	}

	public function getDistrict($id){
		$district = District::where('division_id',$id)->orderby('full_name','asc')->get();
		$html = "";
		$html .= "<option value='' Selected>Select District</option>";
		foreach ($district as $key => $value) {
			$html .= "<option value='".$value->id."'>".$value->full_name."</option>";
		}
		return $html;
	}

	public function getUpazila($id){
		$upazila = Upazila::where('district_id',$id)->orderby('full_name','asc')->get();
		$html = "";
		$html .= "<option value='' Selected>Select Thana/Upazila</option>";
		foreach ($upazila as $key => $value) {
			$html .= "<option value='".$value->id."'>".$value->full_name."</option>";
		}
		return $html;
	}

	public function activeStatus($said,$type){
    	$school = School::findorfail($said);
    	$school->is_active = $type;
    	$school->save();
    	Alert::toast('Active status updated successfully', 'success');
		return redirect('/super-admin/institutionList');
    	
    }

}
