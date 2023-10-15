<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\tqams\Tqams_criteria;
use App\Models\tqams\Tqams_sub_criteria;
use App\Models\tqams\Tqams_field;
use App\Models\Scale;
use App\Models\Scale_set;
use App\Models\tqams\Teacher_satisfaction;
use App\Libraries\Common;
use Redirect;
use Alert;
use App\Models\School;
use App\Models\AcademicSession;
use App\Models\SchoolWiseAcademicSession; 
use App\Models\InstitutionType;
use App\Models\tqams\Cqams_schoolwise_evaluation;
use Carbon\Carbon;
use Session;
use DB;

class TqamsController extends Controller
{
    public function __construct() {
	    //$this->middleware('super-auth');  
	}
	private $viewFolderPath = 'admin/tqams/';

    public function index(){
        $breadcrumb = ['TQAMS','TQAMS'];
    	$pageName   = "TQAMS" ;
    	return view($this->viewFolderPath.'submenu',compact('pageName','breadcrumb'));
    }

    public function addMultiplecriteria(){
        $breadcrumb = ['TQAMS','TQAMS Question Categories'];
        $pageName   = "Question Categories";
        $admin_info = Session::get('admin_info');
        $evaluation_fields = Tqams_criteria::get();
        //dd($evaluation_fields);
        return view( $this->viewFolderPath.'evaluation_criteria_multiple',compact('breadcrumb','evaluation_fields','pageName', 'admin_info'));
    }

    public function getCriteriaField($id){
        $html='';
        for($i=1;$i<=$id;$i++) {
        $html.='<div class="row">';
        $html.='<div class="form-group col-sm-6">';
        $html.='<h5>TQAMS Category '.$i.' (English)</h5>';
            
        $html.='<input type="text" class="form-control title_en" name="criteria_title_en[]" placeholder="Enter Category (en)" required>';            
        $html.='</div>';
        $html.='<div class="form-group col-sm-6">';
        $html.='<h5>TQAMS Category '.$i.' (Bangla)</h5>';
            
        $html.='<input type="text" class="form-control title_bn" name="criteria_title_bn[]" placeholder="Enter Category (bn)" required>';
        $html.='</div>';
        $html.='</div>';
        $html.='</div>';
        $html.='<hr style="margin:0.5rem;">';
        }

        $html.=' <div class="text-xs-right" style="margin-bottom: 20px;">';
        $html.='<button type="submit" class="btn btn-info">Submit</button>';
        $html.=' </div>';
        
        return $html;
    }

    public function storeCriteriaMultiple(Request $request){
        //dd($request);
        //foreach ($request->criteria_title_en as $key => $value) {
        	try{
        		$evaluation_criteria= new Tqams_criteria(); 
	            $evaluation_criteria->criteria_name_en = $request->criteria_title_en;
	            $evaluation_criteria->criteria_name_bn = $request->criteria_title_bn;
	            $evaluation_criteria->save();
        	}
        	catch( Throwable $e){
        		dd($e);
        	}
            
        //}       
        Alert::toast('Successful','Success');     
        return redirect('admin/tqams/add-new-criteria/');
    }

    public function deleteCriteria($id){
        $evaluation_fields=Tqams_criteria::find($id);
        $evaluation_fields->delete();
         Alert::toast('Successfully deleted','Success');        
        return redirect('super-admin/tqams/add-new-criteria/');
    }

    public function updateCriteria(Request $request){
    	if($request->evaluation_row_id){
            $evaluation_criteria= Tqams_criteria::where('evalution_criteria_row_id',$request->evaluation_row_id)->first();
            $evaluation_criteria->criteria_name_en = $request->criteria_title_en;
            $evaluation_criteria->criteria_name_bn = $request->criteria_title_bn;
            $evaluation_criteria->save();
            Alert::toast('Successfully Updated','Success');        
            return redirect('super-admin/tqams/add-new-criteria/');
        }
    }

    public function addMultipleSubcriteria(){
        $breadcrumb = ['TQAMS','TQAMS Sub Categories'];
        $pageName   = "Sub Categories";
        $admin_info = Session::get('admin_info');
        $category = Tqams_criteria::get();
        $evaluation_sub_fields = Tqams_sub_criteria::with('main_category')->get();
        return view( $this->viewFolderPath.'evaluation_sub_criteria_multiple',compact('breadcrumb','category','pageName','evaluation_sub_fields', 'admin_info'));
    }

    public function storeSubCriteriaMultiple(Request $request){
        foreach ($request->criteria_title_en as $key => $value) {
        	try{
        		$evaluation_criteria= new Tqams_sub_criteria(); 
	            $evaluation_criteria->evalution_criteria_row_id = $request->field_type;
	            $evaluation_criteria->criteria_name_en = $value;
	            $evaluation_criteria->criteria_name_bn = $request->criteria_title_bn[$key];
	            $evaluation_criteria->save();
        	}
        	catch( Throwable $e){
        		dd($e);
        	}
            
        }       
        Alert::toast('Successful','Success');     
        return redirect('super-admin/tqams/add-new-sub-criteria/');
    }

    public function updateSubCriteria(Request $request){
    	if($request->evaluation_row_id){
            $evaluation_criteria= Tqams_sub_criteria::where('evalution_sub_criteria_row_id',$request->evaluation_row_id)->first();
            $evaluation_criteria->evalution_criteria_row_id = $request->criteria_type;
            $evaluation_criteria->criteria_name_en = $request->criteria_title_en;
            $evaluation_criteria->criteria_name_bn = $request->criteria_title_bn;
            $evaluation_criteria->save();
            Alert::toast('Successfully Updated','Success');        
            return redirect('super-admin/tqams/add-new-sub-criteria/');
        }
    }

    public function deleteSubCriteria($id){
        $evaluation_fields=Tqams_sub_criteria::find($id);
        $evaluation_fields->delete();
        Alert::toast('Successfully deleted','Success');        
        return redirect('super-admin/tqams/add-new-sub-criteria/');
    }


    public function addMultipleQuestion(){
        $breadcrumb = ['TQAMS','TQAMS Questions'];
        $pageName   = "TQAMS Questions";
        $admin_info = Session::get('admin_info');
        $category = Tqams_criteria::get();
        $evaluation_fields = Tqams_field::with('scale', 'main_category', 'sub_category')->get();
        $scale = Scale_set::get();
        $type = InstitutionType::get();
        $instTypes = array();
        foreach ($type as $data) {
            $instTypes[$data->id] = $data->name;
        }
        //dd($evaluation_fields);

        return view( $this->viewFolderPath.'questions',compact('breadcrumb','evaluation_fields','pageName','category','scale', 'admin_info', 'type', 'instTypes'));
    }

    public function getSubCriteria($id){

        $evaluation_fields = Tqams_sub_criteria::where('evalution_criteria_row_id', $id)->get();
        $html  = "";
        if($id==0){
          // $html .= "<option value='0' selected>All</option>";  
        }
        else{
        foreach($evaluation_fields as $evaluation_criteria) {
            
                $html .= "<option value=".$evaluation_criteria->evalution_sub_criteria_row_id.">".$evaluation_criteria->criteria_name_en."</option>";
            }
        }
        echo $html;
    }
    

    public function getQuestionField($id){
        $html='';
        for($i=1;$i<=$id;$i++) {
        $html.='<div class="row">';
        $html.='<div class="form-group col-sm-6">';
        $html.='<h5>Evaluation Question '.$i.' (English)</h5>';
            
        $html.='<input type="text" class="form-control title_en" name="field_title_en[]" placeholder="Enter Evaluation Question (en)" required>';            
        $html.='</div>';
        $html.='<div class="form-group col-sm-6">';
        $html.='<h5>Evaluation Question '.$i.' (Bangla)</h5>';
            
        $html.='<input type="text" class="form-control title_bn" name="field_title_bn[]" placeholder="Enter Evaluation Question (bn)" required>';
        $html.='</div>';
        $html.='</div>';
        $html.='</div>';
        $html.='<hr style="margin:0.5rem;">';
        }

        // $html.=' <div class="text-xs-right" style="margin-bottom: 20px;">';
        // $html.='<button type="submit" class="btn btn-info">Submit</button>';
        // $html.=' </div>';
        
        return $html;
    }

    public function storeFieldsmultiple(Request $request){
    	try{
    		foreach ($request->field_title_en as $key => $value) {
                $evaluation_fields= new Tqams_field(); 
                $evaluation_fields->field_name_en = $value;
                $evaluation_fields->sub_category_row_id = $request->criteria_type;
                $evaluation_fields->scale_set = $request->scale;
                $evaluation_fields->field_name_bn = $request->field_title_bn[$key];
                $evaluation_fields->evaluation_criteria_row_id = $request->field_type;
                $evaluation_fields->inst_type = $request->inst_type;
                $evaluation_fields->save();
            }   

            Alert::toast('Successfully Added','Success');        
            return redirect('/admin/tqams/add-new-question');
    	}
    	catch(Throwable $e){
    		echo "Something Went Worng. Please contact admin";
    	}
       
    }

    public function updateQuestion(Request $request){
    	if($request->evaluation_row_id){
	    	$evaluation_fields=Tqams_field::find($request->evaluation_row_id);
	    	$evaluation_fields->field_name_en                       = $request->field_title_en;
	    	$evaluation_fields->field_name_bn                       = $request->field_title_bn;
            $evaluation_fields->sub_category_row_id                 = $request->criteria_type;
            $evaluation_fields->scale_set                           = $request->scale;
	    	$evaluation_fields->evaluation_criteria_row_id 		    = $request->field_type;
            $evaluation_fields->inst_type = $request->inst_type;
	    	$evaluation_fields->save();
	    	Alert::toast('Successfully Updated','Success');        
            return redirect('admin/tqams/add-new-question');
    	}
    }

    public function deleteQuestion($id){
    	$evaluation_fields=Tqams_field::find($id);
    	$evaluation_fields->delete();
    	Alert::toast('Successfully Updated','Success');        
        return redirect('/super-admin/tqams/add-new-question');
    }

    public function getQuestionDetails($id){
        $evaluation_fields = Tqams_field::find($id);
        $category = Tqams_criteria::get();
        $sub_category = Tqams_sub_criteria::where('evalution_criteria_row_id', $evaluation_fields->evaluation_criteria_row_id)->get();
        $selected_sub_category = Tqams_sub_criteria::where('evalution_sub_criteria_row_id', $evaluation_fields->sub_category_row_id)->first();
        $scale = Scale_set::get();
        $type = InstitutionType::get();
        $inst_types = ($evaluation_fields->inst_type) ? $evaluation_fields->inst_type : array();
        //dd($selected_sub_category);    
        return view( $this->viewFolderPath.'update_question',compact('evaluation_fields','category','scale', 'sub_category', 'type', 'selected_sub_category', 'inst_types'));
        
    }

    public function getFrom($lang){
        $common_lib = new Common();
        $year = $common_lib->year;
        $breadcrumb = ['TQAMS','TQAMS'];
        $pageName   = "TQAMS";
        $category = Tqams_criteria::get();
        $question = Tqams_field::where('school_row_id', session('school_id'))->where('session_row_id', $session)->get();
        $getExist = Teacher_satisfaction::where('school_id', session('school_id'))->where('session_id', $session)->where('created_by',Auth()->guard('schoolAdmins')->user()->admin_row_id)->first();
        return view( $this->viewFolderPath.'form',compact('breadcrumb','question','pageName','category','lang','session','getExist'));
    
    }

    public function schoolwiseEvaluations(){
        $breadcrumb = ['Schoolwise Evaluation','Schoolwise Evaluation'];
        $pageName   = "Schoolwise Evaluation" ;
        $schools = School::orderby('school_name','asc')->get();
        $common_lib = new Common();
        $admin_info = Session::get('admin_info');
        $all_session = AcademicSession::all();
        $month = $common_lib->month_array;
        $institution_types = InstitutionType::get();
        $instTypes = array();
        foreach ($institution_types as $data) {
            $instTypes[$data->id] = $data->name;
        }
        $all_data = Cqams_schoolwise_evaluation::with('academic_session', 'school_info')->get();
        //dd($all_data);
        return view($this->viewFolderPath.'set_evaluation', compact('schools','admin_info','all_data', 'all_session', 'month', 'instTypes'));
    }

    public function getSchoolTypes($school_id){
        $school_types = School::where('school_id', $school_id)->value('type');
        $stypes = json_decode($school_types, true);
        $institution_types = InstitutionType::get();
        $instTypes = array();
        foreach ($institution_types as $data) {
            $instTypes[$data->id] = $data->name;
        }
        $html = '';
        foreach ($stypes as $key => $value) {
            $html.='<option value="'.$value.'">'.$instTypes[$value].'</option>';
        }

        return $html;
    }

    public function storeSchoolwiseEvaluation(Request $request){
        //dd($request);
        if ($request->isMethod('post')) {
            $eval_settings = new Cqams_schoolwise_evaluation();
            $eval_settings->session_row_id   =  $request->academi_session;
            $eval_settings->school_id        =  $request->school;
            $eval_settings->school_type      =  $request->school_type;
            $eval_settings->eval_lang        =  $request->eval_language;
            $eval_settings->month            =  $request->eval_month;
            $eval_settings->created_by       =  Auth::id();
            $eval_settings->created_at       =  Carbon::now();

            $eval_settings->save();
            toast('Academic Session has been added successfully','success');
        }
        return redirect()->route('schoolwise-evaluations');
    }

    public function submitEvaluation($cse_id){
        $breadcrumb = ['TQAMS','TQAMS QUESTION'];
        $pageName   = "TQAMS QUESTION";
        $subCatgory = [];   
        $question = []; 
        $scaleList = [];   
        $admin_info = Session::get('admin_info');
        $all_data = Cqams_schoolwise_evaluation::with('academic_session', 'school_info')->where('cse_id', $cse_id)->first();
        //dd($all_data);
        $school_type = ''.$all_data['school_type'].'';
        $school_title = $all_data->school_info->school_name;
        $eval_data = '';
        if($all_data['entry_status'] == 1){
            // update request
            $eval_data = Teacher_satisfaction::where('cse_id', $cse_id)->first();
            //dd($eval_data);
        }

        $category = Tqams_criteria::get();
        foreach($category as $row=>$val){
            $subCatgory[$val->evalution_criteria_row_id] = Tqams_sub_criteria::where('evalution_criteria_row_id',$val->evalution_criteria_row_id)->get();
            foreach($subCatgory[$val->evalution_criteria_row_id] as $srow=>$sval){
                $question[$sval->evalution_sub_criteria_row_id] = Tqams_field::where('sub_category_row_id',$sval->evalution_sub_criteria_row_id)->whereJsonContains('inst_type', $school_type)->get();
            }
        }
        
        $allQuestion = Tqams_field::with('scale')->whereJsonContains('inst_type', $school_type)->get();
        //dd($questions);

        foreach($allQuestion as $row=>$val){
            $scaleList[$val->evaluation_field_row_id] = Scale::whereIn('id',json_decode($val->scale->options))->orderBy('scale_point','DESC')->get();
        }

        //dd($scaleList);

        return view( $this->viewFolderPath.'cqams_question',compact('breadcrumb','pageName','category','subCatgory','question','scaleList','admin_info', 'cse_id', 'eval_data', 'all_data', 'school_title'));
    }

    public function submit(Request $request){
        
        if($request->is_update == 1){
            Teacher_satisfaction::where('cse_id', $request->eval_id)->delete();
            Alert::toast('Successfully Updated','Success');  
        }

        $eval_id = $request->eval_id;
        $all_data = Cqams_schoolwise_evaluation::with('academic_session', 'school_info')->where('cse_id', $eval_id)->first();
        //dd($all_data);
        $submission = new Teacher_satisfaction();
        $submission->cse_id =   $eval_id;
        $submission->school_id = $all_data['school_id'];
        $submission->session_id = $all_data['session_row_id'];
        $submission->created_by = Auth::id();
        $submission->feedback = $request->feedback;
        $submission->answer =json_encode($request->answer);
        $submission->save();

        DB::table('cqams_schoolwise_evaluation') ->where('cse_id', $eval_id) ->update(['entry_status'=> 1 ]);
    
        return redirect('/admin/schoolwise-evaluations');
        
    }


}
