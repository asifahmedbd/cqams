<?php
namespace App\Libraries;
use Illuminate\Support\Facades\Input;
use Image;
use DB;
use File;
use App\Models\Section;
use App\Models\Student;
use App\Models\ApiToken;
use App\Models\MasterClass;
use App\Models\EducationVersion;
use App\Models\MasterTerm;
use App\Models\ExamGrade;
use App\Models\PredefinedComment;
use App\Models\ExamRemark;
use App\Models\ClasswiseSubject;
use App\Models\TermProcessedData;
use App\Models\TermProcessedDataFlag;
use App\Models\StudentsOptionalSubject;
use App\Models\StudentsFinalResult;
use App\Models\StudentsFinalResultFlag;
use App\Libraries\Common;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use URL;
use App\Models\OnlineExamList;
use App\Models\OnlineExamQuestion;
use App\Models\OnlineExamAnswer;
use App\Models\OnlineExamSubmission;
use App\Models\SchoolWiseAcademicSession;
use App\Models\MasterSection;
use App\Models\SchoolWiseSection;

class ApiLibrary {

	public function getSchool($school_id){
		$data = DB::table('schools')
					->where('school_id',$school_id)
					->select('school_row_id','school_name')
					->first();

		return $data;
	}

	public function getAllSchool(){
		$data = DB::table('schools')->get();

		return $data;
	}

	public function getActiveVersion($version_row_id){

		$data = DB::table('education_version')
					->where([['status','=','1'],['version_row_id',$version_row_id]])
					->select('version_row_id','version_title')
					->first();
		return $data;
	}

	public function getAllActiveVersion(){

		$data = DB::table('education_version')->where([['status','=','1']])->get();
		return $data;
	}


	public function getActiveSession($academic_session_row_id){
		$data = DB::table('academic_session')
					->where([['status','=','1'],['academic_session_row_id','=',$academic_session_row_id]])
					->select('academic_session_row_id','academic_session_title','academic_session_year')
					->first();

		return $data;
	}

	public function getAllActiveSession(){
		$data = DB::table('academic_session')->where([['status','=','1']])->get();

		return $data;
	}

	public function getClass($class_row_id){

		$data = DB::table('master_classes')
					->where('class_row_id',$class_row_id)
					->select('class_row_id','class_name')
					->first();

		return $data;
	}

	public function getAllClass(){

		$data = DB::table('master_classes')->get();

		return $data;
	}


	public function getSitCapacity($school_row_id, $version_row_id, $academic_session_row_id, $class_row_id){

		$data = DB::table('class_student_capacities')
						->where([['school_row_id',$school_row_id],['version_row_id',$version_row_id],['academic_session_row_id',$academic_session_row_id],['class_row_id',$class_row_id]])
						->select('class_capacity_row_id','student_capacity')
						->first();

		return $data;
	}

	public function totalStudentInClass($school_row_id, $version_row_id, $academic_session_row_id, $class_row_id){

		$data = DB::table('students')
					->where([['school_row_id',$school_row_id],['academic_version',$version_row_id],['current_session',$academic_session_row_id],['current_class',$class_row_id]])
					->count();

		return $data;
	}

	public function getClasswiseSection($class_id, $version_row_id){

		$section_details = DB::table('school_sections')
								->where([['version_row_id',$version_row_id],['class_row_id',$class_id]])
								->whereIn('academic_session',[1,2])
								->select('section_row_id','section_name')
								->get();

		// foreach ($section_details as $key => $value) {
		// 	$sec_id[] = $value->section_row_id;
		// }

		// $sec = $section_details;

		return $section_details;
	}

	public function sectionWiseTotalStudent($class_id, $version_row_id, $section_id){

		$total_student = DB::table('students')
						->where([['academic_version',$version_row_id],['current_class',$class_id],['current_section',$section_id]])
						->whereIn('current_session',[1,2])
						->count();

		return $total_student;
	}

	public function getAdmissionSeatStatus($s_id, $version_row_id){

		$school_classes = getSchoolClasses($version_row_id);
		$sections = array();
		foreach ($school_classes as $key => $value) {
			$class_id = $value->class_row_id;
			
			$sections[$class_id] = $this->getClasswiseSection($class_id, $version_row_id);

			foreach ($sections[$class_id] as $key_1 => $value_1) {
				$section_id = $value_1->section_row_id;

				$total_student[$version_row_id][$class_id][$section_id] = $this->sectionWiseTotalStudent($class_id, $version_row_id, $section_id);
			}

		}
		return $total_student;
	}


	/*
	* Check student authorization
	*/

	public function checkStudentLoginByToken($student_id, $token){
		$student_exists = $this->get_student_login_token_by_master_id($student_id, $token);
		if(isset($student_exists) && !empty($student_exists)){
			return 1;
		} else {
			return 0;
		}
	}

	
	/*
	* Check student authorization
	*/

	public function checkStudentAuth($student_id, $password){

		$teacher_exists = $this->get_teacher_row_by_master_id($student_id);
		if(isset($teacher_exists) && !empty($teacher_exists)){
			$teacher_hashed_password = $teacher_exists->password;
			if (Hash::check($password, $teacher_hashed_password)) {
				session(['school_id' => $teacher_exists->school_id]);
				return 2;
			} else {
				return 0;
			}
		} else {
			$student_exists = $this->get_student_row_by_master_id($student_id);
			if(isset($student_exists) && !empty($student_exists)){
				$hashed_password = $student_exists->password;
				if (Hash::check($password, $hashed_password)) {
					session(['school_id' => $student_exists->school_id]);
					return 1;
				} else {
					return 0;
				}
			} else {
				return 0;
			}
		} 
	}

	public function get_student_login_token_by_master_id($student_id, $token){
		return $student_exists = \App\Models\ApiToken::where([['student_id',$student_id]])->where('token','=',trim($token))->first();
	}

	public function get_teacher_row_by_master_id($student_id){
		return $teacher_exists = \App\Models\Admin::where([['admin_row_id',$student_id], ['staff_designation_category_row_id', 3],['school_id',session('school_id')]])->first();
	}

	public function get_student_row_by_master_id($student_id){
		return $student_exists = \App\Models\Student::where([['student_row_id',$student_id]])->first();
	}

	public function get_student_details_row_by_student_row_id($student_row_id){
		return $student_details_exists = \App\Models\StudentsDetail::where([['student_row_id',$student_row_id]])->first();
	}

	public function get_admin_details_row($admin_row_id){
		return $student_details_exists = \App\Models\Admin_detail::where([['admin_row_id',$admin_row_id]])->first();
	}

	public function get_student_token_by_master_id($student_id){
		$student_exists = $this->get_student_row_by_master_id($student_id);
		$token = isset($student_exists->remember_token)? $student_exists->remember_token: NULL;
		return $token;
	}

	public function updateStudentPassword($student_id, $new_password){
		$Student = $this->get_student_row_by_master_id($student_id);
		$Student->password = bcrypt($new_password);
		$Student->student_password_text = $new_password;
		$Student->updated_at = Carbon::now();
		$Student->updated_by = $Student->student_row_id;;
		$Student->save();
	}

	public function updateTeacherPassword($student_id, $new_password){
		$Admin = $this->get_teacher_row_by_master_id($student_id);
		$Admin->password = bcrypt($new_password);
		$Admin->plain_password = $new_password;
		$Admin->updated_at = Carbon::now();
		$Admin->updated_by = $Admin->admin_row_id;
		$Admin->save();
	}

	public function updateStudentToken($student_id, $token){
		$ApiToken = new ApiToken();
		$ApiToken->student_id = $student_id;
		$ApiToken->token = $token;
		$ApiToken->created_at = Carbon::now();
		$ApiToken->save();
	}

	/*
	* Get student details information
	*/

	public function getStudentDetails($student_id, $token){
		$school_id = session('school_id');
		$api_content = array();
		$Student = $this->get_student_row_by_master_id($student_id);
		if(isset($Student->student_row_id) && !empty($Student->student_row_id)){

			$array_data['academic']['name'] = getUserName($Student->student_row_id);

			$array_data['academic']['institution'] = getSchoolName();
			$school_logo = getSchoolLogo();
			if (isset($school_logo) && !empty($school_logo)) {
				$array_data['academic']['emblem'] = URL::to('/public/images/'.$school_id.'/school_logo/'.$school_logo);
			}else{
				$array_data['academic']['emblem'] = "";
			}

			$array_data['academic']['studentId'] = (string)$student_id; 
			$array_data['academic']['schoolCode'] = session('school_id');
			$array_data['academic']['address'] = getSchoolAddress();
			// $array_data['academic']['student_id'] = $student_id;
			$array_data['academic']['medium'] = get_version_name_By_version_row_id($Student->academic_version);
			$array_data['academic']['group'] = get_department_name_By_department_row_id($Student->current_department);
			$array_data['academic']['shift'] = get_shift_name_By_shift_row_id($Student->current_shift);
			// $array_data['academic']['version'] = $Student->academic_version;
			if (isset($Student->current_section) && $Student->current_section !=NULL) {
				$array_data['academic']['section'] = get_section_name_By_section_row_id($Student->current_section, $Student->current_class);
				//$array_data['academic']['section'] = get_section_name_By_section__session_school($Student->current_section, $Student->academic_session_year, $Student->school_id);
			}else{
				$array_data['academic']['section'] = "";
			}
			$array_data['academic']['class'] = get_class_name_By_class_row_id($Student->current_class);
			// $array_data['academic']['class_row_id'] = $Student->current_class;
			$array_data['academic']['roll'] = $Student->current_rollnumber;

			// add exam status in account data
			$exam_status = DB::table('portal_config AS pc')
            ->where([ ['pc.eims_module', 'exam_result'], ['pc.school_id', session('school_id')]])
            ->value('portal_flag');
            $array_data['academic']['exam_status'] = $exam_status;
			//$api_content[] = $academic;
			$student_details = $this->get_student_details_row_by_student_row_id($Student->student_row_id);
			$user_details = $this->get_user_details_row_by_student_row_id($Student->student_row_id);
			
			if(isset($user_details->date_of_birth) && !empty($user_details->date_of_birth)){
				$array_data['personal']['dob'] = $user_details->date_of_birth;
			} else {
				$array_data['personal']['dob'] = "";
			}

			if(isset($student_details->student_photo) && !empty($student_details->student_photo)){
				$imagePath = URL::to('/public/images/'.$school_id.'/student_images/'.$student_id.'/student_photo/'.$student_details->student_photo);
				$array_data['personal']['imageUrl'] = $imagePath;
			} else {
				if ($student_details->student_gender == 1) {
					$imagePath = URL::to('/public/images/common/male_student.png');
				}else{
					$imagePath = URL::to('/public/images/common/female_student.png');
				}
				$array_data['personal']['imageUrl'] = $imagePath;
			}

			if(isset($student_details->student_blood_group) && !empty($student_details->student_blood_group)){
				$blood_group = config('site_config.blood_group');
				$array_data['personal']['bloodGroup'] = $blood_group[$student_details->student_blood_group];
			} else {
				$array_data['personal']['bloodGroup'] = "";
			}
			if(isset($Student->contact) && !empty($Student->contact)){
				$array_data['personal']['contactNo'] = $Student->contact;
			} else {
				$array_data['personal']['contactNo'] = "";
			}
			if(isset($student_details->emergency_contact_number) && !empty($student_details->emergency_contact_number)){
				$array_data['personal']['emergencyContact'] = $student_details->emergency_contact_number;
			} else {
				$array_data['personal']['emergencyContact'] = "";
			}
			//$api_content[] = $personal;
			if(isset($student_details->father_name) && !empty($student_details->father_name)){
				$array_data['guardian']['name'] = $student_details->father_name;
			} else {
				$array_data['guardian']['name'] = "";
			}

			if(isset($student_details->father_mobile) && !empty($student_details->father_mobile)){
				$array_data['guardian']['contactNo'] = $student_details->father_mobile;
			} else {
				$array_data['guardian']['contactNo'] = "";
			}
			//$api_content[] = $guardian;
			$array_data['account']['type'] = "student";
			
			// CHECK for active status
			//$array_data['account']['isActive'] = true;

			$array_data['account']['oldId'] = $Student->student_id_old;
			$array_data['account']['studentId'] = (string)$student_id;

			if (isset($Student->active_status) && $Student->active_status == 0) {
				$array_data['account']['isActive'] = false;
			}else{
				$array_data['account']['isActive'] = true;
			}

			$array_data['account']['token'] = $token;
			$api_content[] = $array_data;

			$array_data['all_id']['academic_version'] = $Student->academic_version;
			$array_data['all_id']['current_class'] = $Student->current_class;
			$array_data['all_id']['current_shift'] = $Student->current_shift;
			$array_data['all_id']['current_department'] = $Student->current_department;
			$array_data['all_id']['current_section'] = $Student->current_section;
		}		
		return $array_data;
	}

	/*
	* Get student details information
	*/

	public function getTeacherDetails($student_id, $token){
		$api_content = array();
		$Teacher = $this->get_teacher_row_by_master_id($student_id);
		if(isset($Teacher->admin_row_id) && !empty($Teacher->admin_row_id)){
			
			if(isset($Teacher->admin_name) && !empty($Teacher->admin_name)){
				$array_data['academic']['name'] = $Teacher->admin_name;
			} else {
				$array_data['academic']['name'] = "";
			}
			$array_data['academic']['institution'] = getSchoolName();
			$array_data['academic']['address'] = getSchoolAddress();
			$array_data['academic']['designation'] = getDesignationNameByid($Teacher->designation_row_id);
			
			$teacher_details = $this->get_admin_details_row($Teacher->admin_row_id);
			
			if(isset($Teacher->photo_name) && !empty($Teacher->photo_name)){
				$imagePath = URL::to('/public/images/staff_images/'.$Teacher->admin_name.'/staff_photo/'.$Teacher->photo_name);
				$array_data['personal']['imageUrl'] = $imagePath;
			} else {
				$array_data['personal']['imageUrl'] = "";
			}
			if(isset($teacher_details->blood_group) && !empty($teacher_details->blood_group)){
				$blood_group = config('site_config.blood_group');
				$array_data['personal']['bloodGroup'] = $blood_group[$teacher_details->blood_group];
			} else {
				$array_data['personal']['bloodGroup'] = "";
			}
			if(isset($teacher_details->contact_1) && !empty($teacher_details->contact_1)){
				$array_data['personal']['contactNo'] = $teacher_details->contact_1;
			} else {
				$array_data['personal']['contactNo'] = "";
			}
			if(isset($teacher_details->contact_2) && !empty($teacher_details->contact_2)){
				$array_data['personal']['emergencyContact'] = $teacher_details->contact_2;
			} else {
				$array_data['personal']['emergencyContact'] = "";
			}
			
			$array_data['account']['is_validated'] = true;
			$array_data['account']['type'] = "teacher";
			$array_data['account']['token'] = $token;
			$api_content[] = $array_data;
		}		
		return $array_data;
	}

	public function getStudentListByClass($version_row_id, $class_row_id, $student_id){
    	$query = DB::table('students AS stu')
		->select('stu.student_row_id', 'stu.student_id', 'stu.student_name')
		->where([['stu.current_class', $class_row_id], ['stu.academic_version', $version_row_id]])				
		->orderBy('stu.student_id', 'asc');
		$allStudentByClass = $query->get();
		$html = "";
        $html .= "<option value=''>Select Student</option>";
        foreach($allStudentByClass as $key => $val) {
        	if(isset($student_id) && ($val->student_id == $student_id)) {
        		$selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            $html .= "<option value=".$val->student_id." ".$selected.">".$val->student_id."-".$val->student_name."</option>";
            
        }
        echo $html;
    }

    public function getCtestExamInfo($version_row_id, $class_row_id, $group_row_id, $sort_order, $type){
    	return $exam_details_row = \App\Models\CteExamClass::where([['version_row_id',$version_row_id], ['class_row_id', $class_row_id], ['group_row_id', $group_row_id], ['sort_order', $sort_order], ['type', $type]])->first();
    }

     public function getCtestMaxMark($version_row_id, $class_row_id, $group_row_id, $academic_session, $shift_id, $subject_row_id, $ct_no){

     	if($ct_no == 1){
     		$max_col = 'ct1_mark';
     	} elseif($ct_no == 2){
     		$max_col = 'ct2_mark';
     	} else {
     		$max_col = 'ct3_mark';
     	}
    	$highest_mark = \App\Models\CteExamMarkClass::where([['version_row_id',$version_row_id], ['class_row_id', $class_row_id], ['group_row_id', $group_row_id], ['academic_session_row_id', $academic_session], ['shift_row_id', $shift_id], ['subject_row_id', $subject_row_id]])->max($max_col);
    	return $highest_mark;
    }

    public function getStudentCtResultByTest($student_id, $version_row_id, $academic_session, $class_id, $shift_id, $section_id, $group_row_id){

    	$result_array = $api_content = $marks_array = array(); 
    	//DB::enableQueryLog();
    	$studentResult = \App\Models\CteExamMarkClass::where([['student_id', $student_id], ['version_row_id', $version_row_id], ['academic_session_row_id', $academic_session], ['class_row_id', $class_id], ['shift_row_id', $shift_id] , ['section_row_id', $section_id] , ['group_row_id', $group_row_id]])->orderBy('subject_row_id', 'asc')->get();
    	//$quries = DB::getQueryLog();
    	//return $quries;

		/* Process Test 1 */
		$exam_info_row = $this->getCtestExamInfo($version_row_id, $class_id, $group_row_id, 1, 1);
        $result_array['order'] = 1;
       	$result_array['title'] = isset($exam_info_row->exam_name)? $exam_info_row->exam_name: "Class Test 1";
        $result_array['type'] = "CT";
		if(isset($studentResult) && !empty($studentResult)){
			$ct1_gtotal_mark = $ct1_gtotal_obtained_mark = 0;
			foreach ($studentResult as $result_row) {
				if(isset($result_row->ct1_mark) && ($result_row->ct1_mark > -2)){
					$marks_array = array();
					$marks_array['subject'] = get_subject_name_by_subject_id($result_row->subject_row_id);
		            $marks_total = isset($result_row->ct1_total)? $result_row->ct1_total: 25;
		            $marks_array['total'] = $marks_total;
		            $ct1_gtotal_mark += $marks_total;
		            $marks_array['highest'] = $this->getCtestMaxMark($version_row_id, $class_id, $group_row_id, $academic_session, $shift_id, $result_row->subject_row_id, 1);
	              	if(isset($result_row->ct1_mark) && ($result_row->ct1_mark == -1)){
	                	$marks_array['obtained'] = "Absent"; // -1
	              	} else {
	                	$marks_obtained = isset($result_row->ct1_mark)? $result_row->ct1_mark: '';
	                	$marks_array['obtained'] = $marks_obtained;
	                	$ct1_gtotal_obtained_mark += $marks_obtained;
	              	}
	              	$result_array['marks'][] = $marks_array;
					unset($marks_array);
				}
			}
    		$result_array['starts'] = isset($exam_info_row->exam_date)? $exam_info_row->exam_date: "";
          	$result_array['total'] = $ct1_gtotal_mark;
          	$result_array['obtained'] = $ct1_gtotal_obtained_mark;
    	}
    	if(isset($result_array['marks']) && !empty($result_array['marks'])){
    		$api_content[] = $result_array;
    		unset($result_array);
    	}

    	/* Process Test 2 */
    	$result_array = array();
		$exam_info_row = $this->getCtestExamInfo($version_row_id, $class_id, $group_row_id, 2, 1);
        $result_array['order'] = 2;
       	$result_array['title'] = isset($exam_info_row->exam_name)? $exam_info_row->exam_name: "Class Test 2";
         $result_array['type'] = "CT";
		if(isset($studentResult) && !empty($studentResult)){
			$ct2_gtotal_mark = $ct2_gtotal_obtained_mark = 0;
			foreach ($studentResult as $result_row) {
				if(isset($result_row->ct2_mark) && ($result_row->ct2_mark > -2)){
					$marks_array = array();
					$marks_array['subject'] = get_subject_name_by_subject_id($result_row->subject_row_id);
		            $marks_total = isset($result_row->ct2_total)? $result_row->ct2_total: 25;
		            $marks_array['total'] = $marks_total;
		            $ct2_gtotal_mark += $marks_total;
		            $marks_array['highest'] = $this->getCtestMaxMark($version_row_id, $class_id, $group_row_id, $academic_session, $shift_id, $result_row->subject_row_id, 2);
	              	if(isset($result_row->ct2_mark) && ($result_row->ct2_mark == -1)){
	                	$marks_array['obtained'] = "Absent"; // -1
	              	} else {
	                	$marks_obtained = isset($result_row->ct2_mark)? $result_row->ct2_mark: '';
	                	$marks_array['obtained'] = $marks_obtained;
	                	$ct2_gtotal_obtained_mark += $marks_obtained;
	              	}
	              	$result_array['marks'][] = $marks_array;
					unset($marks_array);
				}
			}
    		$result_array['starts'] = isset($exam_info_row->exam_date)? $exam_info_row->exam_date: "";
          	$result_array['total'] = $ct2_gtotal_mark;
          	$result_array['obtained'] = $ct2_gtotal_obtained_mark;
    	}
    	if(isset($result_array['marks']) && !empty($result_array['marks'])){
    		$api_content[] = $result_array;
    		unset($result_array);
    	}

		/* Process Test 3 */
		$result_array = array();
		$exam_info_row = $this->getCtestExamInfo($version_row_id, $class_id, $group_row_id, 3, 1);
        $result_array['order'] = 3;
       	$result_array['title'] = isset($exam_info_row->exam_name)? $exam_info_row->exam_name: "Class Test 3";
         $result_array['type'] = "CT";
		if(isset($studentResult) && !empty($studentResult)){
			$ct3_gtotal_mark = $ct3_gtotal_obtained_mark = 0;
			foreach ($studentResult as $result_row) {
				if(isset($result_row->ct3_mark) && ($result_row->ct3_mark > -2)){
					$marks_array = array();
					$marks_array['subject'] = get_subject_name_by_subject_id($result_row->subject_row_id);
		            $marks_total = isset($result_row->ct3_total)? $result_row->ct3_total: 25;
		            $marks_array['total'] = $marks_total;
		            $ct3_gtotal_mark += $marks_total;
		            $marks_array['highest'] = $this->getCtestMaxMark($version_row_id, $class_id, $group_row_id, $academic_session, $shift_id, $result_row->subject_row_id, 3);
	              	if(isset($result_row->ct3_mark) && ($result_row->ct3_mark == -1)){
	                	$marks_array['obtained'] = "Absent"; // -1
	              	} else {
	                	$marks_obtained = isset($result_row->ct3_mark)? $result_row->ct3_mark: '';
	                	$marks_array['obtained'] = $marks_obtained;
	                	$ct3_gtotal_obtained_mark += $marks_obtained;
	              	}
	              	$result_array['marks'][] = $marks_array;
					unset($marks_array);
				}
			}
    		$result_array['starts'] = isset($exam_info_row->exam_date)? $exam_info_row->exam_date: "";
          	$result_array['total'] = $ct3_gtotal_mark;
          	$result_array['obtained'] = $ct3_gtotal_obtained_mark;
    	}
    	if(isset($result_array['marks']) && !empty($result_array['marks'])){
    		$api_content[] = $result_array;
    		unset($result_array);
    	}

		return json_encode($api_content);
	}
	
	public function getStudentOtherExamResult($school_id, $student_id){

    	$exam = OnlineExamList::where([['school_id',$school_id],['is_deleted',0]])->orderby('exam_id','desc')->get();

		$onlineExamSubmission = array();

        foreach ($exam as $key => $value) {
          $submission = OnlineExamSubmission::where([['student_id', $student_id],['exam_id',$value->exam_id]])->get();

		  foreach ($submission as $row) {
			$onlineExamSubmission[] = $row;
		  }
        }

		foreach ($onlineExamSubmission as $row) {
			$find_exam = OnlineExamList::where([['exam_id',$row->exam_id]])->first();
			$row['exam'] = $find_exam;

			$high = OnlineExamSubmission::where([['exam_id',$row->exam_id]])->max('obtain_mark');
		  	$row['highest'] = $high;
		}

		foreach ($onlineExamSubmission as $ekey => $value) {
            $session = SchoolWiseAcademicSession::where('school_wise_academic_session_row_id',$value->exam->session_id)->first();
            $year = $session->academic_session_year;
            $month = $session->month;
            $sectionList = SchoolWiseSection::where([['school_id',$school_id],['master_group_row_id',$value->exam->group_id],['academic_session_year',$year],['month',$month]])->get();

			$subject = DB::table('school_wise_subjects')->where([['school_id', $school_id],['school_subject_row_id', $value->exam->subject_id]])->first();

			$value['subject'] = $subject;
			
        }

		return $onlineExamSubmission;
	}

	public function my_array_unique($array, $condition){
		$unique_array = array();
		$comparison_array = array();

		foreach($array as $items){
			if(!in_array($items->$condition, $comparison_array)) {
				$comparison_array[] = $items->$condition;
				$unique_array[] = $items;
			}
			
		}
		return $unique_array;
	}

	public function getStudentMasterExamResult($student_id, $version_id, $session_id, $classid, $shiftid, $sectionid, $group_row_id, $mastertermid){

    	$result_array = $api_content = $marks_array = array(); 
		$getExamMasterTermInfo = MasterTerm::where('exam_master_term_row_id', $mastertermid)->first();

		$getExamGrade = ExamGrade::all();

		$getSubjects = ClasswiseSubject::where([ ['version_row_id', $version_id], ['class_row_id', $classid] ])->get();
		//dd($getSubjects);
		
		$highestmarks = array();
		foreach($getSubjects as $subjects) {
			$getHighestMarksByClass = TermProcessedData::where([ ['class_row_id', '=', $classid], ['subject_row_id', '=', $subjects->subject_row_id] ])->max('term_total_marks');
			$getHighestMarksBySection = TermProcessedData::where([ ['class_row_id', '=', $classid], ['section_row_id', $sectionid], ['subject_row_id', '=', $subjects->subject_row_id] ])->max('term_total_marks');
			$highestmarks[$subjects->subject_row_id]['class'] = $getHighestMarksByClass;
			$highestmarks[$subjects->subject_row_id]['section'] = $getHighestMarksBySection;
		}

		/***** Get Student Optional Subjects if he reads in class 9 - 12 ****/
		if(($classid >= 9) && ($classid <= 12)) {
			$studentOptionalSubject = StudentsOptionalSubject::where('student_row_id', $student_id)->first();
		} else {
			$studentOptionalSubject = '';
		}

		$studentMarks = DB::table('term_processed_datas AS tpd')
		->leftjoin('subjects', 'subjects.subject_row_id', '=', 'tpd.subject_row_id')
		->leftjoin('classwise_subjects as cs', function($join)
		{    
			$join->on('cs.subject_row_id', '=', 'subjects.subject_row_id')
			->on('cs.class_row_id', '=', 'tpd.class_row_id')
			->on('cs.version_row_id', '=', 'tpd.version_row_id')
			->on('cs.academic_session_row_id', '=', 'tpd.academic_session_row_id');

		})
		->leftjoin('master_exam_infos AS mei', 'mei.master_exam_row_id', '=', 'tpd.master_exam_row_id')
		->leftjoin('master_exam_marks AS mem', function($join)
		{
			$join->on('mem.master_exam_row_id', '=', 'tpd.master_exam_row_id')
			->on('mem.student_row_id', '=', 'tpd.student_row_id');
		})
		->select('tpd.tpd_row_id as tpdid', 'tpd.*', 'subjects.*', 'mei.*', 'mem.*', 'cs.*')
		->where([ ['tpd.academic_session_row_id', $session_id], ['tpd.class_row_id', $classid], ['tpd.shift_row_id', $shiftid], ['tpd.section_row_id', $sectionid], ['tpd.exam_master_term_row_id', $mastertermid], ['tpd.student_row_id', $student_id] ])
		->orderBy('cs.sort_order', 'asc')
		->get();

		//dd($studentMarks);
		$studentMarks = $this->my_array_unique($studentMarks, 'subject_row_id');

		$school_id = get_school_id();
		$studentposition = DB::select( DB::raw("SELECT s1.`std_final_result_row_id`, s1.`student_row_id`, s1.`term_obtained_marks`, COUNT(DISTINCT s2.`term_obtained_marks`) AS rnk FROM bdedu_students_final_result s1 JOIN bdedu_students_final_result s2 ON (s1.`term_obtained_marks` <= s2.`term_obtained_marks`) WHERE s1.`academic_session_row_id` = ".$session_id." AND s1. `school_row_id` = ".$school_id."  AND s1.`student_row_id` = ".$student_id." AND s2.class_row_id = ".$classid." AND s2.`academic_session_row_id` = ".$session_id." GROUP BY s1.`std_final_result_row_id`, s1.`student_row_id`, s1.`term_obtained_marks`" ));

		$common_lib = new Common();
		$getStudentMasterExamResult = $common_lib->getStudentMasterExamResult($session_id, $classid, $shiftid, $sectionid, $mastertermid, $student_id);
		$exam_remarks = $common_lib->getAllExamRemarks();
		
		$result_array['order'] = 1;
		$result_array['title'] = $getExamMasterTermInfo->exam_category_title;
		$result_array['type'] = "TT";
		
		$count = 1;
		$total_full_marks = 0;
		$total_term_marks = 0;
		$total_grade_point = 0;
		$total_obtained_marks = 0;
		$nextpair = array();
		$prevpairsubjectrowid = 0;
		$prevpair = array();
		$subjectcount = 0;

		foreach($studentMarks as $examdata){
			if(($examdata->class_row_id >= 9) && ($examdata->class_row_id <= 12)) {
				if($examdata->has_pair == 1) {
					if (isset($nextpair[$prevpairsubjectrowid])) {
						$pairMe++;
						$prevpair[$examdata->subject_row_id] = $examdata->pair_subject_row_id;
						$pair_two_total = ceil($examdata->term_total_marks);
						$subjectcount += 1;
					} else {
						$pairMe = 1;
						$nextpair[$examdata->subject_row_id] = $examdata->pair_subject_row_id;
						$prevpairsubjectrowid = $examdata->subject_row_id;
						$pair_one_total = ceil($examdata->term_total_marks);
					}
				} else {
					$subjectcount += 1;
				}
	
				if($examdata->subject_row_id == $studentOptionalSubject->subject_row_id) {
					$subjectcount--;
				}
			} 	else {
				$subjectcount++;
			}
	
			$total_term_marks += ceil($examdata->term_total_marks);
			$total_obtained_marks += ceil($examdata->total_marks);
			if(isset($examdata->exam_total_marks)) {
				$grademarks = 0;
				$total_full_marks += $examdata->exam_total_marks;
	
			  	if($examdata->exam_total_marks != 100) {
					$grademarks = ceil($examdata->term_total_marks);
				} else {
					$grademarks = ceil($examdata->term_total_marks);
				}
			}
	
			if(!empty($examdata->elem_exam_marks) || ($examdata->elem_exam_marks != NULL)) {
				$allelemmarks = json_decode($examdata->elem_exam_marks, true);
			} else {
				$allelemmarks = '';
			}
	
			if($examdata->exam_total_marks != 100) {
				$obtained_percentage = ($examdata->total_marks * 100) / $examdata->exam_total_marks;
			} else {
				$obtained_percentage = $examdata->total_marks;
			}
	
			foreach($exam_remarks as $remarks) {
				if(($obtained_percentage >= $remarks->percentage_from) && ($obtained_percentage <= $remarks->percentage_upto)){
				  $std_subject_remarks = $remarks->remarks_title;
				}
			}

			if (array_key_exists($examdata->subject_row_id, $highestmarks)) {
				$stdhighestmarks = ceil($highestmarks[$examdata->subject_row_id]['class']);
			}

			foreach($getExamGrade as $grade){
				if(($grademarks >= $grade->marks_from) && ($grademarks <= $grade->marks_upto))  {
					$subgrade = $grade->grade_title;
				}
			}

			foreach($getExamGrade as $grade) {
				if(($grademarks >= $grade->marks_from) && ($grademarks <= $grade->marks_upto))  {
					// Check if the subject is optional

				  	if(($examdata->class_row_id >= 9) && ($examdata->class_row_id <= 12)) {
					  	if(!empty($studentOptionalSubject) && ($examdata->subject_row_id == $studentOptionalSubject->subject_row_id)) {
						  $total_gp = number_format($grade->grade_point, 2);
						  $opt_sub_gp = number_format(($grade->grade_point - 2), 2);
						  $subgp = $opt_sub_gp;
						  $total_gp = $opt_sub_gp;
					  	} else {
						  $total_gp = number_format($grade->grade_point, 2);
						  $subgp = $total_gp;
					  	}
				  	} else {
					  $total_gp = number_format($grade->grade_point, 2);
					  $subgp = $total_gp;
				  	}
				  
				  	if((empty($nextpair)) && (empty($prevpair))) {
					  $total_grade_point += $total_gp;
				  	}
			  	}	
			}

			$marks_array = array();
			$marks_array['subject'] = $examdata->subject_title;
			$marks_array['total'] = [$examdata->exam_total_marks, $examdata->exam_total_marks, 0];
			$marks_array['highest'] = $stdhighestmarks;
			$marks_array['obtained'] = [$examdata->total_marks, 0];
			$marks_array['grade'] = $subgrade;
			$marks_array['gp'] = $subgp;
			$marks_array['remarks'] = $std_subject_remarks;
			$result_array['marks'][] = $marks_array;
			unset($marks_array);
		}

		if(!empty($studentMarks)) {
			$final_gpa = number_format(($total_grade_point/$subjectcount), 2);
			foreach($getExamGrade as $grade) {
				if(($final_gpa >= $grade->point_from) && ($final_gpa <= $grade->point_upto)) {
					$final_grade =  $grade->grade_title;
					break;
				}
			}    
		}

		if(!empty($studentMarks)) {
			$final_total_gpa = number_format(($total_grade_point/$subjectcount), 2);
			if($final_total_gpa >= 5) {
				$final_gpa =  number_format(floor($final_total_gpa), 2);
			} else {
				$final_gpa =  $final_total_gpa;
			}
		}

		$result_array['total'] = $total_full_marks;
		$result_array['obtained'] = $total_term_marks;
		$result_array['grade'] = $final_grade;
		$result_array['gpa'] = $final_gpa;
		$result_array['position'] = $studentposition[0]->rnk;

		if(isset($result_array['marks']) && !empty($result_array['marks'])){
			$api_content[] = $result_array;
			unset($result_array);
		}
		return json_encode($api_content);
	}

	public function get_user_details_row_by_student_row_id($user_id){

		return $student_details_exists = DB::table('users')->where([['user_id',$user_id]])->first();
	}

	function getUserNameById($user_id){
    $user_name = array();
    if (isset($user_id) && !empty($user_id)) {
        $user = DB::table('users')->where('user_id',$user_id)->first();
        if(isset($user) && $user != null){
            $user_name['en'] = $user->name;
            $user_name['bn'] = $user->name_bangla;
        }
    }else{
        $user_name = '';
    }
    return $user_name;
}
}