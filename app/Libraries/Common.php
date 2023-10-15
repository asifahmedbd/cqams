<?php
namespace App\Libraries;
use Illuminate\Support\Facades\Input;
use Image;
use DB;
use File;
use App\Models\Section;
use App\Models\SubjectTeacher;
use App\Models\MasterAssetHead;
use App\Models\MasterClass;
use App\Models\MasterSection;
use App\Models\MasterShift;
use App\Models\ChildAssetHead;
use App\Models\MasterEducationGroup;
use App\Models\SchoolWiseAcademicSession;
use App\Models\SchoolWiseSection;
use App\Models\AcademicSession;
use App\Models\Student;
use App\Models\SchoolwiseMasterTerm;
use App\Models\ExamMarksWeight;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\teacher_evaluation\EvaluationSettings;
use App\Models\teacher_evaluation\EvaluationQuestionSettings;
use App\Models\teacher_evaluation\EvaluationQuestionSet;
use App\Models\teacher_evaluation\EvaluationSubmition;
use App\Models\Subject;
use App\Models\AssetFloor;
use App\Models\Asset;
use App\Models\AssetAllocation20;
use App\Models\AssetRoom;
use App\Models\CentralAssetStatus;
use App\Models\CentralAssetUnit;




class Common {
	public $output = array();
	public $year = array(
		'1' => '2019',
		'2' => '2020',
		'3' => '2021',
		'4' => '2022',
		'5' => '2023',
		'6' => '2024',
		'7' => '2025',
		'8' => '2026',
		'9' => '2027',
		'10' => '2028',
		'11' => '2029',
		'12' => '2030',
		);

	public $month_array = array(
		'1' => 'January',
		'2' => 'February',
		'3' => 'March',
		'4' => 'April',
		'5' => 'May',
		'6' => 'June',
		'7' => 'July',
		'8' => 'August',
		'9' => 'September',
		'10' => 'October',
		'11' => 'November',
		'12' => 'December'
		);
	    public $alphabet_array = array(
        '1' => 'a',
        '2' => 'b',
        '3' => 'c',
        '4' => 'd',
        '5' => 'e',
        '6' => 'f',
        '7' => 'g',
        '8' => 'h',
        '9' => 'i',
        '10' => 'j',
        '11' => 'k',
        '12' => 'l',
        '13' => 'm',
        '14' => 'n',
        '15' => 'o',
        '16' => 'p',
        '17' => 'q',
        '18' => 'r',
        '19' => 's',
        '20' => 't',
        '21' => 'u',
        '22' => 'v',
        '23' => 'w',
        '24' => 'x',
        '25' => 'y',
        '26' => 'z'
    );
    public $roman_array = array(
        '1' => 'I',
        '2' => 'II',
        '3' => 'III',
        '4' => 'IV',
        '5' => 'V',
        '6' => 'VI',
        '7' => 'VII',
        '8' => 'VIII',
        '9' => 'IX',
        '10' => 'X',
        '11' => 'XI',
        '12' => 'XII',
        '13' => 'XIII',
        '14' => 'XIV',
        '15' => 'XV',
        '16' => 'XVI',
        '17' => 'XVII',
        '18' => 'XVIII',
        '19' => 'XIX',
        '20' => 'XX',
        '21' => 'XXI',
        '22' => 'XXII',
        '23' => 'XXIII',
        '24' => 'XXIV',
        '25' => 'XXV',
        '26' => 'XXVI'
	);

	public function allCommentCategory() {
		$comment_category = array(
			'1' => 'Outstanding',
			'2' => 'Excellent',
			'3' => 'Very Good',
			'4' => 'Good',
			'5' => 'Satisfactory',
			'6' => 'Not Satisfactory',
			'7' => 'Pass',
			'8' => 'Fail'
			);
		return $comment_category;
	}
    //Assessment Form Comment
    public $assessment_form_comment = array(
        '1' => 'Excellent',
        '2' => 'Very Good',
        '3' => 'Good',
        '4' => 'Satisfactory',
        '5' => ' Need to Improve',
        );
	public function getAcademicSessionList(){
		$session_list = DB::table('academic_session')
							->join('school_wise_academic_session','academic_session.academic_session_year','=','school_wise_academic_session.academic_session_year')
							->where('academic_session.status', 1)
							->orderBy('school_wise_academic_session.academic_session_year', 'desc')
							->get();

		return $session_list;
	}
	/*public function getAcademicShiftList(){
		$shift_list = \App\Models\Shift::where('status', 1)->orderBy('academic_session_row_id', 'desc')->get();
		return $shift_list;
	}*/

	function monthNameEnBn($monthIndex) {
		$MonthName = array();	
	    switch ($monthIndex) {
	        case 1 :
	            $MonthName['en'] = 'January';
	            $MonthName['bn'] = 'জানুয়ারি';
	            break;

	        case 2 :
	            $MonthName['en'] = 'February';
	            $MonthName['bn'] = 'ফেব্রুয়ারী';
	            break;

	        case 3 :
	            $MonthName['en'] = 'March';
	            $MonthName['bn'] = 'মার্চ';
	            break;

	        case 4 :
	            $MonthName['en'] = 'April';
	            $MonthName['bn'] = 'এপ্রিল';
	            break;

	        case 5 :
	            $MonthName['en'] = 'May';
	            $MonthName['bn'] = 'মে';
	            break;

	        case 6 :
	            $MonthName['en'] = 'June';
	            $MonthName['bn'] = 'জুন';
	            break;

	        case 7 :
	            $MonthName['en'] = 'July';
	            $MonthName['bn'] = 'জুলাই';
	            break;

	        case 8 :
	            $MonthName['en'] = 'August';
	            $MonthName['bn'] = 'আগষ্ট';
	            break;

	        case 9 :
	            $MonthName['en'] = 'September';
	            $MonthName['bn'] = 'সেপ্টেম্বর';
	            break;

	        case 10 :
	            $MonthName['en'] = 'October';
	            $MonthName['bn'] = 'অক্টোবর';
	            break;

	        case 11 :
	            $MonthName['en'] = 'November';
	            $MonthName['bn'] = 'নভেম্বর';
	            break;

	        case 12 :
	            $MonthName['en'] = 'December';
	            $MonthName['bn'] = 'ডিসেম্বর';
	            break;

	        default:
	            $MonthName['en'] = '';
	            $MonthName['bn'] = '';
    	}


    return $MonthName;
}

	public function getDistrict($id){
		$district = \App\Models\District::where('division_id',$id)->orderby('full_name','asc')->get();
		$html = "";
		$html .= "<option value='' Selected>Select District</option>";
		foreach ($district as $key => $value) {
			$html .= "<option value='".$value->id."'>".$value->full_name."</option>";
		}
		return $html;
	}

	public function getUpazila($id){
		$upazila = \App\Models\Upazila::where('district_id',$id)->orderby('full_name','asc')->get();
		$html = "";
		$html .= "<option value='' Selected>Select Thana/Upazila</option>";
		foreach ($upazila as $key => $value) {
			if(isset($id) && ($value->id == $id)) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			$html .= "<option value='".$value->id."'>".$value->full_name."</option>";
		}
		return $html;
	}

	public function getAcademicSessionRow($academic_session_row_id){
		$session_row = \App\Models\AcademicSession::where('academic_session_row_id', $academic_session_row_id)->first();
		return $session_row;
	}

	public function getSessionWiseGroup($academic_session_row_id){
		$group = array();
		$session = SchoolWiseAcademicSession::findorfail($academic_session_row_id);
		$session_row = \App\Models\SchoolWiseEducationGroup::where([['school_id',session('school_id')],['academic_session_year',$session->academic_session_year],['month',$session->month]])->get();
		if(count($session_row)>0){
			foreach ($session_row as $key => $value) {
				$group[] = $value->master_group_row_id;
			}
		}
		return $group;
	}

	public function getAcademicShiftRow($academic_shift_row_id){
		$shift_row = \App\Models\Shift::where('shift_row_id', $academic_shift_row_id)->first();
		return $shift_row;
	}
	public function getAcademicSectiontRow($academic_section_row_id){
		$section_row = \App\Models\Section::where('section_row_id', $academic_section_row_id)->first();
		return $section_row;
	}
	public function getAcademicVersionRow($version_row_id){
		$version_row = \App\Models\EducationVersion::where('version_row_id', $version_row_id)->first();
		return $version_row;
	}

	public function getEducationVersionList(){
		$version_list = DB::table('education_version')->get();

		return $version_list;
	}

	public function getSectionByClass($class,$version, $session,$section=null){
		$master_section = MasterSection::get();
		$schoolSession = SchoolWiseAcademicSession::findorfail($session);
		$availableSsections = array();

		if($schoolSession){
			$school_wise_version = SchoolWiseSection::where([['school_id',session('school_id')],['master_class_row_id',$class],['version_row_id',$version],['academic_session_year',$schoolSession->academic_session_year],['is_deleted',0]])->get();
		}else{
			$school_wise_version = SchoolWiseSection::where([['school_id',session('school_id')],['master_class_row_id',$class],['version_row_id',$version],['is_deleted',0]])->get();
		}

		foreach ($school_wise_version as $key => $value) {
			$availableSsections[] = $value->master_section_row_id;
		}

		$html = "";
		$html .= "<option value=''>Select Section</option>";
		foreach ($master_section as $ms => $s) {
			$dis = '';
			if(isset($section) && ($s->master_section_row_id == $section)) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
				if(in_array($s->master_section_row_id, $availableSsections)){
					$dis = 'disabled="disabled"';
				}
			}

			$html .= "<option value=".$s->master_section_row_id." ".$selected." ".$dis.">".$s->section."</option>";
		}
		return $html;
	}

	public function getSchoolWiseEducationVersionList($academic_session_year, $month){
		$session_row =
		$school_id = session('school_id');
		$version_list = DB::table('education_version as ev')
									->join('school_wise_version as swv','ev.version_row_id','=','swv.version_row_id')
									->where([['swv.school_id',$school_id],['ev.status',1],['swv.academic_session_year',$academic_session_year],['swv.month',$month]])
									->select('ev.version_title','ev.version_row_id','swv.school_wise_version_row_id','swv.academic_session_year','swv.school_id','swv.month')
									->get();
		return $version_list;
	}

	public function getVersionListBySession($session, $current_version = null){
		$session_row = SchoolWiseAcademicSession::where('school_wise_academic_session_row_id',$session)->first();
		$school_id = session('school_id');
		$version_list = DB::table('education_version as ev')
									->join('school_wise_version as swv','ev.version_row_id','=','swv.version_row_id')
									->where([['swv.school_id',$school_id],['ev.status',1],['swv.academic_session_year',$session_row->academic_session_year],['swv.month',$session_row->month]])
									->select('ev.version_title','ev.version_row_id','swv.school_wise_version_row_id','swv.academic_session_year','swv.school_id','swv.month')
									->get();
		$html = "<option value=''>Select Version</option>";
		if(isset($version_list) && !empty($version_list)){
			foreach($version_list as $version_row){
				if(isset($current_version) && !empty($current_version) && ($version_row->version_row_id == $current_version)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$version_row->version_row_id."' ".$selected.">".$version_row->version_title."</option>";
			}
		}
		return $html;
	}

	public function getVersionListBySessionAndSchool($session, $school_id = null){
		$session_row = SchoolWiseAcademicSession::where('school_wise_academic_session_row_id',$session)->first();
		$version_list = DB::table('education_version as ev')
									->join('school_wise_version as swv','ev.version_row_id','=','swv.version_row_id')
									->where([['swv.school_id',$school_id],['ev.status',1],['swv.academic_session_year',$session_row->academic_session_year],['swv.month',$session_row->month]])
									->select('ev.version_title','ev.version_row_id','swv.school_wise_version_row_id','swv.academic_session_year','swv.school_id','swv.month')
									->get();
		$html = "<option value=''>Select Version</option>";
		if(isset($version_list) && !empty($version_list)){
			foreach($version_list as $version_row){
				if(isset($current_version) && !empty($current_version) && ($version_row->version_row_id == $current_version)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$version_row->version_row_id."' ".$selected.">".$version_row->version_title."</option>";
			}
		}
		return $html;
	}

	public function getVersionListBySessionArr($session, $school_id = null){
		$session_row = SchoolWiseAcademicSession::where('school_wise_academic_session_row_id',$session)->first();
		$version_list = DB::table('education_version as ev')
									->join('school_wise_version as swv','ev.version_row_id','=','swv.version_row_id')
									->where([['swv.school_id',$school_id],['ev.status',1],['swv.academic_session_year',$session_row->academic_session_year],['swv.month',$session_row->month]])
									->select('ev.version_title','ev.version_row_id','swv.school_wise_version_row_id','swv.academic_session_year','swv.school_id','swv.month')
									->get();
		$versionArr = [];
		if(isset($version_list) && !empty($version_list)){
			foreach($version_list as $version_row){
				$versionArr[] = $version_row->version_row_id;
			}
		}
		return $versionArr;
	}

	public function getSessionStartDate($session){

		return SchoolWiseAcademicSession::where('school_wise_academic_session_row_id',$session)->pluck('start_date')->first();

	}

	public function getShiftList(){
		$school_id = session('school_id');
		$shift_list = DB::table('master_shifts')
							->join('school_wise_shift','master_shifts.master_shift_row_id','=','school_wise_shift.master_shift_row_id')
							// ->join('class_wise_shift','school_wise_shift.school_wise_shift_row_id','=','class_wise_shift.school_shift_row_id')
							->where([['school_wise_shift.is_active', 1],['school_id',$school_id]])->orderBy('school_wise_shift_row_id', 'desc')
							->get();
		return $shift_list;
	}

	public function getSectionList(){
		$school_id = session('school_id');
		$section_list = DB::table('master_sections')
							->join('school_and_class_wise_sections','master_sections.master_section_row_id','=','school_and_class_wise_sections.master_section_row_id')
							->join('master_classes','master_classes.master_class_row_id','=','school_and_class_wise_sections.master_class_row_id')
							->join('education_version','education_version.version_row_id','=','school_and_class_wise_sections.version_row_id')
							->where([['school_and_class_wise_sections.is_deleted', 0],['school_id',$school_id]])
							->orderBy('school_and_class_wise_sections.school_wise_section_row_id', 'desc')
							->get();

		return $section_list;
	}

	public function updateActiveSessionOnDelete($academic_session_row_id){
		$session_list = \App\Models\EducationVersion::where([['active_session_id', $academic_session_row_id]])->orderBy('sort_order', 'desc')->get();
		if(isset($session_list) && !empty($session_list)){
			foreach($session_list as $Version){
				$Version->active_session_id = 0;
				$Version->save();
			}
		}
		return true;
	}

	/*
	* Return all version string list for a
	* session in active any
	*/
	public function getVersionListByActiveSession($academic_session_row_id){
		$version_array = [];
		$version_list = \App\Models\EducationVersion::where([['active_session_id', $academic_session_row_id]])->orderBy('sort_order', 'desc')->get();
		foreach($version_list as $version){
			$version_array[] = $version->version_title;
		}
		$version_string = implode(',', $version_array);
		return $version_string;
	}

	/*
	* Return session wise
	* all version name list of any active session
	*/

	public function getVersionListOnActiveSession(){
		$this->output = array();
		$session_list = $this->getAcademicSessionList();
		if(isset($session_list) && !empty($session_list)){
			foreach($session_list as $session){
				$this->output[$session->academic_session_row_id] = $this->getVersionListByActiveSession($session->academic_session_row_id);
			}
		}
		$output =  $this->output;
		$this->output = array();
		return $output;
	}

	/*
	* Return version wise
	* all version name list with active session
	*/
	public function getactiveSessionList(){
		$this->output = array();
		$session_list = \App\Models\EducationVersion::orderBy('sort_order', 'desc')->get();
		if(isset($session_list) && !empty($session_list)){
			foreach($session_list as $session){
				if(isset($session->active_session_id) && !empty($session->active_session_id)){
					$this->output[$session->version_row_id] = $session->active_session_id;
				}
			}
		}
		$output =  $this->output;
		$this->output = array();
		return $output;
	}

	public function getDistricts($divisionid, $presentdist = NULL) {
		$alldistricts = DB::table('districts')->select('id','full_name')->where('division_id', $divisionid)->orderBy('full_name', 'asc')->get();
		$html = "";
		$html .= "<option value='0'>Select District</option>";
		foreach($alldistricts as $districts) {
			if(isset($presentdist) && ($districts->id == $presentdist)) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}

			$html .= "<option value=".$districts->id." ".$selected.">".$districts->full_name."</option>";
		}
		echo $html;
	}

	public function getUpazilas($districtid, $presentupazila = NULL) {
		$allupazilas = \App\Models\Upazila::where('district_id',$districtid)->orderby('full_name','asc')->get();
		$html = "";
		$html .= "<option value='0'>Select Upazila</option>";
		foreach($allupazilas as $upazilas) {
			if(isset($presentupazila) && ($upazilas->id == $presentupazila)) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			$html .= "<option value=".$upazilas->id." ".$selected.">".$upazilas->full_name."</option>";
		}
		echo $html;
	}

	public function getPreprimeryClass(){
		$classes_list_group1 = DB::table('master_classes')->where('school_group', 1)->orderBy('sort_order')->get();
		return $classes_list_group1;
	}
	public function getPrimeryClass(){
		$classes_list_group2 = DB::table('master_classes')->where('school_group', 2)->orderBy('sort_order')->get();
		return $classes_list_group2;
	}

	public function getSeconderyClass(){
		$classes_list_group3 = DB::table('master_classes')->where('school_group', 3)->orderBy('sort_order')->get();
		return $classes_list_group3;
	}
	public function getHigherSeconderyClass(){
		$classes_list_group4 = DB::table('master_classes')->where('school_group', 4)->orderBy('sort_order')->get();
		return $classes_list_group4;
	}
	public function getEnglishMediumHigherClass(){
		$classes_list_group5 = DB::table('master_classes')->where('school_group', 5)->orderBy('sort_order')->get();
		return $classes_list_group5;
	}

	/*
	* Get academic class record by version
	*/

	public function getCurrentAcademicClassRowByVersion($school_id, $version_row_id,$session){
		$getSession = SchoolWiseAcademicSession::findorfail($session);
		$academic_class_row = \App\Models\SchoolWiseClass::where([['version_row_id', $version_row_id],['academic_session_year',$getSession->academic_session_year],['month',$getSession->month],['school_id',$school_id]])->first();
		return $academic_class_row;
	}

	/*
	* Get current class By Version
	*/
	public function getCurrentAcademicClassByVersion($version_row_id,$session){
		$school_classes = array();
		$getSession = SchoolWiseAcademicSession::findorfail($session);
		$school_id = session('school_id');

		$academic_class = DB::table('school_wise_classes')->where([['version_row_id', $version_row_id],['academic_session_year',$getSession->academic_session_year],['month',$getSession->month],['school_id',$school_id]])->first();
		if(isset($academic_class->master_class_row_id) && !empty($academic_class->master_class_row_id)){
			$school_classes = json_decode($academic_class->master_class_row_id);
		}
		return $school_classes;
	}

	public function getCurrentAcademicClass($session,$school_id,$version_row_id){
		$school_classes = array();
		$getSession = SchoolWiseAcademicSession::findorfail($session);
		$academic_class = DB::table('school_wise_classes')->where([['version_row_id', $version_row_id],['academic_session_year',$getSession->academic_session_year],['month',$getSession->month],['school_id',$school_id]])->first();
		if(isset($academic_class->master_class_row_id) && !empty($academic_class->master_class_row_id)){
			$school_classes = json_decode($academic_class->master_class_row_id);
		}
		return $school_classes;
	}
	public function getMasterClassList($class_list){
		$classes_list = DB::table('master_classes')->whereIn('class_row_id', $class_list)->orderBy('sort_order')->get();
		return $classes_list_group4;
	}
	/*
	* Get class check box list
	*/
	function getClassByVersion($version_row_id, $session){
		//echo $version_row_id.$session;
		$school_classes = $this->getCurrentAcademicClassByVersion($version_row_id, $session);
		$classes_list_group1 = $this->getPreprimeryClass();
    	$classes_list_group2 = $this->getPrimeryClass();
    	$classes_list_group3 = $this->getSeconderyClass();
    	$classes_list_group4 = $this->getHigherSeconderyClass();
    	$classes_list_group5 = $this->getEnglishMediumHigherClass();


    	/* First Level */
    	$html = "";
    	foreach($classes_list_group1 as $group1) {
			$html .= "<fieldset><input type='checkbox' name='school_classes[]' id='checkbox_".$group1->master_class_row_id."' value='".$group1->master_class_row_id."'";
			if(isset($school_classes) && !empty($school_classes) && in_array($group1->master_class_row_id, $school_classes)){
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}
			$html .= $checked."/><label for='checkbox_".$group1->master_class_row_id."'>".$group1->class_name.
				"</label></fieldset>";
		}
		$html = "<div class='col-md-3'><div class='form-group'><div class='controls'>".$html."</div></div></div>";

		/* Second Level */
		$html1 = "";
		foreach($classes_list_group2 as $group2) {
			$html1 .= "<fieldset><input type='checkbox' name='school_classes[]' id='checkbox_".$group2->master_class_row_id."' value='".$group2->master_class_row_id."'";
			if(isset($school_classes) && is_array($school_classes) && in_array($group2->master_class_row_id, $school_classes)){
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}
			$html1 .= $checked."/><label for='checkbox_".$group2->master_class_row_id."'>".$group2->class_name.
				"</label></fieldset>";
		}
		$html1 = "<div class='col-md-3'><div class='form-group'><div class='controls'>".$html1."</div></div></div>";

		/* Third Level */
		$html2 = "";
		foreach($classes_list_group3 as $group3) {
			$html2 .= "<fieldset><input type='checkbox' name='school_classes[]' id='checkbox_".$group3->master_class_row_id."' value='".$group3->master_class_row_id."'";
			if(isset($school_classes) && !empty($school_classes) && in_array($group3->master_class_row_id, $school_classes)){
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}
			$html2 .= $checked."/><label for='checkbox_".$group3->master_class_row_id."'>".$group3->class_name.
				"</label></fieldset>";
		}
		$html2 = "<div class='col-md-3'><div class='form-group'><div class='controls'>".$html2."</div></div></div>";

		/* Fourth Level */
		$html3 = "";
		foreach($classes_list_group4 as $group4) {
			$html3 .= "<fieldset><input type='checkbox' name='school_classes[]' id='checkbox_".$group4->master_class_row_id."' value='".$group4->master_class_row_id."'";
			if(isset($school_classes) && is_array($school_classes) && in_array($group4->master_class_row_id, $school_classes)){
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}
			$html3 .= $checked."/><label for='checkbox_".$group4->master_class_row_id."'>".$group4->class_name.
				"</label></fieldset>";
		}
		$html3 = "<div class='col-md-3'><div class='form-group'><div class='controls'>".$html3."</div></div></div>";

		/* Fifth Level */
		$html4 = "";
		foreach($classes_list_group5 as $group5) {
			$html4 .= "<fieldset><input type='checkbox' name='school_classes[]' id='checkbox_".$group5->master_class_row_id."' value='".$group5->master_class_row_id."'";
			if(isset($school_classes) && is_array($school_classes) && in_array($group5->master_class_row_id, $school_classes)){
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}
			$html4 .= $checked."/><label for='checkbox_".$group5->master_class_row_id."'>".$group5->class_name.
				"</label></fieldset>";
		}
		$html4 = "<div class='col-md-3'><div class='form-group'><div class='controls'>".$html4."</div></div></div>";

		echo $html.$html1.$html2.$html3.$html4;
	}

	public function getSchoolShiftList($version_row_id){
		$current_academic_session_row_id = get_active_academic_session_row_id_By_version_row_id($version_row_id);
		$school_shift_list = DB::table('school_shifts')->where([['version_row_id', $version_row_id], ['academic_session', $current_academic_session_row_id]])->get();
		return $school_shift_list;
	}

	public function getClasswiseCurrentShiftListByClass($version_row_id, $academic_session_row_id, $class_row_id = NULL){
		$school_classwise_shift = array();
		if(isset($class_row_id) && !empty($class_row_id)){
			$classwise_shift = \App\Models\ClassShift::select('shift_row_id')->where([['version_row_id', $version_row_id], ['academic_session', $academic_session_row_id],['class_row_id', $class_row_id]])->get();
			foreach($classwise_shift as $shift){
				$school_classwise_shift[$shift->shift_row_id] = $shift->shift_row_id;
			}
		} else {
			$school_classwise_shift = \App\Models\ClassShift::where([['version_row_id', $version_row_id], ['academic_session', $academic_session_row_id]])->get();
		}

		return $school_classwise_shift;
	}

	public function clearClasswiseShiftByVersion($version_row_id){
		$current_academic_session_row_id = get_active_academic_session_row_id_By_version_row_id($version_row_id);
		$all_shift = $this->getClasswiseCurrentShiftListByClass($version_row_id, $current_academic_session_row_id, NULL);
		if(isset($all_shift) && !empty($all_shift)){
			foreach($all_shift as $class_wise_shift_row){
				$class_wise_shift_row->delete();
			}
		}
	}

	public function getClassWiseShiftByVersion($version_row_id){
		$school_class_list = getSchoolClasses($version_row_id);
		$current_academic_session_row_id = get_active_academic_session_row_id_By_version_row_id($version_row_id);
		$shift_list = $this->getSchoolShiftList($version_row_id);

		$html = "<div class='box-body no-padding'><div class='table-responsive'><table class='table table-striped table-bordered table-hover'><tr><th>Class</th><th>Shifts</th>";

		if(isset($school_class_list) && !empty($school_class_list)){
			foreach($school_class_list as $class_row){

				$class_wise_current_shift_list = $this->getClasswiseCurrentShiftListByClass($version_row_id, $current_academic_session_row_id, $class_row->class_row_id);

				$html .="<tr><td>".$class_row->class_name."</td><td><fieldset>";
				foreach($shift_list as $shift_row){
					if(isset($class_wise_current_shift_list) && in_array($shift_row->shift_row_id, $class_wise_current_shift_list)){
						$checked = 'checked="checked"';
					} else {
						$checked = '';
					}
					$html .= "<input type='checkbox' name='class_shifts_".$class_row->class_row_id.
					"[]' id='checkbox_".$class_row->class_row_id.$shift_row->shift_row_id."' value='".$shift_row->shift_row_id."' ".$checked."/><label style='padding-right:10px;' for='checkbox_".$class_row->class_row_id.$shift_row->shift_row_id."'>".$shift_row->shift_title.
					"</label>";
				}
				$html .="</fieldset></td></tr>";
				unset($class_wise_current_shift_list);
			}
		}
		$html .="</table></div></div>";
		return $html;
	}

	public function getClassListByVersion($version_row_id, $session, $current_class = NULL){
		$school_class_list = getSchoolClasses($version_row_id, $session);
		$html = "<option value=''>Select Class</option>";
		if(isset($school_class_list) && !empty($school_class_list)){
			foreach($school_class_list as $class_row){
				if(isset($current_class) && !empty($current_class) && ($class_row->master_class_row_id == $current_class)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$class_row->master_class_row_id."' ".$selected.">".$class_row->class_name."</option>";
			}
		}
		return $html;
	}

	public function getClassListByInstitutionAndSessionAndVersion($scid, $sid, $vid){
		$session_row = DB::table('school_wise_academic_session')->where('school_wise_academic_session_row_id',$sid)->first();
		$school_id = $scid;

        $school_class_list = DB::table('school_wise_classes')
                            ->where([['school_id',$school_id],['version_row_id',$vid],['academic_session_year',$session_row->academic_session_year],['month',$session_row->month]])
                            ->first();

        if(!$school_class_list){
            return false;
        }else{
            $classList = json_decode($school_class_list->master_class_row_id);
            $school_class_list = DB::table('master_classes')->whereIn('master_class_row_id', $classList)->orderBy('sort_order', 'ASC')->get();
        }

		$html = "<option value=''>Select Class</option>";
		if(isset($school_class_list) && !empty($school_class_list)){
			foreach($school_class_list as $class_row){
				if(isset($current_class) && !empty($current_class) && ($class_row->master_class_row_id == $current_class)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$class_row->master_class_row_id."' ".$selected.">".$class_row->class_name."</option>";
			}
		}
		return $html;
	}

	public function getGroupListByInstitutionAndSessionAndVersionAndClass($scid, $sid, $vid, $cid){
		if($vid != 4 && ($cid == 9 || $cid == 10 || $cid == 11 || $cid == 12)){
			$is_general = 0;
		} else {
			$is_general = 1;
		}

		$session_row = getSchoolWiseSessionRow($sid);
        $school_id = $scid;
        if(isset($is_general)){
            $query = DB::table('master_education_groups AS meg')
            ->join('school_wise_education_groups as sweg','meg.master_group_row_id','=','sweg.master_group_row_id')
            ->select('meg.*')
            ->where([['meg.is_general', $is_general],['sweg.school_id',$school_id],
					['sweg.academic_session_year',$session_row->academic_session_year],
					['sweg.month',$session_row->month]])
            ->orderBy('meg.sort_order', 'asc');
        } else {
            $query = DB::table('master_education_groups AS meg')
            ->join('school_wise_education_groups as sweg','meg.master_group_row_id','=','sweg.master_group_row_id')
            ->select('meg.*')
            ->where([['sweg.school_id',$school_id],['sweg.academic_session_year',$session_row->academic_session_year],
					['sweg.month',$session_row->month]])
            ->orderBy('meg.sort_order', 'asc');
        }

        $allgroups = $query->get();

		$education_group_list = $allgroups;
		$html = "<option value=''>Select Group</option>";
		if(isset($education_group_list) && !empty($education_group_list)){
			foreach($education_group_list as $group_row){
				if(isset($current_group_row_id) && !empty($current_group_row_id) && ($group_row->master_group_row_id == $current_group_row_id)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$group_row->master_group_row_id."' ".$selected.">".$group_row->group_title."</option>";
			}
		}
		return $html;
	}

	public function getSectionListByInstitutionAndSessionAndVersionAndClassAndGroup($scid, $sid, $vid, $cid, $gid){
		$session_row = DB::table('school_wise_academic_session')->where('school_wise_academic_session_row_id',$sid)->first();
        $school_id = $scid;
        $query = DB::table('master_sections AS ms')
            ->join('school_and_class_wise_sections as scws','ms.master_section_row_id','=','scws.master_section_row_id')
            ->where([['scws.school_id',$school_id],['scws.master_class_row_id',$cid],
					['scws.academic_session_year',$session_row->academic_session_year],
					['scws.month',$session_row->month],['scws.version_row_id',$vid],
					['scws.master_group_row_id', $gid], ['scws.is_deleted', 0]])
            ->select('ms.master_section_row_id','ms.section','scws.section_title','scws.school_wise_section_row_id',
					'scws.academic_session_year')
            // ->orderBy('scws.school_wise_section_row_id', 'asc');
            ->orderBy('scws.school_wise_section_row_id', 'asc');
        $allsections = $query->get();

		$school_class_wise_section_list = $allsections;
		$html = "<option value=''>Select Section</option>";
		if(isset($school_class_wise_section_list) && !empty($school_class_wise_section_list)){
			foreach($school_class_wise_section_list as $section_row){
				if(isset($current_section) && !empty($current_section) && ($section_row->master_section_row_id == $current_section)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$section_row->master_section_row_id."' ".$selected.">".$section_row->section_title."</option>";
			}
		}
		return $html;
	}

	public function getStudentListByInstitutionAndSessionAndVersionAndClassAndGroup($scid, $sid, $vid, $shid, $cid, $gid, $secid){

		$month = $this->month_array;
		$academic_session_list = SchoolWiseAcademicSession::where([['school_id',$scid],['is_deleted',0],['is_active',1]])->get();

		$academic_session_info = getAcademicSessionInfo($sid);

		$academic_department_array = config('site_config.academic_department');
		$class = MasterClass::where('master_class_row_id', $cid)->first();
		if (isset($class) && $class != null) {
			$class_name = $class->class_name;
		}else{
			$class_name = '';
		}
		$shift = MasterShift::where('master_shift_row_id', $shid)->first();
		if (isset($shift) && $shift != null) {
			$shift_title = $shift->shift_title;
		}else{
			$shift_title = '';
		}
		$section_name = get_section_name_By_section_row_id($secid, $cid);
		$department_name = $academic_department_array[$gid];

		//DB::enableQueryLog();

		$query = DB::table('students AS std')
				->leftjoin('master_classes AS mc', 'mc.master_class_row_id', '=', 'std.current_class')
				->leftjoin('class_wise_shift AS scs', 'scs.class_wise_shift_row_id', '=', 'std.current_shift')
				->leftjoin('school_wise_shift AS sft', 'sft.school_wise_shift_row_id', '=', 'scs.class_wise_shift_row_id')
				->leftjoin('school_wise_academic_session AS session', 'session.school_wise_academic_session_row_id', '=', 'std.academic_session_year')
				->leftjoin('school_and_class_wise_sections AS ss',function ($join) {
					$join->on('ss.master_section_row_id', '=', 'std.current_section')
					->on('ss.version_row_id', '=', 'std.academic_version')
					->on('ss.master_class_row_id', '=', 'std.current_class')
					->on('ss.master_group_row_id', '=', 'std.current_department')
					->on('ss.master_section_row_id', '=', 'std.current_section' )
					->on('ss.academic_session_year', '=', 'session.academic_session_year' )
					->where('ss.is_deleted', 0)
					->where('ss.school_id',session('school_id'));
				})
				->leftjoin('students_details AS sd', 'std.student_row_id', '=', 'sd.student_row_id')
				->leftjoin('users AS user', 'std.student_row_id', '=', 'user.user_id')
				// ->select('std.student_row_id as studentRowid', 'std.*', 'mc.*', 'ss.*','sd.*','user.*')
				->select('std.student_row_id as studentRowid','user.name')
				->where('std.is_deleted', '=', 0)
				->where('std.active_status', '=', 1)
				->where('std.academic_session_year', '=', $sid)
				->where('std.academic_version', '=', $vid)
				->where('std.current_class', '=', $cid)
				->where('std.current_shift', '=', $shid)
				->where('std.current_section', '=', $secid)
				->where('std.current_department', '=', $gid)
				->where('std.school_id', '=', $scid)
				->orderBy('std.current_rollnumber', 'ASC');

		// $current_version = $vid;
		// $current_class = $request->academic_class;
		// $current_shift = $request->academic_shift;
		// $current_section = $request->academic_section;
		// $current_department = $request->academic_department;
		// $current_session = $request->academic_session;

		$student_list = $query->get();
		// $total_student = $query->count();

		$html = "<option value=''>Select Student</option>";
		if(isset($student_list) && !empty($student_list)){
			foreach($student_list as $student){

				$html .="<option value='".$student->studentRowid."'>".$student->studentRowid." - ".$student->name."</option>";

				// if($class_row->session_type == 2){
				// 	if(isset($current_class) && !empty($current_class) && ($class_row->class_row_id == $current_class)){
				// 			$selected = 'selected="selected"';
				// 		} else {
				// 			$selected = '';
				// 		}
				// }
			}
		}

		return $html;
	}

	public function getOptionalSubjectClassListByVersion($version_row_id, $current_class = NULL){
		$school_class_list = getSchoolClasses($version_row_id);
		$html = "<option value=''>Select Class</option>";
		if(isset($school_class_list) && !empty($school_class_list)){
			foreach($school_class_list as $class_row){
				if($class_row->session_type == 2){
					if(isset($current_class) && !empty($current_class) && ($class_row->class_row_id == $current_class)){
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}

					$html .="<option value='".$class_row->class_row_id."' ".$selected.">".$class_row->class_name."</option>";
				}
			}
		}
		return $html;
	}


	public function getSessionListByClass($class_row_id, $current_session = NULL){
		$class_session_type = get_class_session_type_By_class_row_id($class_row_id);
		$session_list = getSessionListByType($class_session_type);
		$html = "<option value=''>Select Session</option>";
		if(isset($session_list) && !empty($session_list)){
			foreach($session_list as $session_row){
				if(isset($current_session) && !empty($current_session) && ($session_row->academic_session_row_id == $current_session)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$session_row->academic_session_row_id."' ".$selected.">".$session_row->academic_session_title."</option>";
			}
		}
		return $html;
	}

	public function getSessionListByInstitution($sid){

		$academic_session_list = SchoolWiseAcademicSession::where([['school_id',$sid],['is_deleted',0],['is_active',1]])->get();
		$html = "<option value=''>Select Session</option>";
		if(isset($academic_session_list) && !empty($academic_session_list))
			foreach($academic_session_list as $key => $row){
				$html .= "<option value=".$row->school_wise_academic_session_row_id.">
							".$row->academic_session_year."-".$this->month_array[$row->month]."
						</option>";
			}

		$year = $row->academic_session_year;

		return json_encode($params = [
							$html,
							$year
						]);
	}

	public function getVersionListByInstitutionAndSession($scid, $sid){
		$session_row = SchoolWiseAcademicSession::where('school_wise_academic_session_row_id',$sid)->first();
		$school_id = $scid;
		$version_list = DB::table('education_version as ev')
									->join('school_wise_version as swv','ev.version_row_id','=','swv.version_row_id')
									->where([['swv.school_id',$school_id],['ev.status',1],['swv.academic_session_year',$session_row->academic_session_year],['swv.month',$session_row->month]])
									->select('ev.version_title','ev.version_row_id','swv.school_wise_version_row_id','swv.academic_session_year','swv.school_id','swv.month')
									->get();
		$html = "<option value=''>Select Version</option>";
		if(isset($version_list) && !empty($version_list)){
			foreach($version_list as $version_row){
				if(isset($current_version) && !empty($current_version) && ($version_row->version_row_id == $current_version)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$version_row->version_row_id."' ".$selected.">".$version_row->version_title."</option>";
			}
		}
		return $html;
	}

	public function getShiftListByVersion($version_row_id,$session_id, $current_shift = NULL){
		$school_class_wise_shift_list = getSchoolClassewiseShiftList($version_row_id,$session_id);
		$html = "";
		$html = "<option value=''>Select Shift</option>";
		if(isset($school_class_wise_shift_list) && !empty($school_class_wise_shift_list)){
			foreach($school_class_wise_shift_list as $shift_row){
				if(isset($current_shift) && ($shift_row->master_shift_row_id == $current_shift)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$shift_row->master_shift_row_id."' ".$selected.">".$shift_row->shift_title."</option>";
			}
		}
		return $html;
	}

	public function getShiftListByInstitutionAndSessionAndVersion($scid, $sid, $vid){
		$session_row = DB::table('school_wise_academic_session')->where('school_wise_academic_session_row_id',$sid)->first();
		$school_id = $scid;
        $school_class_wise_shift_list = DB::table('master_shifts as ms')
            ->join('school_wise_shift as sws','ms.master_shift_row_id','=','sws.master_shift_row_id')
            ->where([['sws.version_row_id',$vid],['sws.is_active',1],
					['sws.school_id',$school_id],['sws.academic_session_year',$session_row->academic_session_year],
					['sws.month',$session_row->month]])
            ->select('ms.shift_title','ms.master_shift_row_id','sws.school_wise_shift_row_id')
            ->get();

		$html = "";
		$html = "<option value=''>Select Shift</option>";
		if(isset($school_class_wise_shift_list) && !empty($school_class_wise_shift_list)){
			foreach($school_class_wise_shift_list as $shift_row){
				if(isset($current_shift) && ($shift_row->master_shift_row_id == $current_shift)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$shift_row->master_shift_row_id."' ".$selected.">".$shift_row->shift_title."</option>";
			}
		}
		return $html;
	}

	/*public function getSectionListByClass($version_row_id, $class_row_id, $session_row_id, $current_section = NULL){
		$school_class_wise_section_list = getSchoolClassewiseSectionList($version_row_id, $class_row_id, $session_row_id);
		$html = "<option value=''>Select Section</option>";
		if(isset($school_class_wise_section_list) && !empty($school_class_wise_section_list)){
			foreach($school_class_wise_section_list as $section_row){
				if(isset($current_section) && !empty($current_section) && ($section_row->section_row_id == $current_section)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$section_row->section_row_id."' ".$selected.">".$section_row->section_name."</option>";
			}
		}
		return $html;
	}*/

	public function getSectionListByClass($version_row_id, $master_class_row_id,$academic_session_year, $current_section = NULL){
		$school_class_wise_section_list = getSchoolClassewiseSectionList($version_row_id, $master_class_row_id,$academic_session_year);
		$html = "<option value=''>Select Section</option>";
		$html .="<option value='0'>Select All</option>";
		if(isset($school_class_wise_section_list) && !empty($school_class_wise_section_list)){
			foreach($school_class_wise_section_list as $section_row){
				if(isset($current_section) && !empty($current_section) && ($section_row->master_section_row_id == $current_section)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$section_row->master_section_row_id."' ".$selected.">".$section_row->section_title."</option>";
			}
		}
		return $html;
	}

	public function getSectionListByClassArr($version_row_id, $master_class_row_id,$academic_session_year,$academic_session_month,$school_id){
		return $school_class_wise_section_list = SchoolWiseSection::where([['school_id',$school_id],['master_class_row_id',$master_class_row_id],['academic_session_year',$academic_session_year],['month',$academic_session_month],['version_row_id',$version_row_id], ['is_deleted', 0]])->get();
		$sectionArr = [];
		if(isset($school_class_wise_section_list) && !empty($school_class_wise_section_list)){
			foreach($school_class_wise_section_list as $section_row){
				$sectionArr[]= $section_row->master_section_row_id;	
			}
		}
		return $sectionArr;
	}

	public function getSectionListByClassByGroup($version_row_id, $master_class_row_id, $group_id, $academic_session_year, $current_section = NULL){
		$school_class_wise_section_list = getSchoolClassewiseSectionListByGroup($version_row_id, $master_class_row_id, $group_id, $academic_session_year);
		$html = "<option value=''>Select Section</option>";
		if(isset($school_class_wise_section_list) && !empty($school_class_wise_section_list)){
			foreach($school_class_wise_section_list as $section_row){
				if(isset($current_section) && !empty($current_section) && ($section_row->master_section_row_id == $current_section)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$section_row->master_section_row_id."' ".$selected.">".$section_row->section_title."</option>";
			}
		}
		return $html;
	}

	public function getClassSectionListByVersion($version_row_id, $session_id){
		$school_class_wise_section_list = getSchoolVersionWiseClassSectionList($version_row_id,$session_id);
		foreach($school_class_wise_section_list as $key => $value){
			$data_array[$value->master_class_row_id][] = $value;
		}
		return view('backend/school_admin/distance_learning/class_section',compact('data_array'));
;	}

	public function getOptionalSubjectListByClass($version_row_id, $class_row_id, $session_row_id, $current_group){
		$alloptionalsubjectbyclass = DB::table('class_wise_subject AS cs')
				->leftjoin('school_wise_subjects AS sub', 'cs.school_wise_subjects_row_id', '=', 'sub.school_subject_row_id')
				->select('*')
				->where([ ['cs.master_class_row_id', $class_row_id], ['cs.version_row_id', $version_row_id], ['cs.academic_session', $session_row_id],['sub.subject_group', $current_group],['sub.is_fake', 0], ['cs.is_optional', 1],['cs.school_id', session('school_id')]])
				->orderBy('sub.sort_order', 'asc')->get();
	return $alloptionalsubjectbyclass;
	}
	public function getStudentListByFilter($version_row_id, $class_row_id, $session_row_id, $current_shift, $current_section, $current_group){
		if(session('school_id') != '08630009'){
			$academic_session_year = DB::table('school_wise_academic_session')->where([ ['school_wise_academic_session_row_id', $session_row_id], ['is_active', 1] ])->value('academic_session_year');
	    	$current_year = date('Y'); 
	    	if(($academic_session_year != $current_year) && (session('school_id') != '06480005')){
	    		$inactive_stds = DB::table('students AS std')->where([ ['std.school_id', session('school_id')], ['std.current_class', $class_row_id], ['std.current_shift', $current_shift], ['std.current_section', $current_section], ['std.academic_version', $version_row_id], ['std.current_department', $current_group], ['std.active_status', 0], ['std.is_deleted', 0] ])->pluck('student_row_id')->toArray();


	    		$studentList = DB::table('students_academic_details AS std')->leftjoin('users AS user', 'std.student_row_id', '=', 'user.user_id')->where([ ['std.school_id', session('school_id')], ['std.academic_class', $class_row_id], ['std.academic_shift', $current_shift], ['std.academic_section', $current_section], ['std.academic_version', $version_row_id], ['std.academic_department', $current_group] ])->whereNotIn('std.student_row_id', $inactive_stds)->orderBy('std.academic_rollnumber', 'asc')->get();	
	    	} else {
	    		$studentList = DB::table('students AS std')->leftjoin('users AS user', 'std.student_row_id', '=', 'user.user_id')->where([ ['std.school_id', session('school_id')], ['std.current_class', $class_row_id], ['std.current_shift', $current_shift], ['std.current_section', $current_section], ['std.academic_version', $version_row_id], ['std.current_department', $current_group], ['std.active_status', 1], ['std.is_deleted', 0], ['std.academic_session_year', $session_row_id] ])->orderBy('std.current_rollnumber', 'asc')->get();	
	    	}
    	} else {
    		$studentList = DB::table('students AS std')->leftjoin('users AS user', 'std.student_row_id', '=', 'user.user_id')->where([ ['std.school_id', session('school_id')], ['std.current_class', $class_row_id], ['std.current_shift', $current_shift], ['std.current_section', $current_section], ['std.academic_version', $version_row_id], ['std.current_department', $current_group], ['std.active_status', 1], ['std.is_deleted', 0], ['std.academic_session_year', $session_row_id] ])->orderBy('std.current_rollnumber', 'asc')->get();	
    	}
		return $studentList;
	}
    public function getStudentListBySection($version_row_id, $class_row_id, $session_row_id, $current_shift, $current_section, $current_group){
        $studentList = DB::table('students AS std')
                ->leftjoin('users AS user', 'std.student_row_id', '=', 'user.user_id')
                ->leftjoin('students_details AS details', 'std.student_row_id', '=', 'details.student_row_id')
                ->where([ ['school_id', session('school_id')], ['std.current_class', $class_row_id], ['std.current_shift', $current_shift],
                    ['std.current_section', $current_section], ['std.academic_version', $version_row_id], ['std.current_department', $current_group],
                    ['std.active_status', 1], ['std.academic_session_year',$session_row_id], ['std.is_deleted', 0] ])
                ->orderBy('std.current_rollnumber', 'ASC')->get();
        return $studentList;
    }

    public function countTotalStudentBySection($version_row_id, $class_row_id, $session_row_id, $current_shift, $current_section, $current_group){
        $total_student = DB::table('students AS std')->leftjoin('users AS user', 'std.student_row_id', '=', 'user.user_id')->where([ ['school_id', session('school_id')], ['current_class', $class_row_id], ['current_shift', $current_shift], ['current_section', $current_section], ['academic_version', $version_row_id], ['current_department', $current_group], ['active_status', 1], ['academic_session_year',$session_row_id] ])->count();
        return $total_student;
    }
    public function getStudentListForDropdown($session_id, $version_row_id, $shift, $master_class_row_id, $section, $department){
        $studentList = DB::table('students AS std')->leftjoin('users AS user', 'std.student_row_id', '=', 'user.user_id')->where([ ['school_id', session('school_id')], ['current_class', $master_class_row_id], ['current_shift', $shift], ['current_section', $section], ['academic_version', $version_row_id], ['current_department', $department], ['active_status', 1], ['academic_session_year',$session_id] ])->orderBy('current_rollnumber', 'ASC')->get();
        $html = '';
        $html = "<option value=''>Select Student</option>";
        if(isset($studentList) && !empty($studentList)){
            foreach($studentList as $student_row){
                if(isset($current_subject_row_id) && !empty($current_subject_row_id) && ($student_row->student_row_id == $current_subject_row_id)){
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }

                $html .="<option value='".$student_row->student_row_id."'  student_row_id='".$student_row->student_row_id."'".$selected.">".$student_row->name."</option>";
            }
        }
        return $html;
    }

    public function getStudentListForResult($session_id, $version_row_id, $shift, $master_class_row_id, $section, $department){
    	$academic_session_year = DB::table('school_wise_academic_session')->where([ ['school_wise_academic_session_row_id', $session_id], ['is_active', 1] ])->value('academic_session_year');
    	$current_year = date('Y');
    	if(session('school_id') != '08630009'){
	    	if(($academic_session_year != $current_year) && (session('school_id') != '06480005')){
	    		$studentList = DB::table('students_academic_details AS std')->leftjoin('users AS user', 'std.student_row_id', '=', 'user.user_id')->where([ ['school_id', session('school_id')], ['academic_class', $master_class_row_id], ['academic_shift', $shift], ['academic_section', $section], ['academic_version', $version_row_id], ['academic_department', $department], ['academic_session',$session_id] ])->orderBy('academic_rollnumber', 'ASC')->get();
	    	} else {
	    		$studentList = DB::table('students AS std')->leftjoin('users AS user', 'std.student_row_id', '=', 'user.user_id')->where([ ['school_id', session('school_id')], ['current_class', $master_class_row_id], ['current_shift', $shift], ['current_section', $section], ['academic_version', $version_row_id], ['current_department', $department], ['active_status', 1], ['is_deleted', 0], ['academic_session_year',$session_id] ])->orderBy('current_rollnumber', 'ASC')->get();
	    		// $studentList = DB::table('students_academic_details AS std')->leftjoin('users AS user', 'std.student_row_id', '=', 'user.user_id')->where([ ['school_id', session('school_id')], ['academic_class', $master_class_row_id], ['academic_shift', $shift], ['academic_section', $section], ['academic_version', $version_row_id], ['academic_department', $department], ['academic_session',$session_id] ])->orderBy('academic_rollnumber', 'ASC')->get();
	    	}
    	} else {
    		$studentList = DB::table('students AS std')->leftjoin('users AS user', 'std.student_row_id', '=', 'user.user_id')->where([ ['school_id', session('school_id')], ['current_class', $master_class_row_id], ['current_shift', $shift], ['current_section', $section], ['academic_version', $version_row_id], ['current_department', $department], ['active_status', 1], ['is_deleted', 0], ['academic_session_year',$session_id] ])->orderBy('current_rollnumber', 'ASC')->get();
    	}
        
        //dd($studentList);
        $html = '';
        $html = "<option value=''>Select Student</option>";
        if(isset($studentList) && !empty($studentList)){
            foreach($studentList as $student_row){
                if(isset($current_subject_row_id) && !empty($current_subject_row_id) && ($student_row->student_row_id == $current_subject_row_id)){
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }

                if((session('school_id') != '08630009') && (session('school_id') != '06480005')){
                	$current_rollnumber = ($academic_session_year != $current_year) ? $student_row->academic_rollnumber  : $student_row->current_rollnumber;
            	} else {
            		$current_rollnumber = $student_row->current_rollnumber;
            	}

                $html .="<option value='".$student_row->student_row_id."'  student_row_id='".$student_row->student_row_id."'".$selected.">Roll: ".$current_rollnumber.' - '.$student_row->name."</option>";
            }
        }
        return $html;
    }

	public function getSubjectListByClass($version_row_id, $class_row_id, $session_row_id, $group_row_id, $current_subject_row_id = NULL){
		$html = '';
		$allsubjectbyclass = DB::table('class_wise_subject AS cs')
				->leftjoin('school_wise_subjects AS sub', 'cs.school_wise_subjects_row_id', '=', 'sub.school_subject_row_id')
				->select('cs.class_wise_subject_row_id', 'sub.subject_title', 'sub.subject_short_tag', 'cs.school_wise_subjects_row_id','sub.school_subject_row_id')
				->where([ ['cs.master_class_row_id', $class_row_id], ['cs.version_row_id', $version_row_id], ['cs.academic_session', $session_row_id], ['cs.group_row_id', $group_row_id],['sub.is_fake', 0]])
				->orderBy('sub.sort_order', 'asc')->get();
		$html = "<option value=''>Select Subject</option>";
		if(isset($allsubjectbyclass) && !empty($allsubjectbyclass)){
			foreach($allsubjectbyclass as $subject_row){
				if(isset($current_subject_row_id) && !empty($current_subject_row_id) && ($subject_row->school_wise_subjects_row_id == $current_subject_row_id)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$subject_row->school_wise_subjects_row_id."' subject_short_tag='".$subject_row->subject_short_tag."' classwise_subject_row_id='".$subject_row->class_wise_subject_row_id."'".$selected.">".$subject_row->subject_title."</option>";
			}
		}
		return $html;
	}

	public function getSubjectCategoryListBySubject($classwiseSubjectRowId) {
		$subject_parts = DB::table('classwise_subjects')->where('classwise_subject_row_id', $classwiseSubjectRowId)->first()->subject_parts;
		$subpart = json_decode($subject_parts);
		$html = "<h5>Subject Category <span class='text-dange'>*</span></h5><div class='controls'>";
		if(is_array($subpart)) {
			if(in_array(1, $subpart)) {
				$html.= '<fieldset><input type="checkbox" id="checksubject_1" checked value="1" class="checksubject" name="subjectpart[]"><label for="checksubject_1">Subjective</label></fieldset>';
			}
			if(in_array(2, $subpart)) {
				$html.= '<fieldset><input type="checkbox" checked value="2" id="checksubject_2" class="checksubject" name="subjectpart[]"><label for="checksubject_2">Objective</label></fieldset>';
			}
			if(in_array(3, $subpart)) {
				$html.= '<fieldset><input type="checkbox" checked value="3" id="checksubject_3" class="checksubject" name="subjectpart[]"><label for="checksubject_3">Practical</label></fieldset>';
			}

		} else {
			$html.= '<div>No Subject Category Found.</div>';
		}
		$html.="</div";
		echo $html;
	}

	public function getAssignSubjectListByClass($version_row_id, $class_row_id, $session_row_id){
		$allMasterSubjects = $this->getSubjectList(1);
		$html = "<table class='table table-striped table-bordered table-hover'><tr><th>Subject</th><th>Subject Category</th>";
		if(isset($allMasterSubjects) && !empty($allMasterSubjects)){
			foreach($allMasterSubjects as $subject_row){

				$html .="<tr class='tr_class' id='trid_".$subject_row->subject_row_id."'>";

					$html .= "<td class='subjectRowId' id='subrowid_".$subject_row->subject_row_id."'><fieldset><input type='checkbox' name='mastersubject[]' class='checksubject' subject_row_id='".$subject_row->subject_row_id.
					"[]' id='checkbox_".$subject_row->subject_row_id."' value='".$subject_row->subject_row_id."' "."/><label style='padding-right:10px;' for='checkbox_".$subject_row->subject_row_id."'>".$subject_row->subject_title.
					"</label></fieldset></td>";

					$html .= "<td class='subjectpartsid' id='subjectparts_".$subject_row->subject_row_id."'><fieldset><input type='checkbox' class='checksubjectpart_".$subject_row->subject_row_id.'_1'.
					"' name='subjectpart[".$subject_row->subject_row_id."][]' value='1' id='checksubjectpart_1_".$subject_row->subject_row_id."' /><label style='padding-right:10px;' for='checksubjectpart_1_".$subject_row->subject_row_id."'>".'Subjective'.
					"</label>
					<input type='checkbox' class='checksubjectpart_'".$subject_row->subject_row_id.'_2'.
					"' name='subjectpart[".$subject_row->subject_row_id."][]' value='2' id='checksubjectpart_2_".$subject_row->subject_row_id. "' /><label style='padding-right:10px;' for='checksubjectpart_2_".$subject_row->subject_row_id."'>".'Objective'.
					"</label>
					<input type='checkbox' class='checksubjectpart_'".$subject_row->subject_row_id.'_3'.
					"' name='subjectpart[".$subject_row->subject_row_id."][]' value='3' id='checksubjectpart_3_".$subject_row->subject_row_id."' /><label style='padding-right:10px;' for='checksubjectpart_3_".$subject_row->subject_row_id."'>".'Practical'.
					"</label>
					</fieldset></td>";

				$html .="</tr>";
			}
		}
		$html .="</table>";
		return $html;
	}
	public function getGroupListDropdownSuperAdmin($version_row_id, $master_class_row_id, $current_group_row_id = NULL){

		if($version_row_id != 4 && ($master_class_row_id == 9 || $master_class_row_id == 10 || $master_class_row_id == 11 || $master_class_row_id == 12)){
				$education_group_list = MasterEducationGroup::get();

		} else {
				$education_group_list = MasterEducationGroup::where('is_general',1)->get();
		}
		$html = "<option value=''>Select Group</option>";
		if(isset($education_group_list) && !empty($education_group_list)){
			foreach($education_group_list as $group_row){
				if(isset($current_group_row_id) && !empty($current_group_row_id) && ($group_row->master_group_row_id == $current_group_row_id)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$group_row->master_group_row_id."' ".$selected.">".$group_row->group_title."</option>";
			}
		}
		return $html;
	}

	public function getGroupListDropdown($version_row_id, $master_class_row_id,$academic_session_year, $current_group_row_id = NULL){

		if($version_row_id != 4 && ($master_class_row_id == 9 || $master_class_row_id == 10 || $master_class_row_id == 11 || $master_class_row_id == 12)){
			$is_general = 0;
		} else {
			$is_general = 1;
		}
		$education_group_list = getEducationGroupList($academic_session_year, $is_general);
		$html = "<option value=''>Select Group</option>";
		if(isset($education_group_list) && !empty($education_group_list)){
			foreach($education_group_list as $group_row){
				if(isset($current_group_row_id) && !empty($current_group_row_id) && ($group_row->master_group_row_id == $current_group_row_id)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$group_row->master_group_row_id."' ".$selected.">".$group_row->group_title."</option>";
			}
		}
		return $html;
	}

	public function getAllGroupListByVersion($version_row_id, $session_id, $current_group_row_id = null){
		$session_row = getSchoolWiseSessionRow($session_id);
		$school_id = session('school_id');
		$education_group_list = DB::table('master_education_groups as mg')
                        ->join('school_wise_education_groups as swg','mg.master_group_row_id','=','swg.master_group_row_id')
                        ->where([['school_id',$school_id],['academic_session_year',$session_row->academic_session_year],['month',$session_row->month]])
                        ->select('swg.*','mg.*')
                        ->get();

		$html = "<option value=''>Select Group</option>";
		if(isset($education_group_list) && !empty($education_group_list)){
			foreach($education_group_list as $group_row){
				if(isset($current_group_row_id) && !empty($current_group_row_id) && ($group_row->master_group_row_id == $current_group_row_id)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$group_row->master_group_row_id."' ".$selected.">".$group_row->group_title."</option>";
			}
		}
		return $html;
	}

	public function getDesignations($admin_type, $current_designation = 0) {

		$designations = DB::table('staff_designations')->where('school_id', session('school_id'))->where('staff_designation_category_row_id', $admin_type)->where('is_active',1)->get();
		$html = "";
		foreach($designations as $designation) {
			if(isset($current_designation) && ($designation->designation_row_id == $current_designation)) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			$html .= "<option value=" . $designation->designation_row_id . " " . $selected . ">" . $designation->designation_title . "</option>";
		}
		echo $html;

	}


	/*
    	input date as string
    	return date as string
    */
	public function DateEnglishToBangla($currentDate) {
		$engDATE = array('1','2','3','4','5','6','7','8','9','0','January','February','March','April',
		'May','June','July','August','September','October','November','December','Saturday','Sunday',
		'Monday','Tuesday','Wednesday','Thursday','Friday');

		$bangDATE = array('১','২','৩','৪','৫','৬','৭','৮','৯','০','জানুয়ারী','ফেব্রুয়ারী','মার্চ','এপ্রিল','মে',
		'জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর','শনিবার','রবিবার','সোমবার','মঙ্গলবার','
		বুধবার','বৃহস্পতিবার','শুক্রবার'
		);

		$convertedDATE = str_replace($engDATE, $bangDATE, $currentDate);
		return $convertedDATE;
	}

	public function getGroupByClass($version_row_id, $class_row_id, $current_section = NULL){
		$school_class_wise_section_list = getSchoolClassewiseSectionList($version_row_id, $class_row_id);
		$html = "<option value=''>Select Section</option>";
		foreach($school_class_wise_section_list as $section_row){
			if(isset($current_section) && !empty($current_section) && ($section_row->section_row_id == $current_section)){
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}

			$html .="<option value='".$section_row->section_row_id."' ".$selected.">".$section_row->section_name."</option>";
		}
		return $html;
	}

	public function uploadImage($fileInputField, $uploadFolder) {
        $uploadedFileName = '';
        if ($request->file($fileInputField)) {
            $fileInfo = $request->file($fileInputField);
            $uploadedFileName = time() . "_" . $fileInfo->getClientOriginalName();
            /*
             * Upload Original Image
             */
            $upload_path = public_path($uploadFolder);
            if (!File::exists($upload_path)) {
                File::makeDirectory($upload_path, $mode = 0777, true, true);
            }
            $fileInfo->move($upload_path, $uploadedFileName);
        }
        return $uploadedFileName;
    }



    public function check_api_key($school_id, $api_key){
		$check_api_key = DB::Table('api_keys')->where([['school_id', $school_id],['api_key', $api_key]])->get();
		if(!$check_api_key) {
			return false;
		} else {
        	return true;
        }
    }

    public function getSubjectList($status = 1){
		$subject_list = \App\Models\Subject::where([['status', $status],['school_id',session('school_id')]])-> orderBy('is_fake', 'desc')->orderBy('sort_order','ASC')->get();
		return $subject_list;
	}

	public function getClassWiseSubjectDetail($version_row_id, $class_row_id, $session_row_id, $group_row_id, $subject_row_id){
		$Class_wise_subject_list = \App\Models\ClasswiseSubject::where([['version_row_id', $version_row_id],['master_class_row_id', $class_row_id],['academic_session', $session_row_id], ['group_row_id', $group_row_id],['school_wise_subjects_row_id', $subject_row_id]])->first();
		return $Class_wise_subject_list;
	}

	public function getClassWiseSubjectList($version_row_id, $class_row_id, $session_row_id, $group_row_id){
		$Class_wise_subject_list = \App\Models\ClasswiseSubject::where([['version_row_id', $version_row_id],['master_class_row_id', $class_row_id],['academic_session', $session_row_id], ['group_row_id', $group_row_id]])->orderBy('sort_order', 'asc')->get();
		return $Class_wise_subject_list;
	}
	public function getSubjectRow($subject_row_id){
		$subject_row = \App\Models\Subject::where('school_subject_row_id', $subject_row_id)->first();
		return $subject_row;
	}
	public function checkSubjectRowByCode($subject_code){
		$subject_row = \App\Models\Subject::where([['subject_code', $subject_code],['school_id', session('school_id')]])->first();
		return $subject_row;
	}
	public function getNextSortOrderForSubject(){
		$sort_order = \App\Models\Subject::orderBy('sort_order', 'desc')->where('school_id',session('school_id'))->first();
		if(!isset($sort_order) && empty($sort_order)){
			$latest = 0;
		} else {
			$latest = $sort_order->sort_order;
		}
		return $latest+1;
	}

	public function getNextSortOrderForRoutineTimeslot($version_row_id, $class_row_id, $session_row_id, $current_shift, $current_section, $current_group){
		$sort_order = \App\Models\RoutineTimeslot::where([['version_row_id', $version_row_id], ['class_row_id', $class_row_id], ['academic_session_row_id', $session_row_id], ['shift_row_id', $current_shift], ['section_row_id', $current_section], ['group_row_id', $current_group]])->orderBy('sort_order', 'desc')->first();
		if(!isset($sort_order) && empty($sort_order)){
			$latest = 0;
		} else {
			$latest = $sort_order->sort_order;
		}
		return $latest+1;
	}

	public function getAssignSubjectTeacherList(){
		$query = DB::table('subject_teacher As st')
				->leftjoin('school_departments AS sd', 'a.school_department_row_id', '=', 'sd.school_department_row_id')
				->leftjoin('staff_designations AS sdg', 'a.designation_row_id', '=', 'sdg.designation_row_id')
				->select('a.*', 'sd.*', 'sdg.*')
				->where([ ['a.school_row_id', 1], ['a.designation_category_row_id', $teacherCatId ], ['a.active_status', 1] ])
				->orderBy('a.sort_order', 'asc');
		$allteachers = $query->get();
	}

	public function getTacherList(){
		$teacherCatId = getTeacherCatId();
		$allteachers = DB::table('admins As a')
				->leftjoin('users','a.admin_row_id','=','users.user_id')
				->leftjoin('staff_designations AS sdg', 'a.designation_row_id', '=', 'sdg.designation_row_id')
				->select('a.*', 'sdg.*','users.*')
				->whereIn('a.staff_designation_category_row_id', $teacherCatId)
				->where([['a.active_status', 1], ['a.school_id', session('school_id')],['sdg.school_id', session('school_id') ] ])
				->orderBy('a.sort_order', 'asc')->get();
		return $allteachers;
	}

	public function getTeachListWithoutTop(){
		$teacherCatId = getTeacherCatId();
		if (($key = array_search(1, $teacherCatId)) !== false) {
		    unset($teacherCatId[$key]);
		}
		//dd($teacherCatId);
		$allteachers = DB::table('admins As a')
				->leftjoin('users','a.admin_row_id','=','users.user_id')
				->leftjoin('staff_designations AS sdg', 'a.designation_row_id', '=', 'sdg.designation_row_id')
				->select('a.*', 'sdg.*','users.*')
				->whereIn('a.staff_designation_category_row_id', $teacherCatId)
				->where([['a.active_status', 1], ['a.school_id', session('school_id')],['sdg.school_id', session('school_id') ] ])
				->orderBy('a.sort_order', 'asc')->get();
		return $allteachers;
	}

	public function getAllEmployeeList(){
		$allemps = DB::table('admins As a')
				->leftjoin('users','a.admin_row_id','=','users.user_id')
				->leftjoin('staff_designations AS sdg', 'a.designation_row_id', '=', 'sdg.designation_row_id')
				->select('a.admin_row_id', 'users.name')
				->where([['a.active_status', 1], ['a.school_id', session('school_id')],['sdg.school_id', session('school_id') ] ])
				->orderBy('a.sort_order', 'asc')->get();
		return $allemps;
	}

	public function getTeacherListBySubject($version_row_id, $session_row_id, $class_row_id, $section_row_id, $group_row_id, $subject_row_id) {
		echo 'VER-'.$version_row_id.' SESS-'.$session_row_id.' CLASS- '.$class_row_id;
		echo '<br>';
		echo 'SEC-'.$section_row_id.' GRP-'.$group_row_id.' SUB- '.$subject_row_id;
		$query = DB::table('subject_teacher As st')
				->leftjoin('admins As a', 'a.admin_row_id', '=', 'st.admin_row_id')
				->leftjoin('school_departments AS sd', 'a.master_department_row_id', '=', 'sd.school_department_row_id')
				->leftjoin('staff_designations AS sdg', 'a.designation_row_id', '=', 'sdg.designation_row_id')
				->leftjoin('users', 'users.user_id', '=', 'a.admin_row_id')
				->select('st.*', 'a.*', 'sd.*', 'sdg.*', 'users.name', 'users.user_id')
				->where([ ['st.version_row_id', $version_row_id], ['st.school_id', session('school_id')], ['st.master_class_row_id', $class_row_id], ['st.master_section_row_id', $section_row_id], ['st.group_row_id', $group_row_id], ['st.class_wise_subject_row_id', $subject_row_id]  ])
				->orderBy('users.name', 'asc');
		$allteachers = $query->get();

		$html = "";
        $html .= "<option value=''>Select Teacher</option>";
        foreach ($allteachers as $data) {
        	$html .= "<option value='".$data->admin_row_id."'>".$data->name."</option>";
        }
		return $html;
	}

	public function getMajorExamByClass($version_row_id, $class_row_id, $academic_session) {
		$academic_session_year = DB::table('school_wise_academic_session')->where('school_wise_academic_session_row_id', $academic_session)->value('academic_session_year');
		$query = DB::table('classwise_master_term AS cmt')
				->leftjoin('schoolwise_master_term As smt', 'smt.schoolwise_master_term_row_id', '=', 'cmt.schoolwise_master_term_row_id')
				->leftjoin('master_term AS mt', 'mt.exam_master_term_row_id', '=', 'smt.master_term_row_id')
				->select('cmt.*', 'smt.*', 'mt.*')
				->where([ ['cmt.version_row_id', $version_row_id], ['cmt.master_class_row_id', $class_row_id] , ['cmt.entry_year', $academic_session_year], ['cmt.school_id', session('school_id')] ])
				->orderBy('mt.exam_master_term_row_id', 'asc');
		$allMajorTerms = $query->get();
		$html = "";
        $html .= "<option value=''>Select Major Term</option>";
        foreach ($allMajorTerms as $data) {
        	$html .= "<option value='".$data->classwise_master_term_row_id.'|'.$data->master_term_row_id.'|'.$data->exam_short_tag."'>".$data->exam_category_title."</option>";
        }
		return $html;
	}

	public function getAllMajorExams($version_row_id, $academic_session = NULL) {
		$academic_session_year = DB::table('school_wise_academic_session')->where('school_wise_academic_session_row_id', $academic_session)->value('academic_session_year');

		$entry_years = SchoolwiseMasterTerm::where('school_id',session('school_id'))->orderBy('entry_year', 'desc')->groupBy('entry_year')->pluck('entry_year')->toArray();

		$query = DB::table('schoolwise_master_term AS smt')
				->leftjoin('master_term AS mt', 'mt.exam_master_term_row_id', '=', 'smt.master_term_row_id')
				->select('smt.*', 'mt.*')
				->where([ ['smt.school_id', session('school_id')],['smt.entry_year', $entry_years[0]] ])
				->groupBy('mt.exam_master_term_row_id')
				->orderBy('mt.exam_master_term_row_id', 'asc');
		$allMajorTerms = $query->get();
		//dd($allMajorTerms);
		$html = "";
        $html .= "<option value=''>Select Major Term</option>";
        foreach ($allMajorTerms as $data) {
        	$html .= "<option value='".$data->schoolwise_master_term_row_id.'|'.$data->master_term_row_id.'|'.$data->exam_short_tag."'>".$data->exam_category_title."</option>";
        }
		return $html;
	}

	public function getOthersExamByClass($version_row_id, $class_row_id) {
		$query = DB::table('classwise_elementary_term AS cet')
				->leftjoin('schoolwise_elementary_term As set', 'set.schoolwise_elementary_term_row_id', '=', 'cet.schoolwise_elementary_term_row_id')
				->leftjoin('exam_elementary_master_term AS eemt', 'eemt.exam_elementary_master_term_row_id', '=', 'set.exam_elementary_master_term_row_id')
				->select('cet.*', 'set.*', 'eemt.*')
				->where([ ['cet.version_row_id', $version_row_id], ['cet.master_class_row_id', $class_row_id] ])
				->orderBy('eemt.exam_elementary_master_term_row_id', 'asc');
		$allElementaryTerms = $query->get();
		$html = "";
        $html .= "<option value=''>Select Other Exam</option>";
        foreach ($allElementaryTerms as $data) {
        	$html .= "<option value='".$data->exam_elementary_master_term_row_id.'|'.$data->elementary_short_tag."'>".$data->elementary_term_title."</option>";
        }
		return $html;
	}

	public function getRoutineTimeslotRow($timeslots_row_id){
		$timeslot_row = \App\Models\RoutineTimeslot::where('timeslots_row_id', $timeslots_row_id)->first();
		return $timeslot_row;
	}

	public function getRoutineTimeslotList($status = 1){
		$timeslot_list = \App\Models\RoutineTimeslot::where([['school_id',session('school_id')],['status', $status]])->orderBy('master_class_row_id', 'ASC')->get();
		return $timeslot_list;
 	}

	public function getRoutineTimeslotListByClass($version_row_id, $class_row_id, $shift_row_id, $academic_session_row_id, $section_row_id, $group_row_id){
		$session = \App\Models\SchoolWiseAcademicSession::find($academic_session_row_id);
		$timeslot_list = \App\Models\RoutineTimeslot::where([['status', 1],['school_id', session('school_id')],['version_row_id', $version_row_id], ['master_class_row_id', $class_row_id], ['master_shift_row_id', $shift_row_id], ['academic_session_year', $session->academic_session_year], ['month', $session->month], ['master_section_row_id', $section_row_id], ['master_group_row_id', $group_row_id]])->orderBy('sort_order', 'ASC')->get();
		return $timeslot_list;
	}

	public function getMasterExamList(){
		$exam_master_term_list = \App\Models\MasterTerm::orderBy('exam_master_term_row_id', 'asc')->get();
		return $exam_master_term_list;
	}

	public function getElementaryExamList(){
		$exam_elementary_term_list = \App\Models\ExamElementaryMasterTerm::orderBy('exam_elementary_master_term_row_id', 'asc')->get();
		return $exam_elementary_term_list;
	}

	public function getSubjectByTeacherbyClass($version,$class, $section, $group,$session){
		$user = Auth()->guard('schoolAdmins')->user()->admin_row_id;
		$sessionDetails = SchoolWiseAcademicSession::findorfail($session);
		$subject = SubjectTeacher::with('master_subject')->where([['master_class_row_id', $class], ['version_row_id', $version], ['academic_session_year', $sessionDetails->academic_session_year],['month', $sessionDetails->month], ['master_section_row_id', $section], ['group_row_id', $group],['admin_row_id',$user]])->get();
		$html = "";
        $html .= "<option value=''>Select Subject</option>";
        foreach ($subject as $key => $value) {
        	$html .= "<option value='".$value->master_subject_row_id."'>".$value->master_subject->subject_title."</option>";
        }
		return $html;
	}

	public function getStudentsWithIdByClass($session, $version_id, $classid, $shiftid, $sectionid, $group) {
		$query = DB::table('students AS std')
		->leftjoin('users','std.student_row_id','=','users.user_id')
		->select('std.*','users.name')
		->where([ ['std.academic_version', $version_id],['std.academic_session_year', $session], ['std.current_class', $classid], ['std.current_shift', $shiftid], ['std.current_section', $sectionid],['std.current_department', $group],['std.is_deleted',0],['std.active_status', 1],  ['school_id',session('school_id')]])
		->orderBy('std.current_rollnumber', 'asc');

		$allStudentsByClass = $query->get();

		$html = "";
		$html .= "<option value=''>Select Student</option>";
		foreach($allStudentsByClass as $students) {
			$html .= "<option value=".$students->student_row_id.">".$students->name."</option>";
		}
		echo $html;
	}

	public function getStudentListByClass($version_id, $classid, $shiftid, $session, $sectionid, $group=0) {
		$query = DB::table('students AS std')
		->select('std.*')
		->where([ ['std.academic_version', $version_id],['std.current_session', $session], ['std.current_class', $classid], ['std.current_shift', $shiftid], ['std.current_section', $sectionid] ])
		->orderBy('std.current_rollnumber', 'asc');

		if($group) {
			$query->where('std.current_department', $group);
		}

		$allStudentsByClass = $query->get();
		return json_encode($allStudentsByClass);
	}

	public function getElementaryResultById($version_id, $classid, $shiftid, $sectionid, $group, $student_id, $master_term_id, $session_id) {
		$query = DB::table('elementary_exam_marks AS eem')
		->leftjoin('elementary_exam_infos as eei', 'eem.elem_exam_infos_row_id', '=', 'eei.elem_exam_infos_row_id')
		->leftjoin('subjects', 'eei.master_subject_row_id', '=', 'subjects.subject_row_id')
		->leftjoin('classwise_subjects AS cs', function($join){
            $join->on('cs.subject_row_id', '=', 'subjects.subject_row_id')->on('cs.version_row_id', '=', 'eei.version_row_id');
        })
		->select('eem.*', 'eei.*', 'subjects.*', 'cs.*')
		->where([ ['eei.academic_session_row_id', $session_id], ['eem.student_row_id', $student_id], ['eei.master_term_row_id', $master_term_id], ['cs.class_row_id', $classid], ['cs.academic_session_row_id', $session_id], ['cs.version_row_id', $version_id], ['eei.master_section_row_id', $sectionid], ['cs.group_row_id', $group]  ])
		->orderBy('cs.sort_order', 'asc');

		$elementaryResult = $query->get();

		// count highest marks in a subject
		foreach($elementaryResult as $examdata) {
			$highest_marks = \App\Models\ElementaryExamMark::where('elem_exam_infos_row_id', '=', $examdata->elem_exam_infos_row_id)->max('marks_obtained');
			$examdata->highestmarks = $highest_marks;
		}
		return $elementaryResult;
	}

	public function getSubjectNameByClass($session_row_id,$version_row_id, $class_row_id, $group_row_id){

		$subject_data = DB::table('classwise_subject_assessment')->where([ ['school_id', get_school_id()], ['academic_session_row_id', $session_row_id], ['version_row_id', $version_row_id], ['master_class_row_id', $class_row_id] ])->first();
		//dd($subject_data);
		$subjects_id_inassessment = json_decode($subject_data->subjects_id_inassessment, true);

		//dd($subjects_id_inassessment);

		$Class_wise_subject_list = DB::table('master_subjects As msub')
				->leftjoin('school_wise_subjects AS swsub', 'msub.master_subject_id', '=', 'swsub.master_subject_row_id')
				->leftjoin('class_wise_subject AS cwsub', 'swsub.school_subject_row_id', '=', 'cwsub.school_wise_subjects_row_id')
				->where([['cwsub.academic_session',$session_row_id],['cwsub.version_row_id',$version_row_id],['cwsub.master_class_row_id',$class_row_id],['cwsub.group_row_id',$group_row_id],['cwsub.school_id',session('school_id')],['swsub.is_fake',0]])
				->whereIn('swsub.school_subject_row_id', $subjects_id_inassessment)
				->orderBy('swsub.sort_order', 'asc')
				->get();

		return $Class_wise_subject_list;
	}

	public function getSubjectNameByClassForAPI($school_id, $session_row_id,$version_row_id, $class_row_id, $group_row_id){

		$Class_wise_subject_list = DB::table('master_subjects As msub')
				->leftjoin('school_wise_subjects AS swsub', 'msub.master_subject_id', '=', 'swsub.master_subject_row_id')
				->leftjoin('class_wise_subject AS cwsub', 'swsub.school_subject_row_id', '=', 'cwsub.school_wise_subjects_row_id')
				->where([['cwsub.academic_session',$session_row_id],['cwsub.version_row_id',$version_row_id],['cwsub.master_class_row_id',$class_row_id],['cwsub.group_row_id',$group_row_id],['cwsub.school_id',$school_id],['swsub.is_fake',0]])
				->orderBy('cwsub.sort_order', 'asc')
				->get();

		return $Class_wise_subject_list;
	}

	public function getStudentCtevMarkEntryExist($student_id, $version_row_id, $session_row_id, $shift_row_id, $class_row_id, $group_row_id, $section_row_id, $subject_row_id){
		$stu_mark_row = \App\Models\CteExamMarkClass::where([['student_id', $student_id], ['version_row_id', $version_row_id], ['class_row_id', $class_row_id], ['academic_session_row_id', $session_row_id], ['group_row_id', $group_row_id], ['section_row_id', $section_row_id], ['subject_row_id', $subject_row_id], ['shift_row_id', $shift_row_id]])->first();
		return $stu_mark_row;
	}

	public $year_list = array(
		'2020' => '2020',

		);

	public $month_list = array(
		'01' => 'January',
		'02' => 'February',
		'03' => 'March',
		'04' => 'April',
		'05' => 'May',
		'06' => 'June',
		'07' => 'July',
		'08' => 'August',
		'09' => 'September',
		'10' => 'October',
		'11' => 'November',
		'12' => 'December'
		);

	public function countStudentsByClass($startClass, $endClass, $current_session) {
		for($class_row_id=$startClass; $class_row_id<=$endClass; $class_row_id++)
		{
			$total_students = DB::table('students')
			->where([ ['current_session', $current_session], ['current_section', $class_row_id], ['active_status', 1] ])
			->count();
			$arr[$class_row_id] = $total_students;
		}
		return $arr;

	}

	public function getOtherExamInfo($elem_exam_infos_row_id = null) {
		if($elem_exam_infos_row_id) {
			$other_exam_info = DB::table('elementary_exam_infos As eei')
				->leftjoin('master_classes AS mc', 'mc.class_row_id', '=', 'eei.master_class_row_id')
				->leftjoin('school_sections AS ss', 'ss.section_row_id', '=', 'eei.master_section_row_id')
				->leftjoin('subjects AS sub', 'sub.subject_row_id', '=', 'eei.master_subject_row_id')
				->leftjoin('admins AS adm', 'adm.admin_row_id', '=', 'eei.admin_row_id')
				->leftjoin('education_version AS ev', 'ev.version_row_id', '=', 'eei.version_row_id')
				->select('eei.*', 'mc.class_row_id', 'mc.class_name', 'ss.section_row_id', 'ss.section_name', 'sub.subject_row_id', 'sub.subject_title', 'adm.admin_row_id', 'adm.admin_name', 'ev.version_row_id', 'ev.version_title')
				->where('eei.elem_exam_infos_row_id', $elem_exam_infos_row_id)
				->orderBy('eei.created_at', 'desc')->first();
		} else {
			$other_exam_info = DB::table('elementary_exam_infos As eei')
				->leftjoin('master_classes AS mc', 'mc.class_row_id', '=', 'eei.master_class_row_id')
				->leftjoin('school_sections AS ss', 'ss.section_row_id', '=', 'eei.master_section_row_id')
				->leftjoin('subjects AS sub', 'sub.subject_row_id', '=', 'eei.master_subject_row_id')
				->leftjoin('admins AS adm', 'adm.admin_row_id', '=', 'eei.admin_row_id')
				->leftjoin('education_version AS ev', 'ev.version_row_id', '=', 'eei.version_row_id')
				->select('eei.*', 'mc.class_row_id', 'mc.class_name', 'ss.section_row_id', 'ss.section_name', 'sub.subject_row_id', 'sub.subject_title', 'adm.admin_row_id', 'adm.admin_name', 'ev.version_row_id', 'ev.version_title')
				->orderBy('eei.created_at', 'desc')->get();
		}

		return $other_exam_info;
	}

	public function getMajorExamInfo($is_super_admin, $master_exam_row_id = null) {
		$admin_row_id = Auth()->guard('schoolAdmins')->user()->admin_row_id;
		$swas = DB::table('school_wise_academic_session As swas')->where([ ['school_id', session('school_id')], ['is_active', 1] ])->get();
		$academic_session_year = array();
		foreach ($swas as $data) {
			$academic_session_year[] = $data->academic_session_year;
		}
		//dd($academic_session_year);
		$date_year = (session('school_id') == '06480005') ? date('Y')-1 : date('Y');
		if($master_exam_row_id) {
			$major_exam_info = DB::table('master_exam_infos As mei')
				->leftjoin('master_classes AS mc', 'mc.master_class_row_id', '=', 'mei.class_row_id')
				->leftjoin('school_and_class_wise_sections as ms', function($join) {
					$join->on('ms.master_section_row_id', '=', 'mei.section_row_id')
						 ->on('ms.school_id', '=', 'mei.school_row_id')
						 ->on('ms.master_class_row_id', '=', 'mei.class_row_id');
                })
				->leftjoin('school_wise_subjects AS sub', 'sub.school_subject_row_id', '=', 'mei.subject_row_id')
				->leftjoin('users AS us', 'us.user_id', '=', 'mei.admin_row_id')
				->leftjoin('education_version AS ev', 'ev.version_row_id', '=', 'mei.version_row_id')
				->select('mei.*', 'mc.master_class_row_id', 'mc.class_name', 'ms.master_section_row_id', 'ms.section_title', 'sub.school_subject_row_id', 'sub.subject_title', 'sub.subject_short_tag', 'us.user_id', 'us.name', 'ev.version_row_id', 'ev.version_title')
				->where('mei.master_exam_row_id', $master_exam_row_id)
				->where('mei.school_row_id',session('school_id'))
				//->where('mei.academic_session_year', date('Y'))
				->whereIn('mei.academic_session_year', $academic_session_year)
				->where('mei.exam_type', 1)
				->where('ms.is_deleted', 0)
				->whereIn('ms.academic_session_year', $academic_session_year)
				->orderBy('mei.created_at', 'desc')->first();

		} else {
			if($is_super_admin != 1){
				$major_exam_info = DB::table('master_exam_infos As mei')
				->leftjoin('master_classes AS mc', 'mc.master_class_row_id', '=', 'mei.class_row_id')
				->leftjoin('school_and_class_wise_sections as ms', function($join) {
					$join->on('ms.master_section_row_id', '=', 'mei.section_row_id')
						 ->on('ms.school_id', '=', 'mei.school_row_id')
						 ->on('ms.master_class_row_id', '=', 'mei.class_row_id');
                })
				->leftjoin('school_wise_subjects AS sub', 'sub.school_subject_row_id', '=', 'mei.subject_row_id')
				->leftjoin('users AS us', 'us.user_id', '=', 'mei.admin_row_id')
				->leftjoin('education_version AS ev', 'ev.version_row_id', '=', 'mei.version_row_id')
				->select('mei.*', 'mc.master_class_row_id', 'mc.class_name', 'ms.master_section_row_id', 'ms.section_title', 'sub.school_subject_row_id', 'sub.subject_title', 'sub.subject_short_tag', 'us.user_id', 'us.name', 'ev.version_row_id', 'ev.version_title')
				->where('mei.admin_row_id', $admin_row_id)
				->where('mei.school_row_id',session('school_id'))
				//->where('mei.academic_session_year', date('Y'))
				->whereIn('mei.academic_session_year', $academic_session_year)
				->where('mei.exam_type', 1)
				->where('ms.is_deleted', 0)
				->whereIn('ms.academic_session_year', $academic_session_year)
				->orderBy('mei.created_at', 'desc')->get();
			} else {
				$major_exam_info = DB::table('master_exam_infos As mei')
				->leftjoin('master_classes AS mc', 'mc.master_class_row_id', '=', 'mei.class_row_id')
				->leftjoin('school_and_class_wise_sections as ms', function($join) {
					$join->on('ms.master_section_row_id', '=', 'mei.section_row_id')
						 ->on('ms.school_id', '=', 'mei.school_row_id')
						 ->on('ms.master_class_row_id', '=', 'mei.class_row_id');
                })
				->leftjoin('school_wise_subjects AS sub', 'sub.school_subject_row_id', '=', 'mei.subject_row_id')
				->leftjoin('users AS us', 'us.user_id', '=', 'mei.admin_row_id')
				->leftjoin('education_version AS ev', 'ev.version_row_id', '=', 'mei.version_row_id')
				->select('mei.*', 'mc.master_class_row_id', 'mc.class_name', 'ms.master_section_row_id', 'ms.section_title', 'sub.school_subject_row_id', 'sub.subject_title', 'sub.subject_short_tag', 'us.user_id', 'us.name', 'ev.version_row_id', 'ev.version_title')
				->where('mei.school_row_id', session('school_id'))
				//->where('mei.academic_session_year', date('Y'))
				->whereIn('mei.academic_session_year', $academic_session_year)
				->where('mei.exam_type', 1)
				->where('ms.is_deleted', 0)
				->whereIn('ms.academic_session_year', $academic_session_year)
				->orderBy('mei.created_at', 'desc')->get();
			}
		}

		return $major_exam_info;
	}

	public function getSdpExamInfo($is_super_admin, $master_exam_row_id = null) {
		$admin_row_id = Auth()->guard('schoolAdmins')->user()->admin_row_id;
		$date_year = (session('school_id') == '06480005') ? date('Y')-1 : date('Y');
		if($master_exam_row_id) {
			$major_exam_info = DB::table('master_exam_infos As mei')
				->leftjoin('master_classes AS mc', 'mc.master_class_row_id', '=', 'mei.class_row_id')
				->leftjoin('school_and_class_wise_sections as ms', function($join) {
					$join->on('ms.master_section_row_id', '=', 'mei.section_row_id')
						 ->on('ms.school_id', '=', 'mei.school_row_id')
						 ->on('ms.master_class_row_id', '=', 'mei.class_row_id');
                })
				->leftjoin('school_wise_subjects AS sub', 'sub.school_subject_row_id', '=', 'mei.subject_row_id')
				->leftjoin('users AS us', 'us.user_id', '=', 'mei.admin_row_id')
				->leftjoin('education_version AS ev', 'ev.version_row_id', '=', 'mei.version_row_id')
				->select('mei.*', 'mc.master_class_row_id', 'mc.class_name', 'ms.master_section_row_id', 'ms.section_title', 'sub.school_subject_row_id', 'sub.subject_title', 'sub.subject_short_tag', 'us.user_id', 'us.name', 'ev.version_row_id', 'ev.version_title')
				->where('mei.master_exam_row_id', $master_exam_row_id)
				->where('mei.school_row_id',session('school_id'))
				->where('mei.academic_session_year', date('Y'))
				->where('mei.exam_type', 2)
				->where('ms.academic_session_year', $date_year)
				->orderBy('mei.created_at', 'desc')->first();

		} else {
			if($is_super_admin != 1){
				$major_exam_info = DB::table('master_exam_infos As mei')
				->leftjoin('master_classes AS mc', 'mc.master_class_row_id', '=', 'mei.class_row_id')
				->leftjoin('school_and_class_wise_sections as ms', function($join) {
					$join->on('ms.master_section_row_id', '=', 'mei.section_row_id')
						 ->on('ms.school_id', '=', 'mei.school_row_id')
						 ->on('ms.master_class_row_id', '=', 'mei.class_row_id');
                })
				->leftjoin('school_wise_subjects AS sub', 'sub.school_subject_row_id', '=', 'mei.subject_row_id')
				->leftjoin('users AS us', 'us.user_id', '=', 'mei.admin_row_id')
				->leftjoin('education_version AS ev', 'ev.version_row_id', '=', 'mei.version_row_id')
				->select('mei.*', 'mc.master_class_row_id', 'mc.class_name', 'ms.master_section_row_id', 'ms.section_title', 'sub.school_subject_row_id', 'sub.subject_title', 'sub.subject_short_tag', 'us.user_id', 'us.name', 'ev.version_row_id', 'ev.version_title')
				->where('mei.admin_row_id', $admin_row_id)
				->where('mei.school_row_id',session('school_id'))
				->where('mei.academic_session_year', date('Y'))
				->where('mei.exam_type', 2)
				->where('ms.academic_session_year', $date_year)
				->orderBy('mei.created_at', 'desc')->get();
			} else {
				$major_exam_info = DB::table('master_exam_infos As mei')
				->leftjoin('master_classes AS mc', 'mc.master_class_row_id', '=', 'mei.class_row_id')
				->leftjoin('school_and_class_wise_sections as ms', function($join) {
					$join->on('ms.master_section_row_id', '=', 'mei.section_row_id')
						 ->on('ms.school_id', '=', 'mei.school_row_id')
						 ->on('ms.master_class_row_id', '=', 'mei.class_row_id');
                })
				->leftjoin('school_wise_subjects AS sub', 'sub.school_subject_row_id', '=', 'mei.subject_row_id')
				->leftjoin('users AS us', 'us.user_id', '=', 'mei.admin_row_id')
				->leftjoin('education_version AS ev', 'ev.version_row_id', '=', 'mei.version_row_id')
				->select('mei.*', 'mc.master_class_row_id', 'mc.class_name', 'ms.master_section_row_id', 'ms.section_title', 'sub.school_subject_row_id', 'sub.subject_title', 'sub.subject_short_tag', 'us.user_id', 'us.name', 'ev.version_row_id', 'ev.version_title')
				->where('mei.school_row_id', session('school_id'))
				->where('mei.academic_session_year', date('Y'))
				->where('mei.exam_type', 2)
				->where('ms.academic_session_year', $date_year)
				->orderBy('mei.created_at', 'desc')->get();
			}
		}

		return $major_exam_info;
	}

	public function getImatapExamInfo($is_super_admin, $master_exam_row_id = null) {
		$admin_row_id = Auth()->guard('schoolAdmins')->user()->admin_row_id;

		$swas = DB::table('school_wise_academic_session As swas')->where([ ['school_id', session('school_id')], ['is_active', 1] ])->get();
		$academic_session_year = array();
		foreach ($swas as $data) {
			$academic_session_year[] = $data->academic_session_year;
		}

		if($master_exam_row_id) {
			$major_exam_info = DB::table('imatap_exam_infos As mei')
				->leftjoin('master_classes AS mc', 'mc.master_class_row_id', '=', 'mei.class_row_id')
				->leftjoin('school_and_class_wise_sections as ms', function($join) {
					$join->on('ms.master_section_row_id', '=', 'mei.section_row_id')
						 ->on('ms.school_id', '=', 'mei.school_row_id')
						 ->on('ms.master_class_row_id', '=', 'mei.class_row_id');
                })
				->leftjoin('school_wise_subjects AS sub', 'sub.school_subject_row_id', '=', 'mei.subject_row_id')
				->leftjoin('users AS us', 'us.user_id', '=', 'mei.admin_row_id')
				->leftjoin('education_version AS ev', 'ev.version_row_id', '=', 'mei.version_row_id')
				->select('mei.*', 'mc.master_class_row_id', 'mc.class_name', 'ms.master_section_row_id', 'ms.section_title', 'sub.school_subject_row_id', 'sub.subject_title', 'sub.subject_short_tag', 'us.user_id', 'us.name', 'ev.version_row_id', 'ev.version_title')
				->where('mei.imatap_exam_row_id', $master_exam_row_id)
				->where('mei.school_row_id',session('school_id'))
				->whereIn('mei.academic_session_year', $academic_session_year)
				->whereIn('ms.academic_session_year', $academic_session_year)
				->orderBy('mei.created_at', 'desc')->first();

		} else {
			if($is_super_admin != 1){
				$major_exam_info = DB::table('imatap_exam_infos As mei')
				->leftjoin('master_classes AS mc', 'mc.master_class_row_id', '=', 'mei.class_row_id')
				->leftjoin('school_and_class_wise_sections as ms', function($join) {
					$join->on('ms.master_section_row_id', '=', 'mei.section_row_id')
						 ->on('ms.school_id', '=', 'mei.school_row_id')
						 ->on('ms.master_class_row_id', '=', 'mei.class_row_id');
                })
				->leftjoin('school_wise_subjects AS sub', 'sub.school_subject_row_id', '=', 'mei.subject_row_id')
				->leftjoin('users AS us', 'us.user_id', '=', 'mei.admin_row_id')
				->leftjoin('education_version AS ev', 'ev.version_row_id', '=', 'mei.version_row_id')
				->select('mei.*', 'mc.master_class_row_id', 'mc.class_name', 'ms.master_section_row_id', 'ms.section_title', 'sub.school_subject_row_id', 'sub.subject_title', 'sub.subject_short_tag', 'us.user_id', 'us.name', 'ev.version_row_id', 'ev.version_title')
				->where('mei.admin_row_id', $admin_row_id)
				->where('mei.school_row_id',session('school_id'))
				->whereIn('mei.academic_session_year', $academic_session_year)
				->whereIn('ms.academic_session_year', $academic_session_year)
				->orderBy('mei.created_at', 'desc')->get();
			} else {
				$major_exam_info = DB::table('imatap_exam_infos As mei')
				->leftjoin('master_classes AS mc', 'mc.master_class_row_id', '=', 'mei.class_row_id')
				->leftjoin('school_and_class_wise_sections as ms', function($join) {
					$join->on('ms.master_section_row_id', '=', 'mei.section_row_id')
						 ->on('ms.school_id', '=', 'mei.school_row_id')
						 ->on('ms.master_class_row_id', '=', 'mei.class_row_id');
                })
				->leftjoin('school_wise_subjects AS sub', 'sub.school_subject_row_id', '=', 'mei.subject_row_id')
				->leftjoin('users AS us', 'us.user_id', '=', 'mei.admin_row_id')
				->leftjoin('education_version AS ev', 'ev.version_row_id', '=', 'mei.version_row_id')
				->select('mei.*', 'mc.master_class_row_id', 'mc.class_name', 'ms.master_section_row_id', 'ms.section_title', 'sub.school_subject_row_id', 'sub.subject_title', 'sub.subject_short_tag', 'us.user_id', 'us.name', 'ev.version_row_id', 'ev.version_title')
				->where('mei.school_row_id', session('school_id'))
				->whereIn('mei.academic_session_year', $academic_session_year)
				->whereIn('ms.academic_session_year', $academic_session_year)
				->orderBy('mei.created_at', 'desc')->get();
			}
		}

		return $major_exam_info;
	}

	public function getAllPredefinedComments() {
		return $predefined_comments = DB::table('predefined_comments')->get();
	}

	public function getAllExamRemarks() {
		return $exam_remarks = DB::table('exam_remarks')->get();
	}

	public function allAssetHeads($showAllocation = false, $showExpense = false, $budget_year = 0, $check_head_active_status = 0, $asset_head_row_id=0, $from_date='', $to_date='') {
		$this->output = array();
		$main_heads = MasterAssetHead::where('is_deleted',0)->get();
		foreach ($main_heads as $main) {
			$this->output[] = $main;
			if($main->has_child == 1){
				$child_heads = ChildAssetHead::where('master_asset_head_row_id',$main->master_asset_head_row_id)->where('is_deleted',0)->get();
				foreach ($child_heads as $child) {
					$this->output[] = $child;
					if($showAllocation) {
						$child->total_allocation = $this->totalAssetAllcations($child->child_asset_head_row_id, $budget_year, $from_date, $to_date);

					}
					if($child->has_child == 1){
						$childs_child_heads = ChildAssetHead::where('master_asset_head_row_id',$child->child_asset_head_row_id)->where('is_deleted',0)->get();
						foreach ($childs_child_heads as $grand_child) {
							$this->output[] = $grand_child;
							if($showAllocation) {
								$grand_child->total_allocation = $this->totalAssetAllcations($grand_child->child_asset_head_row_id, $budget_year, $from_date, $to_date);

							}
						}
					}

				}
			}

		}
		$output = $this->output;
		$this->output = array();
		return $output;

	}

	function setAssetHeadChildren($haystack, $parentHeadId, $showAllocation=0, $showExpense=0, $budget_year=0, $from_date='', $to_date = '')
	{
		if( count($haystack))
		{
			foreach($haystack as $head)
			{
				if($head->master_asset_head_row_id && $head->master_asset_head_row_id== $parentHeadId)
				{

					if($head->has_child)
					{

						$this->output[] = $head;
						$this->setAssetHeadChildren($haystack, $head->master_asset_head_row_id);
					}
					else
					{
						if($showAllocation) {
							$head->total_allocation = $this->totalAssetAllcations($head->child_asset_head_row_id, $budget_year, $from_date, $to_date);
						}
						if($showExpense) {
							$head->total_expense = $this->totalExpense($head->child_asset_head_row_id, $budget_year);
						}
						$this->output[] = $head;
					}
				}
			}
		}
	}

	public function totalAssetAllcations($asset_head_row_id, $budget_year, $from_date, $to_date) {

		if($from_date && $to_date) {
			return  \App\Models\AssetAllocation::where('child_asset_head_row_id', $asset_head_row_id)
											->whereBetween('allocation_at', array($from_date, $to_date))
											->sum('amount');
		} else {
			return  \App\Models\AssetAllocation::where('child_asset_head_row_id', $asset_head_row_id)
											     ->sum('amount');
		}


	}

	public function getStudentMasterExamResult($academic_session_row_id, $current_class, $current_shift, $current_section, $mastertermid, $student_row_id) {

    	return \App\Models\StudentsFinalResult::where([ ['academic_session_row_id', $academic_session_row_id], ['class_row_id', $current_class], ['shift_row_id', $current_shift], ['section_row_id', $current_section], ['exam_master_term_row_id', $mastertermid], ['student_row_id', $student_row_id] ])->first();

    }

    public function getSectionTitleByClass($class,$version,$session,$current_section=null){

		if($session == null){
			$school_wise_section = SchoolWiseSection::where([['school_id',session('school_id')],['master_class_row_id',$class],['version_row_id',$version],['is_deleted',0]])->get();
		}else{
			$session_details = SchoolWiseAcademicSession::where([['school_wise_academic_session_row_id', $session], ['school_id', session('school_id')]])->first();
			$school_wise_section = SchoolWiseSection::where([['school_id',session('school_id')],['master_class_row_id',$class],['version_row_id',$version],['academic_session_year', $session_details->academic_session_year],['is_deleted',0]])->get();
		}

		$html = "";
		$html .= "<option value=''>Select Section</option>";
		foreach($school_wise_section as $section) {
			if(isset($current_section) && ($section->master_section_row_id == $current_section)) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}

			$html .= "<option value=".$section->master_section_row_id." ".$selected.">".$section->section_title."</option>";
		}
		echo $html;

	}

	public function getGroupListDropdown2($version_row_id, $class_row_id, $current_group_row_id = NULL){

		if($version_row_id != 4 && ($class_row_id == 9 || $class_row_id == 10 || $class_row_id == 11 || $class_row_id == 12)){
			$is_general = 0;
		} else {
			$is_general = 1;
		}
		$education_group_list = getEducationGroupList($is_general);
		$html = "<option value=''>Select Group</option>";
		if(isset($education_group_list) && !empty($education_group_list)){
			foreach($education_group_list as $group_row){
				if(isset($current_group_row_id) && !empty($current_group_row_id) && ($group_row->group_row_id == $current_group_row_id)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$group_row->group_row_id."' ".$selected.">".$group_row->group_title."</option>";
			}
		}
		return $html;
	}

	public function getSubjectListBySection($session_id,$version_row_id,$shift,$master_class_row_id,$section,$department, $current_subject_row_id = null){
		$school_id = session('school_id');

		$session = SchoolWiseAcademicSession::where([['school_wise_academic_session_row_id',$session_id],['school_id',$school_id]])->first();
		$html = '';
		$allsubjectbySection = DB::table('school_wise_subjects AS swsub')
				->leftjoin('class_wise_subject AS cwsub', 'swsub.school_subject_row_id', '=', 'cwsub.school_wise_subjects_row_id')
				->select('swsub.subject_title', 'cwsub.class_wise_subject_row_id','swsub.master_subject_row_id')
				->where([ ['cwsub.master_class_row_id', $master_class_row_id], ['cwsub.version_row_id', $version_row_id], ['cwsub.academic_session', $session_id], ['cwsub.group_row_id', $department],['cwsub.school_id',$school_id],['swsub.is_fake', 0],['swsub.school_id',$school_id]])
				->orderBy('cwsub.sort_order', 'asc')->get();
		$html = "<option value=''>Select Subject</option>";
		if(isset($allsubjectbySection) && !empty($allsubjectbySection)){
			foreach($allsubjectbySection as $subject_row){
				if(isset($current_subject_row_id) && !empty($current_subject_row_id) && ($subject_row->master_subject_row_id == $current_subject_row_id)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$subject_row->master_subject_row_id."' class_wise_subject_row_id='".$subject_row->class_wise_subject_row_id."'".$selected.">".$subject_row->subject_title."</option>";
			}
		}
		return $html;

	}

	//For payroll By Santanu
	public function getBasicSalary($admin_row_id,$salary_month_start_date,$salary_month_end_date) {
		$basic_salary = 0;
        $salary_row = \App\Models\PayrollSalarySetting::where([['admin_row_id', $admin_row_id],['school_id',session('school_id')]])->whereDate('salary_effected_from','<=', $salary_month_start_date)->orderBy('payroll_row_id', 'DESC')->first();
        if($salary_row) {
            $basic_salary = $salary_row->basic_salary;
        }
        return $basic_salary;
	}

	public function getAllowanceBreakdowns($admin_row_id,$salary_month_start_date,$salary_month_end_date){
        $allowance_breakdowns = '';
        $salary_row = \App\Models\PayrollSalarySetting::where([['admin_row_id', $admin_row_id],['school_id',session('school_id')]])->whereDate('salary_effected_from','<=', $salary_month_start_date)->orderBy('payroll_row_id', 'DESC')->first();
        if($salary_row) {
            $allowance_breakdowns = $salary_row->allowance_breakdowns;
        }
        return $allowance_breakdowns;
	}

	public function getDeductionBreakdowns($admin_row_id,$salary_month_start_date,$salary_month_end_date) {
        $deduction_breakdowns = '';
        $salary_row = \App\Models\PayrollSalarySetting::where([['admin_row_id', $admin_row_id],['school_id',session('school_id')]])->whereDate('salary_effected_from','<=', $salary_month_start_date)->orderBy('payroll_row_id', 'DESC')->first();
        if($salary_row) {
            $deduction_breakdowns = $salary_row->deduction_breakdowns;
        }
        return $deduction_breakdowns;
    }

    public function getBonusAmount($admin_row_id,$salary_month_start_date,$salary_month_end_date) {
        $bonus_amount = 0;
        $salary_row = \App\Models\PayrollSalarySetting::where([['admin_row_id', $admin_row_id],['school_id',session('school_id')]])->whereDate('salary_effected_from','<=', $salary_month_start_date)->orderBy('payroll_row_id', 'DESC')->first();
        if($salary_row) {
            $bonus_amount = $salary_row->bonus_amount;
        }
        return $bonus_amount;
    }



    public function getGrossSalary($admin_row_id,$salary_month_start_date,$salary_month_end_date) {
        $gross_salary = 0;
        $salary_row = \App\Models\PayrollSalarySetting::where([['admin_row_id', $admin_row_id],['school_id',session('school_id')]])->whereDate('salary_effected_from','<=', $salary_month_start_date)->orderBy('payroll_row_id', 'DESC')->first();
        if($salary_row) {
            $gross_salary = $salary_row->gross_salary;
        }
        return $gross_salary;

    }

    public function getPayableSalary($admin_row_id,$salary_year,$salary_month,$salary_month_start_date,$salary_month_end_date) {
        $payable_salary = 0;
        $salary_row = \App\Models\PayrollSalarySetting::where([['admin_row_id', $admin_row_id],['school_id',session('school_id')]])->whereDate('salary_effected_from','<=', $salary_month_start_date)->orderBy('payroll_row_id', 'DESC')->first();

        if(isset($salary_row->deduction_breakdowns) && $salary_row->deduction_breakdowns){
        	$deduction_breakdowns = $salary_row->deduction_breakdowns;
        	$data['deduction'] = json_decode($deduction_breakdowns);
        	$cancel_deduct_amount_total = 0;

        	foreach ($data['deduction'] as $key => $value) {
	           if($salary_year <= $value->year_to || $value->year_to == NULL){
	           		if($salary_month <= $value->month_to || $value->month_to == NULL){
	           			$cancel_deduct_amount_total = $cancel_deduct_amount_total + $value->output;
	           		}
	           }
	        }
	        if($salary_row) {
	            $payable_salary = $salary_row->gross_salary - $cancel_deduct_amount_total;
	        }
        }
        else{
        	if($salary_row) {
	            $payable_salary = $salary_row->payable_salary;
	        }
        }




        return $payable_salary;

    }

    public function getStudentListDropdownSuperAdmin($session_id, $school_id, $version_row_id, $class_row_id,$section_id, $current_group_row_id, $student_row_id = null){

    	$sessionSplice = explode('-',$session_id);
    	$academic_session_year = $sessionSplice[0];
    	$month = $sessionSplice[1];

    	$session_row_id = SchoolWiseAcademicSession::where([['academic_session_year',$academic_session_year],['month',$month],['school_id',$school_id]])->first()->school_wise_academic_session_row_id;

    	$student_list = Student::with('student_name')->where([['school_id',$school_id],['academic_session_year',$session_row_id],['academic_version',$version_row_id],['current_class',$class_row_id],['current_section',$section_id],['current_department',$current_group_row_id],['active_status',1],['is_deleted',0]])->orderby('current_rollnumber','ASC')->get();

		$html = "<option value=''>Select Student</option>";
		if(isset($student_list) && !empty($student_list)){
			foreach($student_list as $student_row){
				if(isset($student_row_id) && !empty($student_row_id) && ($student_row->student_row_id  == $student_row_id)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}

				$html .="<option value='".$student_row->student_row_id."' ".$selected.">".$student_row->student_name->name."</option>";
			}
		}
		return $html;
	}

	public function getMarksWeightByClass($version_row_id, $school_id, $master_class_row_id, $classwise_master_term) {
		$marks_weight = ExamMarksWeight::where([['version_row_id',$version_row_id],['school_row_id',$school_id],['class_row_id',$master_class_row_id],['exam_master_term_row_id',$classwise_master_term]])->first();

		return $marks_weight;
	}

    public function getMajorExamListBySection($session_id,$version_row_id,$shift,$master_class_row_id,$section,$department){
        $school_id = session('school_id');

        $session = SchoolWiseAcademicSession::where([['school_wise_academic_session_row_id',$session_id],['school_id',$school_id]])->first();
        $html = '';
        $allMajorExambySection = DB::table('central_exam_majors')
            ->where([['academic_session_year',$session->academic_session_year],['academic_session_month',$session->month],['master_shift_id',$shift],['is_active',1]])
            ->whereJsonContains('school_id',$school_id)
            ->whereJsonContains('master_class_id',$master_class_row_id)
            ->whereJsonContains('master_section_id',$section)
            ->get();
        $html = "<option value='0'>Select Major Exam</option>";
        if(isset($allMajorExambySection) && !empty($allMajorExambySection)){
            foreach($allMajorExambySection as $major_exam_row){
                if(isset($current_subject_row_id) && !empty($current_subject_row_id) && ($major_exam_row->master_subject_row_id == $current_subject_row_id)){
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }

                $html .="<option value='".$major_exam_row->major_exam_id."' major_exam_row_id='".$major_exam_row->major_exam_id."'".$selected.">".$major_exam_row->major_exam_name."</option>";
            }
        }
        return $html;

    }

    public function getClassWiseMasterTerm($session_id, $version_id, $class_row_id){
    	$school_id = session('school_id');

    	$academic_session_year = DB::table('school_wise_academic_session')->where('school_wise_academic_session_row_id', $session_id)->value('academic_session_year');

    	$entry_years_master = SchoolwiseMasterTerm::orderBy('entry_year', 'desc')->groupBy('entry_year')->pluck('entry_year')->toArray();

    	$classwise_master_terms = DB::table('classwise_master_term AS cmt')
		->leftjoin('schoolwise_master_term AS smt', 'cmt.schoolwise_master_term_row_id', '=', 'smt.schoolwise_master_term_row_id')
		->leftjoin('master_term AS mt', 'mt.exam_master_term_row_id', '=', 'smt.master_term_row_id')
		->select('cmt.*', 'smt.schoolwise_master_term_row_id', 'mt.exam_master_term_row_id', 'mt.exam_category_title')
		->where([ ['cmt.version_row_id', $version_id], ['cmt.entry_year', $academic_session_year], ['cmt.master_class_row_id', $class_row_id], ['cmt.school_id', $school_id] ])
		->orderBy('mt.exam_master_term_row_id', 'asc')->get()->toArray();
		//$classwiseMasterterm = $this->my_array_unique($classwise_master_terms, 'exam_master_term_row_id');

		$html = "<option value='0'>Select Master Term</option>";

		if(isset($classwise_master_terms) && !empty($classwise_master_terms)){
            foreach($classwise_master_terms as $major_term_data){

                $html .="<option value='".$major_term_data->exam_master_term_row_id."'>".$major_term_data->exam_category_title."</option>";
            }
        }
        return $html;
    }

	function getClassWiseMasterTermJson($session_id, $version_id, $class_row_id, $school_id = NULL){
    	if($school_id == NULL){
    		$school_id = session('school_id');	
    	}
    

    	$entry_years_master = SchoolwiseMasterTerm::orderBy('entry_year', 'desc')->groupBy('entry_year')->pluck('entry_year')->toArray();

    	$classwise_master_terms = DB::table('classwise_master_term AS cmt')
		->leftjoin('schoolwise_master_term AS smt', 'cmt.schoolwise_master_term_row_id', '=', 'smt.schoolwise_master_term_row_id')
		->leftjoin('master_term AS mt', 'mt.exam_master_term_row_id', '=', 'smt.master_term_row_id')
		->select('cmt.*', 'smt.schoolwise_master_term_row_id', 'mt.exam_master_term_row_id', 'mt.exam_category_title', 'mt.exam_short_tag')
		->where([ ['cmt.version_row_id', $version_id], ['cmt.entry_year', $entry_years_master[0]], ['cmt.master_class_row_id', $class_row_id], ['cmt.school_id', $school_id] ])
		->orderBy('smt.term_sort_order', 'asc')->get()->toArray();
		//$classwiseMasterterm = $this->my_array_unique($classwise_master_terms, 'exam_master_term_row_id');
        return $classwise_master_terms;
    }


	public function getAllSchoolIdAndNameSuperAdmin($id)
	{
		$allschools = DB::table('schools')->select('school_id','school_name')->get();

		$selectedSchools = collect(json_decode(DB::table('school_wise_holidays')->where('holiday_row_id', $id)->first()->school_id));

		$collection = [];
		foreach ($selectedSchools as $key => $value) {
			$school_name = DB::table('schools')->where([['school_id', $value]])->first()->school_name;
			$collection[$value] = $school_name;
		}
		//dd($collection);

		$html = "<option value='' disabled>Select School</option>";

		if(isset($allschools) && !empty($allschools)){
			foreach($allschools as $school_row){
				foreach ($selectedSchools as $key => $value){
					if(isset($school_row->school_id) && !empty($value) && ($school_row->school_id == $key)){
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}
				}
				$html .="<option value='".$school_row->school_id."' ".$selected.">".$school_row->school_name."</option>";
			}
		}
		return $html;
	}

	public function getRecentYearsSuperAdmin($id)
	{
		$current_year = Carbon::now()->year;
		$years = [$current_year-2, $current_year-1, $current_year, $current_year+1, $current_year+2];

		$html = "<option value='' disabled selected>Select Year</option>";
		if(isset($years) && !empty($years)){
			foreach($years as $year_row){
				$selected = '';
				$html .="<option value='".$year_row."' ".$selected.">".$year_row."</option>";
			}
		}
		return $html;
	}
	public function getAssetSubCategory($asset_category_row_id,$current_sub_category_id)
	{
		$current_year = Carbon::now()->year;
        $school_id = session('school_id');
        $sub_category = DB::table('asset_sub_categories')->where([['asset_category_row_id',$asset_category_row_id],['school_id',$school_id],['is_deleted',0]])->get();
		$html = "<option value='-1' selected>Select Sub-Category</option>";
		if(isset($sub_category) && !empty($sub_category)){
			foreach($sub_category as $value){
                if(isset($current_sub_category_id) && ($value->asset_sub_category_row_id == $current_sub_category_id)) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
				$html .="<option value='".$value->asset_sub_category_row_id."' ".$selected.">".$value->title."</option>";
			}
		}
		return $html;
	}

	function getEvaluationTime($session, $version_row_id){
		$evaluation_settings = EvaluationSettings::where([['school_id', session('school_id')],['academic_session_id', $session], ['academic_version_id', $version_row_id], ['is_active', 1]])->first();

        $from_array = json_decode($evaluation_settings->from_date);
        $to_array = json_decode($evaluation_settings->to_date);

        $from_date = array();
        $to_date = array();

        foreach ($from_array as $row){
            foreach ($row as $key => $value){
				if($value != null){
					$from_date[$key] =  $value;
				}
            }
        }

        foreach ($to_array as $row){
            foreach ($row as $key => $value){
				if($value != null){
					$to_date[$key] =  $value;
				}
            }
        }

        $month_time = array();
        foreach ($from_date as $f_key => $f_val){
            $i= 1;
            $f_key_int = (int)$f_key+1;
            $f_key_string = (string)$f_key_int;
            foreach ($to_date as $t_key => $t_val){
                if( $f_key == $t_key ){
                    $month_time[] = $f_val.','.$t_val;
                }elseif($f_key_string == $t_key){
                    $month_time[] = $f_val.','.$to_date[$f_key_string];
                }
                $i++;
            }
        }

		$html = "<option value='' disabled selected>Select Evalution Time</option>";

		if(isset($month_time) && !empty($month_time)){
			foreach($month_time as $value){
				$splitName = explode(',', $value);
				$html .="<option value='".$value."'>".$splitName[0]." to ".$splitName[1]."</option>";
			}
		}

		return $html;
	}

	public function getAllTeacherList(){
		$teacher_list =  $this->getTacherList();
		$html = "<option value='' disabled selected>Select Teacher</option>";
		if(isset($teacher_list) && !empty($teacher_list)){
			foreach($teacher_list as $value){
				$html .="<option value='".$value->admin_row_id."'>".$value->name."</option>";
			}
		}
		return $html;
	}

	public function getClassByTeacher($admin_row_id){
		$SubjectTeacher = SubjectTeacher::where('school_id',session('school_id'))->where('admin_row_id',$admin_row_id)->get();

		$html = "<option value='' disabled selected>Select Class</option>";

		$class_arr = array();
		 foreach ($SubjectTeacher as $val){
            $class = MasterClass::where('master_class_row_id', $val->master_class_row_id )->first();

			if($class){
				if(!in_array($class, $class_arr)){
					$class_arr[] = $class;
				}
			}
        }
		if(count($class_arr)>0){
			foreach ($class_arr as $ar){
				$html .="<option value='".$ar->master_class_row_id."'>".$ar->class_name."</option>";
			}
		}
		return $html;
	}

	public function getSectionByTeacher($admin_row_id, $class_id){
		$SubjectTeacher = SubjectTeacher::where('school_id',session('school_id'))->where([['admin_row_id',$admin_row_id],['master_class_row_id', $class_id]])->get();

		$html = "<option value='' disabled selected>Select Section</option>";

		$sec_arr = array();
		 foreach ($SubjectTeacher as $val){
			$section = SchoolWiseSection::where([['school_id',session('school_id')],['master_section_row_id', $val->master_section_row_id],['master_class_row_id',$class_id],['version_row_id',$val->version_row_id],['is_deleted',0]])->first();

			if($section){
				if(!in_array($section, $sec_arr)){
					$sec_arr[] = $section;
				}
			}
        }
		if(count($sec_arr)>0){
			foreach ($sec_arr as $ar){
				$html .="<option value='".$ar->master_section_row_id."'>".$ar->section_title."</option>";
			}
		}
		return $html;
	}

	public function getSubjectByTeacher($admin_row_id, $class_id, $section_id){
		$SubjectTeacher = SubjectTeacher::where('school_id',session('school_id'))->where([['admin_row_id',$admin_row_id],['master_class_row_id', $class_id],['master_section_row_id', $section_id]])->get();

		$html = "<option value='' disabled selected>Select Subject</option>";

		$sub_arr = array();
		 foreach ($SubjectTeacher as $val){
			$subject = Subject::where('school_subject_row_id', $val->master_subject_row_id)->first();

			if($subject){
				if(!in_array($subject, $sub_arr)){
					$sub_arr[] = $subject;
				}
			}
        }
		if(count($sub_arr)>0){
			foreach ($sub_arr as $ar){
				$html .="<option value='".$ar->school_subject_row_id."'>".$ar->subject_title."</option>";
			}
		}
		return $html;
	}

	public function getFloorByBuilding($building_id, $floor_id){
		$floor = AssetFloor::where([['school_id',session('school_id')],['asset_building_row_id', $building_id],['is_deleted',0]])->get();

		$html = "<option value='' disabled selected>Select Floor</option>";

		foreach ($floor as $val){
			if($floor_id !=  null){
				if($val->asset_floor_row_id == $floor_id){
					$selected = 'selected';
				}else{
					$selected = '';
				}
				$html .="<option value='".$val->asset_floor_row_id."'".$selected.">".$val->floor_number."</option>";
			}else{
				$html .="<option value='".$val->asset_floor_row_id."'>".$val->floor_number."</option>";
			}
        }

		return $html;
	}

	public function getAssetItem($asset_category_row_id, $asset_sub_category_row_id, $asset_id){
		$assets = Asset::where([['school_id',session('school_id')],['asset_category_row_id', $asset_category_row_id],['asset_sub_category_row_id', $asset_sub_category_row_id], ['is_deleted',0]])->get();

		$html = "<option value='' disabled selected>Select Item</option>";

		foreach ($assets as $val){
			if(isset($asset_id) && $asset_id!=  null){
				if($val->asset_row_id == $asset_id){
					$selected = 'selected';
				}else{
					$selected = '';
				}
				$html .="<option value='".$val->asset_row_id."'".$selected.">".$val->item_name."</option>";
			}else{
				$html .="<option value='".$val->asset_row_id."'>".$val->item_name."</option>";
			}

        }

		return $html;
	}

	public function getAssetItemQuantity($id){
		$asset = Asset::findorfail($id);
		$quantity = $asset->quantuty;
		$allocated_quantity = AssetAllocation20::where([['asset_id', $id],['is_deleted', 0]])->pluck('quantity')->toArray();
		$total_allocated_quantity = array_sum($allocated_quantity);

		$avaiable_quantity = $quantity - $total_allocated_quantity;
		return $avaiable_quantity;
	}

	public function getAssetItemStatus($assetId, $allocationId){

		if($allocationId == null){
			$asset = Asset::findorfail($assetId)->asset_status;
		}else{
			$asset = $allocationId;
		}

		$AssetStatus = CentralAssetStatus::where('is_deleted',0)->get();

		$html = "<option value='' disabled selected>Select Condition</option>";

		foreach ($AssetStatus as $status){
			if(isset($asset) && $asset !=  null){
				if($asset == $status->asset_status_row_id){
					$selected = 'selected';
				}else{
					$selected = '';
				}
				$html .="<option value='".$status->asset_status_row_id."'".$selected.">".$status->status_title."</option>";
			}else{
				$html .="<option value='".$status->asset_status_row_id."'>".$status->status_title."</option>";
			}

        }

		return $html;
	}

	public function getAssetItemUnit($assetId , $allocationId){
		if( $allocationId == null ){
			$asset = Asset::findorfail($assetId)->asset_unit;
		}else{
			$asset = $allocationId;
		}


		$unit = CentralAssetUnit::where([['asset_unit_row_id',$asset],['is_deleted',0]])->first();

		$html = "<option value='' selected disabled>Select Unit</option>";

		if($unit){
			$html ="<option value='".$unit->asset_unit_row_id."' selected>".$unit->unit_title."</option>";
		}else{
			$html = "<option value='' selected disabled>Select Unit</option>";
		}

		return $html;
	}

	public function getAssetRoomId($building_id, $floor_id, $room_id){
		$rooms = AssetRoom::where([['school_id',session('school_id')],['asset_building_row_id', $building_id],['asset_floor_row_id', $floor_id], ['is_deleted',0]])->get();

		$html = "<option value='' disabled selected>Select Room</option>";

		foreach ($rooms as $room){
			if(isset($room_id) && $room_id !=  null){
				if($room->asset_room_row_id == $room_id){
					$selected = 'selected';
				}else{
					$selected = '';
				}
				$html .="<option value='".$room->asset_room_row_id."'".$selected.">".$room->room_number."</option>";
			}else{
				$html .="<option value='".$room->asset_room_row_id."'>".$room->room_number."</option>";
			}

        }

		return $html;
	}

	public function getAllVersionList(){
		$version_list = \App\Models\EducationVersion::orderBy('version_row_id', 'asc')->get();
		return $version_list;
	}

	public function updateSubjectTeacher($master_exam_row_id, $admin_row_id){
		$affected = DB::table('master_exam_infos') ->where('master_exam_row_id', $master_exam_row_id) ->update(['admin_row_id' => $admin_row_id]);

		return '1';
	}
	
	public function updateSubjectTeacherImatap($imatap_exam_row_id, $admin_row_id){
		$affected = DB::table('imatap_exam_infos') ->where('imatap_exam_row_id', $imatap_exam_row_id) ->update(['admin_row_id' => $admin_row_id]);

		return '1';
	}

	public function getAdminDetails($admin_row_id){
		$adminDetails = DB::table('admins As a')
				->leftjoin('users','a.admin_row_id','=','users.user_id')
				->select('a.*', 'users.*')
				->where([
					['a.active_status', 1], ['a.school_id', session('school_id')],['a.admin_row_id', $admin_row_id]
				])->first();
		return $adminDetails;
	}

	public function getTeacherClassInfo($session_id, $version_row_id, $shift_id, $admin_row_id){
		$session = SchoolWiseAcademicSession::findorfail($session_id);

		$classIds =  SubjectTeacher::select('master_class_row_id')->distinct()->where([ ['school_id',session('school_id')], ['version_row_id', $version_row_id], ['academic_session_year', $session->academic_session_year], ['admin_row_id', $admin_row_id] ])->get()->toArray();

		$class_wise_subjects = array();
		foreach ($classIds as $key => $value) {
			$class_row_id = $value['master_class_row_id'];

			$Class_wise_subject_list = \App\Models\ClasswiseSubject::where([['version_row_id', $version_row_id],['master_class_row_id', $class_row_id],['academic_session', $session_id], ['in_exam', 1]])->get();

			foreach ($Class_wise_subject_list as $data) {
				$class_wise_subjects[$class_row_id][] = $data->school_wise_subjects_row_id;
			}
		}

		//dd($class_wise_subjects);
		
		$teacherSubject = SubjectTeacher::with('master_classes', 'master_subject', 'class_shift', 'class_section', 'class_teacher')
							->where([ ['school_id',session('school_id')], ['version_row_id', $version_row_id], ['academic_session_year', $session->academic_session_year], ['admin_row_id', $admin_row_id] ])
							->orderBy('created_at', 'DESC')->get();

		$tsubject = array();					

		foreach ($teacherSubject as $tdata) {
			$tsubject[$tdata->master_class_row_id][] = $tdata;
		}

		//dd($tsubject);

		$finalteacherSubject = array();
		foreach ($tsubject as $classid => $cdata) {
			foreach ($cdata as $value) {
				if(in_array($value->master_subject_row_id, $class_wise_subjects[$classid])){
					$finalteacherSubject[$classid][] = $value;
				}
			}
		}

		//dd($finalteacherSubject);

		$html = "";
		$html = "<option value='0'>Select All</option>";
		if(isset($finalteacherSubject) && !empty($finalteacherSubject)){
			foreach($finalteacherSubject as $cid => $cldata){
				foreach ($cldata as $cvalue) {
					$section_id = $cvalue->master_section_row_id;
					$subject_id = $cvalue->master_subject_row_id;
					$class_name = $cvalue->master_classes->class_name;
					$section_name = $cvalue->class_section->section_title;
					$sub_title = $cvalue->master_subject->subject_title;
					$opt_val = $cid.'|'.$section_id.'|'.$subject_id;
					$details = "Class: $class_name | Section: $section_name | Subject: $sub_title";
					$html .="<option value=".$opt_val.">".$details."</option>";
				}
			}
		}
		return $html;
	}

	public function getEvaluationMonthBySession($version_row_id, $academic_session){

		$schoolWiseEvaluationSetting = \App\Models\teacher_evaluation\SchoolWiseEvaluationSetting::where([['academic_session_id',$academic_session],['school_id',session('school_id')]])->first();
        $evaluation_months = json_decode($schoolWiseEvaluationSetting['from_date']);
        
        foreach($evaluation_months as $key=>$mdata){
          foreach($mdata as $month=>$date){
              $month_name[$month] = $this->monthNameEnBn($month);  
          } 
        }
        //dd($month_name);

		$html = "<option value='' disabled selected>Select Month</option>";

		foreach ($month_name as $monthid =>$mdata){
			$html .="<option value='".$monthid."'>".$mdata['en']."</option>";
        }

		return $html;
	}


}
