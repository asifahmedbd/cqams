<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Common;
use App\Models\SubjectTeacher;
use App\Models\SchoolWiseAcademicSession;
use App\Models\TermwiseSubjectInexam;
use App\Models\TermProcessedData;
use App\Models\ExamGrade;
use App\Models\SchoolWiseSubject;
use App\Models\StudentsFinalResult;
use App\Models\MasterTerm;
use App\Models\SchoolAndClassWiseSection;
use App\Models\SchoolwiseMasterTerm;
use App\Models\Student;
use App\Models\tqams\Tqams;
use App\Models\tqams\Tqams_field;
use App\Models\tqams\Tqams_criteria;
use App\Models\tqams\Tqams_sub_criteria;
use App\Models\School;
use App\Models\AcademicSession;
use App\Models\tqams\Cqams_schoolwise_evaluation;
use App\Models\tqams\Teacher_satisfaction;
use Carbon\Carbon;
use Config;
use DB;
use PDF;
use Session;
use Validator;
use Alert;

class AcademicReportsController extends Controller
{
    private $viewFolderPath = 'admin/academic_reports/';
    public function __construct() {
        //$this->middleware('school-auth');
    }

    public function classTeacherLists(){
    	$breadcrumb = ['Class Teacher Lists'];
    	$pageName   = "Class Teacher Lists";
		$SubjectTeacher = new SubjectTeacher();
        $swas = DB::table('school_wise_academic_session As swas')->where([ ['school_id', session('school_id')], ['is_active', 1] ])->get();
        $academic_session_year = array();
        foreach ($swas as $data) {
            $academic_session_year[] = $data->academic_session_year;
        }
		$allSubjectTeacher = $SubjectTeacher::with('master_classes', 'master_subject', 'class_shift', 'class_section', 'class_teacherV2', 'staff_designation')
											->where([ ['school_id',session('school_id')],['class_teacher', 1] ])
                                            ->whereIn('academic_session_year', $academic_session_year)
											->orderBy('master_class_row_id', 'ASC')->get();

        $nsarray = array();
        foreach($allSubjectTeacher as $stdata){
            $nsarray[$stdata->master_classes->sort_order][$stdata->master_class_row_id][] = $stdata;
        }
        ksort($nsarray);
        $allSubjectTeacher = $nsarray;

		//echo '<pre>'.print_r($allSubjectTeacher, true).'</pre>'; exit();
		//dd($allSubjectTeacher);											

		return view($this->viewFolderPath . 'class_teacher_list', compact('breadcrumb', 'allSubjectTeacher','pageName'));
    }

    public function exportToPDF() {
        $school_info = DB::table('schools')->where('school_id', session('school_id'))->first();
        $school_address = getSchoolAddress($school_info);
        $school_logo = ($school_info->school_logo != '') ? $school_info->school_logo : 'default_logo.jpg';
        $school_logo_url = ($school_info->school_logo != '') ? public_path().'/images/'.session('school_id').''.$school_info->school_logo : public_path().'/images/default_logo.jpg';
        $SubjectTeacher = new SubjectTeacher();
        $allSubjectTeacher = $SubjectTeacher::with('master_classes', 'master_subject', 'class_shift', 'class_section', 'class_teacherV2', 'staff_designation')
											->where([ ['school_id',session('school_id')],['class_teacher', 1], ['academic_session_year', date('Y')] ])
											->orderBy('master_class_row_id', 'ASC')->get();
        $nsarray = array();
        foreach($allSubjectTeacher as $stdata){
            $nsarray[$stdata->master_classes->sort_order][$stdata->master_class_row_id][] = $stdata;
        }
        ksort($nsarray);
        $allSubjectTeacher = $nsarray;
        // return $admin_list;
        $pdf = \PDF::loadView($this->viewFolderPath.'class_teacher_list_pdf', compact('allSubjectTeacher','school_info','school_address','school_logo','school_logo_url'));        
        return $pdf->stream('Class_Teachers_List.pdf');
    }

    public function subjectWiseResultAnalysis(){
        $common_lib = new Common();
        $breadcrumb = ['Subject Wise Result Analysis'];
        $pageName   = "Subject Wise Result Analysis" ;
        $academic_version_list = $common_lib->getEducationVersionList();

        $month = $common_lib->month_array;
        $academic_session_list = SchoolWiseAcademicSession::where([['school_id',session('school_id')],['is_deleted',0],['is_active',1]])->get();

        return view( $this->viewFolderPath . 'subject_wise_result_analysis', compact('version_list', 'breadcrumb','pageName', 'academic_version_list', 'month', 'academic_session_list'));
    }

    public function generateSubjectWiseReportAnalysis($session_id, $version_row_id, $class_row_id, $shift_id, $section_id, $department, $generate_pdf){
        $common_lib = new Common();
        $master_terms = $common_lib->getClassWiseMasterTermJson($session_id, $version_row_id, $class_row_id);
        //dd($master_terms);
        $getExamGrade = ExamGrade::where([ ['school_id', session('school_id')], ['is_others', 1] ])->get();
        $school_info = \App\Models\School::where('school_id', session('school_id'))->first();
        $section_title = \App\Models\SchoolAndClassWiseSection::where([
            ['school_id', session('school_id')], ['master_section_row_id', $section_id], 
            ['master_class_row_id', $class_row_id], ['version_row_id', $version_row_id]
        ])->value('section_title');
        $term_subjects = array();
        $all_subjects = array();

        foreach ($master_terms as $mdata) {
            $mterm_id = $mdata->exam_master_term_row_id;
            $tsirecord = TermwiseSubjectInexam::where([ ['school_id', get_school_id()], ['academic_session_row_id', $session_id], ['version_row_id', $version_row_id], ['master_term_row_id', $mterm_id], ['master_class_row_id', $class_row_id], ['group_row_id', $department] ])->first();

            //dd($tsirecord);

            if(!empty($tsirecord) || ($tsirecord != null)){
                $tsiSubjects = json_decode($tsirecord->subjects_id_inexam);
                //dd($tsiSubjects);

                foreach ($tsiSubjects as $key => $sid) {

                    $all_subjects[] = $sid;

                    $subjectTermData = TermProcessedData::where([
                        ['academic_session_row_id', $session_id], ['version_row_id', $version_row_id], 
                        ['school_row_id', session('school_id')], ['class_row_id', $class_row_id], 
                        ['shift_row_id', $shift_id], ['section_row_id', $section_id], 
                        ['exam_master_term_row_id', $mterm_id], ['subject_row_id', $sid]
                    ])->get();

                    //dd($subjectTermData);

                    foreach ($subjectTermData as $sdata) {
                        foreach($getExamGrade as $grade) {
                            //echo 'Std-GR:- '.$data->obtained_gpa.'  Point From '.$grade->point_from.'</br>';
                            if(($sdata->subject_cgpa >= $grade->point_from) && ($sdata->subject_cgpa <= $grade->point_upto)) {
                                //$obtained_grade =  $grade->grade_title;
                                $term_subjects[$mterm_id][$sid][$grade->grade_title][] = $sdata;
                                break;
                            }
                        }
                    }
                }

            }
        }

        $gchart_array = array();
        //dd($term_subjects);
        $subjects_data = array_unique($all_subjects);
        
        $gchart_array = array( 
             array('Grades', 'FE', 'HY', 'SE', 'FN'),
        );
        $subject_name = array();
        $subject_report = array();
        foreach($subjects_data as $key=>$subid){
            $subjectTitle = SchoolWiseSubject::where('school_subject_row_id',  $subid)->value('subject_title');
            $subject_name[$subid][] = $subjectTitle;
            // $subject_report[$subid] = array( 
            //      array('Grades', 'FE', 'HY', 'SE', 'FN'),
            // );
            $subject_report[$subid][0][] = 'Grades';
            foreach ($master_terms as $mdatatag) {
                $subject_report[$subid][0][] = $mdatatag->exam_short_tag;
            }
            //dd($subject_report);

            foreach($getExamGrade as $grade) {
                $individual_sub = array();
                $individual_sub[] = $grade->grade_title;
                foreach ($master_terms as $mdata) {
                    $mterm_id = $mdata->exam_master_term_row_id;
                    if(array_key_exists($grade->grade_title, $term_subjects[$mterm_id][$subid])){
                        $individual_sub[] = count($term_subjects[$mterm_id][$subid][$grade->grade_title]);
                    } else {
                        $individual_sub[] = 0;
                    }
                }
                $subject_report[$subid][] = $individual_sub;
            }
            
            //dd($subject_report);
        }

        $studentsID = Student::where([
            ['academic_session_year', $session_id], ['academic_version', $version_row_id], 
            ['school_id', session('school_id')], ['current_class', $class_row_id], 
            ['current_shift', $shift_id], ['current_section', $section_id], 
            ['active_status', 1]
        ])->pluck('student_row_id');
        
        //dd($subject_report);
        // Grade wise report section
        $grade_wise_report = array();
        $grade_wise_report[0][] = 'Grades';
        // $grade_wise_report = array( 
        //          array('Grades', 'FE', 'HY', 'SE', 'FN'),
        //     );
        foreach ($master_terms as $mdatatag) {
            $grade_wise_report[0][] = $mdatatag->exam_short_tag;
        }
 
        foreach($getExamGrade as $grade) {
            $ind_grade_data = array();
            $ind_grade_data[] = $grade->grade_title;
            foreach ($master_terms as $mdata) {
                $mterm_id = $mdata->exam_master_term_row_id;
                $studentsFinalResult = StudentsFinalResult::where([
                    ['academic_session_row_id', $session_id], ['version_row_id', $version_row_id], 
                    ['school_row_id', session('school_id')], ['class_row_id', $class_row_id], 
                    ['shift_row_id', $shift_id], ['section_row_id', $section_id], 
                    ['exam_master_term_row_id', $mterm_id], ['obtained_grade', $grade->grade_title]
                ])->whereIn('student_row_id', $studentsID)->get();
                $tcount = $studentsFinalResult->count();
                if($tcount > 0){
                    $ind_grade_data[] += $tcount;
                } else {
                    $ind_grade_data[] = 0;
                }
                // if(isset($term_subjects[$mterm_id])){
                //     foreach($term_subjects[$mterm_id] as $subjects){
                //         foreach($subjects as $gradeTitle=>$count){
                //             //echo $grade->grade_title.' '.$gradeTitle;
                //             if($grade->grade_title == $gradeTitle){
                //                 $ind_grade_data[$mterm_id] += count($count);
                //             } else {
                //                 $ind_grade_data[$mterm_id] += 0;
                //             }
                //         }
                //     }
                // } else {
                //     $ind_grade_data[$mterm_id] = 0;
                // }
            }
            $grade_wise_report[] = $ind_grade_data;
        }

        //dd($grade_wise_report);
        if($generate_pdf == 1){
            // $dompdf->load_html($html);
            // $dompdf->render();
            $dompdf = PDF::loadView( $this->viewFolderPath . 'subject_wise_result_analysis_report', compact('subject_report', 'subject_name', 'grade_wise_report', 'master_terms'));
            //return $pdf->download('master_exam_report_card.pdf');
            return $dompdf->stream('chart.pdf');
        } else {
            return view( $this->viewFolderPath . 'subject_wise_result_analysis_report', compact('subject_report', 'subject_name', 'grade_wise_report', 'school_info', 'section_title', 'class_row_id', 'master_terms'));    
        }
        
    }

    public function positionWiseResult(){
        $common_lib = new Common();
        $breadcrumb = ['Position Wise Report'];
        $pageName   = "Position Wise Report" ;
        $academic_version_list = $common_lib->getEducationVersionList();

        $month = $common_lib->month_array;
        $academic_session_list = SchoolWiseAcademicSession::where([['school_id',session('school_id')],['is_deleted',0],['is_active',1]])->get();
        return view( $this->viewFolderPath . 'position_wise_report', compact('version_list', 'breadcrumb','pageName', 'academic_version_list', 'month', 'academic_session_list'));
    }

    public function positionWiseDetails($session_id, $version_row_id, $class_row_id, $shift_id, $section_id, $department, $mastertermid, $generate_pdf){
        $school_info = DB::table('schools')->where('school_id', session('school_id'))->first();
        $school_address = getSchoolAddress($school_info);
        $school_logo = ($school_info->school_logo != '') ? $school_info->school_logo : 'default_logo.jpg';
        $school_logo_url = ($school_info->school_logo != '') ? public_path().'/images/'.session('school_id').''.$school_info->school_logo : public_path().'/images/default_logo.jpg';
        $master_term_id   = explode('|', $mastertermid);
        $term_title = MasterTerm::where('exam_master_term_row_id', $master_term_id[1])->value('exam_category_title');
        $section_title = SchoolAndClassWiseSection::where([
            ['school_id', session('school_id')], ['master_section_row_id', $section_id], 
            ['master_class_row_id', $class_row_id], ['version_row_id', $version_row_id]
        ])->value('section_title');

        if($section_id == 0){
            $stdresult = DB::table('students_final_result AS sfr')
            ->leftjoin('students AS std', function($join)
            {
                $join->on('std.student_row_id', '=', 'sfr.student_row_id')
                ->on('std.school_id', '=', 'sfr.school_row_id')
                ->on('std.current_class', '=', 'sfr.class_row_id')
                ->on('std.current_section', '=', 'sfr.section_row_id')
                ->on('std.academic_version', '=', 'sfr.version_row_id');
            })
            ->leftjoin('users AS user', 'user.user_id', '=', 'sfr.student_row_id')
            ->select('sfr.*', 'user.name', 'std.*')
            ->where([ ['sfr.academic_session_row_id', $session_id], ['sfr.version_row_id', $version_row_id], 
                    ['sfr.school_row_id', session('school_id')], ['sfr.class_row_id', $class_row_id], 
                    ['sfr.shift_row_id', $shift_id], 
                    ['sfr.exam_master_term_row_id', $master_term_id[1]]
            ])
            ->orderBy('sfr.student_position', 'asc')
            ->get();
            
            $std_filter_array = array();
            foreach ($stdresult as $sdata) {
                $std_filter_array[$sdata->student_row_id] = $sdata;
            }
            //dd($std_filter_array);

            $position_array = array();

            $sort_position = StudentsFinalResult::where([ ['academic_session_row_id', $session_id], ['school_row_id', session('school_id')], ['version_row_id', $version_row_id], ['class_row_id', $class_row_id], ['shift_row_id', $shift_id], ['exam_master_term_row_id', $master_term_id[1]], ['active_status', 1] ])->orderBy('obtained_gpa', 'desc')->orderBy('term_obtained_marks', 'DESC')->get();
            foreach ($sort_position as $pdata) {
                $position_array[$pdata->student_row_id] = $pdata->term_obtained_marks;
            }

            $unique = array_unique($position_array);
            $duplicates = array_diff_assoc($position_array, $unique);

            $std_cgpa_all = array();

            // $position = 0;
            // foreach($position_array as $std_id=>$cgpa){
            //     if(!in_array($cgpa, $std_cgpa_all)){
            //         $std_cgpa_all[] = $std_filter_array[$std_id];
            //         $position++;    
            //     }
            // }

            $position = 0;
            foreach($position_array as $std_id=>$cgpa){
                if(!in_array($cgpa, $cgpa_new)){
                    $cgpa_new[] = $cgpa;
                    $position++;
                    $std_filter_array[$std_id]->position = $position;
                    $std_cgpa_all[] = $std_filter_array[$std_id]; 
                } else {
                    $std_filter_array[$std_id]->position = $position;
                    $std_cgpa_all[] = $std_filter_array[$std_id];
                }
            }
            
            //dd($std_cgpa_all);
        } else {
            $stdresult = DB::table('students_final_result AS sfr')
            ->leftjoin('students AS std', function($join)
            {
                $join->on('std.student_row_id', '=', 'sfr.student_row_id')
                ->on('std.school_id', '=', 'sfr.school_row_id')
                ->on('std.current_class', '=', 'sfr.class_row_id')
                ->on('std.current_section', '=', 'sfr.section_row_id')
                ->on('std.academic_version', '=', 'sfr.version_row_id');
            })
            ->leftjoin('users AS user', 'user.user_id', '=', 'sfr.student_row_id')
            ->select('sfr.*', 'user.name', 'std.*')
            ->where([ ['sfr.academic_session_row_id', $session_id], ['sfr.version_row_id', $version_row_id], 
                    ['sfr.school_row_id', session('school_id')], ['sfr.class_row_id', $class_row_id], 
                    ['sfr.shift_row_id', $shift_id], ['sfr.section_row_id', $section_id], 
                    ['sfr.exam_master_term_row_id', $master_term_id[1]]
            ])
            ->orderBy('sfr.student_position', 'asc')
            ->get();
        }

        

        //dd($stdresult);
        if($generate_pdf == 1){

            $tname = str_replace(' ', '_', $term_title);
            $file_name = get_class_name_By_class_row_id($class_row_id).'_'.get_section_name_By_section_row_id($section_id, $class_row_id).'_'.$tname.'_'.date('Y');
            if($section_id == 0){
                $pdf = \PDF::loadView($this->viewFolderPath.'position_wise_details_allsection_pdf', compact('stdresult','school_info','school_address','school_logo','school_logo_url', 'term_title', 'section_title', 'class_row_id', 'std_cgpa_all', 'section_id'));
            } else {
                $pdf = \PDF::loadView($this->viewFolderPath.'position_wise_details_pdf', compact('stdresult','school_info','school_address','school_logo','school_logo_url', 'term_title', 'section_title', 'class_row_id', 'section_id'));   
            }
            
        return $pdf->download($file_name.'.pdf');
        } else {
            return view( $this->viewFolderPath . 'position_wise_details', compact('stdresult', 'std_cgpa_all', 'section_id', 'class_row_id'));
        }

    }

    public function teacherWiseResultAnalysis(){
        $breadcrumb = ['Teacher Wise Result Analysis'];
        $pageName   = "Assign Subject Teacher" ;
        $common_lib = new Common();
        $academic_version_list = $common_lib->getEducationVersionList();
        $allteachers = $common_lib->getTacherList();
        $month = $common_lib->month_array;
        $academic_session_list = SchoolWiseAcademicSession::where([['school_id',session('school_id')],['is_deleted',0],['is_active',1]])->get();
        return view($this->viewFolderPath .  'teacher_wise_result_analysis', compact('breadcrumb', 'pageName', 'allteachers', 'academic_version_list', 'subject_teacher_row_id','academic_session_list','month'));
    }

    public function generateTeacherWiseReportAnalysis($session_id, $version_row_id, $shift_id, $teacher_id, $generate_pdf){
        $common_lib = new Common();
        $getExamGrade = ExamGrade::where([ ['school_id', session('school_id')], ['is_others', 1] ])->get();
        $school_info = \App\Models\School::where('school_id', session('school_id'))->first();

        $swas = DB::table('school_wise_academic_session As swas')->where([ ['school_id', session('school_id')], ['is_active', 1] ])->get();
        $academic_session_year = array();
        foreach ($swas as $data) {
            $academic_session_year[] = $data->academic_session_year;
        }

        $allSubjectTeacher = SubjectTeacher::with('master_classes', 'master_subject', 'class_shift', 'class_section', 'class_teacher')
            ->where('school_id', session('school_id'))
            ->where('admin_row_id', $teacher_id)
            ->whereIn('academic_session_year', $academic_session_year)
            ->orderBy('created_at', 'DESC')->get();

       //dd($allSubjectTeacher);     
        
        $class_wise_subjects_by_teacher = array();
        foreach ($allSubjectTeacher as $teacherData) {
            $class_wise_subjects_by_teacher[$teacherData->master_class_row_id][$teacherData->master_section_row_id][] = $teacherData;
        }
        //dd($class_wise_subjects_by_teacher);

        //keep subjects which are in exams only
        foreach ($class_wise_subjects_by_teacher as $cid => $sections) {
            $master_terms = $common_lib->getClassWiseMasterTermJson($session_id, $version_row_id, $cid);
            foreach ($master_terms as $mdata) {
                $mterm_id = $mdata->exam_master_term_row_id;
                foreach ($sections as $secid => $value) {
                   foreach ($value as $key => $data) {
                        //dd($data);
                        $tsirecord = TermwiseSubjectInexam::where([ ['school_id', get_school_id()], ['academic_session_row_id', $session_id], ['version_row_id', $version_row_id], ['master_term_row_id', $mterm_id], ['master_class_row_id', $cid], ['group_row_id', $data->group_row_id] ])->first();

                        //dd($tsirecord);

                        if(!empty($tsirecord) || ($tsirecord != null)){
                            $tsiSubjects = json_decode($tsirecord->subjects_id_inexam);
                            //dd($tsiSubjects);
                            //echo $data->master_subject_row_id.'<br>';
                            //foreach ($tsiSubjects as $key => $sid) {
                            if(in_array($data->master_subject_row_id, $tsiSubjects)) {
                                $sid = $data->master_subject_row_id;
                                //echo $sid.'<br>';
                                //if(!in_array($sid, $all_subjects[$cid])){
                                    $all_subjects[$cid][$secid][] = $sid;

                                    $subjectTermData = TermProcessedData::where([
                                        ['academic_session_row_id', $session_id], ['version_row_id', $version_row_id], 
                                        ['school_row_id', session('school_id')], ['class_row_id', $cid], 
                                        ['shift_row_id', $data->master_shift_row_id], ['section_row_id', $secid], 
                                        ['exam_master_term_row_id', $mterm_id], ['subject_row_id', $sid]
                                    ])->get();

                                    //dd($subjectTermData);

                                    foreach ($subjectTermData as $sdata) {
                                        foreach($getExamGrade as $grade) {
                                            //echo 'Std-GR:- '.$data->obtained_gpa.'  Point From '.$grade->point_from.'</br>';
                                            if(($sdata->subject_cgpa >= $grade->point_from) && ($sdata->subject_cgpa <= $grade->point_upto)) {
                                                //$obtained_grade =  $grade->grade_title;
                                                $term_subjects[$cid][$secid][$mterm_id][$sid][$grade->grade_title][] = $sdata;
                                                break;
                                            }
                                        }
                                    }
                                //}
                            }

                        }
                    }
                }
                
            }
        } // end of main forloop

        $gchart_array = array();
        //dd($all_subjects);
        //$subjects_data = array_unique($all_subjects);
        
        $filtered_sub_data = array();
        foreach($all_subjects as $clid => $sections){
            foreach ($sections as $secid => $mtdata) {
                foreach ($mtdata as $key => $mtid) {
                    if(!in_array($mtid, $filtered_sub_data[$clid][$secid])){
                        $filtered_sub_data[$clid][$secid][] = $mtid;
                    }
                }
            }
        }
        //dd($filtered_sub_data);

        $gchart_array = array( 
             array('Grades', 'FE', 'HY', 'SE', 'FN'),
        );
        $subject_name = array();
        $section_name = array();
        $subject_report = array();
        foreach($filtered_sub_data as $clid=>$sections){
            foreach ($sections as $secid => $subdata) {
                $section_title = SchoolAndClassWiseSection::where([
                    ['school_id', session('school_id')], ['master_section_row_id', $secid], 
                    ['master_class_row_id', $clid], ['version_row_id', $version_row_id]
                ])->value('section_title');
                $section_name[$clid][$secid] = $section_title;
                foreach ($subdata as $key => $subid) {
                    $subjectTitle = SchoolWiseSubject::where('school_subject_row_id',  $subid)->value('subject_title');
                    $subject_name[$clid][$secid][$subid] = $subjectTitle;
                    $subject_report[$clid][$secid][$subid] = array( 
                         array('Grades', 'FE', 'HY', 'SE', 'FN'),
                    );

                    foreach($getExamGrade as $grade) {
                        $individual_sub = array();
                        $individual_sub[] = $grade->grade_title;
                        foreach ($master_terms as $mdata) {
                            $mterm_id = $mdata->exam_master_term_row_id;
                            if(array_key_exists($grade->grade_title, $term_subjects[$clid][$secid][$mterm_id][$subid])){
                                $individual_sub[] = count($term_subjects[$clid][$secid][$mterm_id][$subid][$grade->grade_title]);
                            } else {
                                $individual_sub[] = 0;
                            }
                        }
                        $subject_report[$clid][$secid][$subid][] = $individual_sub;
                    }
                }
            }
        }

        //dd($subject_report);

        if($generate_pdf == 1){
            // $dompdf->load_html($html);
            // $dompdf->render();
            $dompdf = PDF::loadView( $this->viewFolderPath . 'teacher_wise_result_analysis_report', compact('subject_report', 'school_info', 'subject_name', 'grade_wise_report', 'teacher_id', 'section_name'));
            //return $pdf->download('master_exam_report_card.pdf');
            return $dompdf->stream('chart.pdf');
        } else {
            return view( $this->viewFolderPath . 'teacher_wise_result_analysis_report', compact('subject_report', 'subject_name', 'grade_wise_report', 'school_info', 'section_title', 'class_row_id', 'teacher_id', 'section_name'));    
        }
    }

    public function studentWiseResultAnalysis(){
        $common_lib = new Common();
        $breadcrumb = ['Student Wise Result Analysis'];
        $pageName   = "Student Wise Result Analysis" ;
        $academic_version_list = $common_lib->getEducationVersionList();

        $month = $common_lib->month_array;
        $academic_session_list = SchoolWiseAcademicSession::where([['school_id',session('school_id')],['is_deleted',0],['is_active',1]])->get();

        return view( $this->viewFolderPath . 'student_wise_result_analysis', compact('version_list', 'breadcrumb','pageName', 'academic_version_list', 'month', 'academic_session_list'));
    }

    public function generateStudentWiseReportAnalysis($session_id, $version_row_id, $class_row_id, $shift_id, $section_id, $department, $studentid, $generate_pdf){
        $common_lib = new Common();
        $master_terms = $common_lib->getClassWiseMasterTermJson($session_id, $version_row_id, $class_row_id);
        //dd($master_terms);
        $getExamGrade = ExamGrade::where([ ['school_id', session('school_id')], ['is_others', 1] ])->get();
        $school_info = \App\Models\School::where('school_id', session('school_id'))->first();
        $section_title = \App\Models\SchoolAndClassWiseSection::where([
            ['school_id', session('school_id')], ['master_section_row_id', $section_id], 
            ['master_class_row_id', $class_row_id], ['version_row_id', $version_row_id]
        ])->value('section_title');
        $student_details = \App\Models\Student::with('master_classes', 'student_name')->where('student_row_id', $studentid)->first();
        //dd($student_details);
        $term_subjects = array();
        $all_subjects = array();
        $std_grade = array();
        $std_grade[0] = 'CGPA';
        foreach ($master_terms as $mdata) {
            $mterm_id = $mdata->exam_master_term_row_id;
            $tsirecord = TermwiseSubjectInexam::where([ ['school_id', get_school_id()], ['academic_session_row_id', $session_id], ['version_row_id', $version_row_id], ['master_term_row_id', $mterm_id], ['master_class_row_id', $class_row_id], ['group_row_id', $department] ])->first();

            //dd($tsirecord);

            if(!empty($tsirecord) || ($tsirecord != null)){
                $tsiSubjects = json_decode($tsirecord->subjects_id_inexam);
                //dd($tsiSubjects);

                $subjectinfo = SchoolWiseSubject::where('school_id',  get_school_id())->orderBy('sort_order', 'ASC')->get();

                
                $subject_sort = array();
                foreach ($subjectinfo as $key => $value) {
                    $subject_sort[] = $value->school_subject_row_id;
                }
                //dd($subject_sort);

                $studentsFinalResult = StudentsFinalResult::where([
                    ['academic_session_row_id', $session_id], ['version_row_id', $version_row_id], 
                    ['school_row_id', session('school_id')], ['class_row_id', $class_row_id], 
                    ['shift_row_id', $shift_id], ['section_row_id', $section_id], 
                    ['exam_master_term_row_id', $mterm_id], ['student_row_id', $studentid]
                ])->first();
                

                $std_grade[$mterm_id] = $studentsFinalResult->obtained_gpa;

                foreach ($tsiSubjects as $key => $sid) {

                    $all_subjects[] = $sid;

                    $subjectTermData = TermProcessedData::where([
                        ['academic_session_row_id', $session_id], ['version_row_id', $version_row_id], 
                        ['school_row_id', session('school_id')], ['class_row_id', $class_row_id], 
                        ['shift_row_id', $shift_id], ['section_row_id', $section_id], 
                        ['exam_master_term_row_id', $mterm_id], ['subject_row_id', $sid], ['student_row_id', $studentid]
                    ])->get();

                    //dd($subjectTermData);

                    foreach ($subjectTermData as $sdata) {
                        foreach($getExamGrade as $grade) {
                            //echo 'Std-GR:- '.$data->obtained_gpa.'  Point From '.$grade->point_from.'</br>';
                            if(($sdata->subject_cgpa >= $grade->point_from) && ($sdata->subject_cgpa <= $grade->point_upto)) {
                                //$obtained_grade =  $grade->grade_title;
                                $term_subjects[$mterm_id][$sid][$grade->grade_title][] = $sdata;
                                break;
                            }
                        }
                    }
                }

            } else {
                $std_grade[$mterm_id] = 0;
            }
        }

        //dd($std_grade);
        //dd($term_subjects);
        $subjects_data = array_unique($all_subjects);
        
        $subject_name = array();
        $subject_report = array();
        //foreach($subjects_data as $key=>$subid){
            
            // $subject_report[$subid] = array( 
            //      array('Grades', 'FE', 'HY', 'SE', 'FN'),
            // );
        $subject_report[0][] = 'Subjects';
        foreach ($master_terms as $mdatatag) {
            $subject_report[0][] = $mdatatag->exam_short_tag;
        }
        //dd($subjects_data);
        
        foreach($subjects_data as $key=>$subid){
            $individual_sub = array();    
            $subjectTitle = SchoolWiseSubject::where('school_subject_row_id',  $subid)->value('subject_short_tag');
            $subject_name[$subid][] = $subjectTitle;
            $individual_sub[] = $subjectTitle;
            
            foreach ($master_terms as $mdata) {
                $mterm_id = $mdata->exam_master_term_row_id;
                $allgrade_stat = 0;
                foreach($getExamGrade as $grade) {
                    if(array_key_exists($grade->grade_title, $term_subjects[$mterm_id][$subid])){
                        $allgrade_stat = 1;
                        $individual_sub[] = $term_subjects[$mterm_id][$subid][$grade->grade_title][0]['subject_cgpa'];
                    }

                    if(($grade->grade_title == 'F') && ($allgrade_stat == 0)){
                        $individual_sub[] = 0;
                    }
                }
            }
            $subject_report[$subid] = $individual_sub;
        }

        //dd($subject_report);
        $subject_report = array_values($subject_report);

        //dd($subject_report);
        if($generate_pdf == 1){
            // $dompdf->load_html($html);
            // $dompdf->render();
            $dompdf = PDF::loadView( $this->viewFolderPath . 'student_wise_result_analysis_report', compact('subject_report', 'subject_name', 'master_terms', 'student_details', 'std_grade'));
            //return $pdf->download('master_exam_report_card.pdf');
            return $dompdf->stream('chart.pdf');
        } else {
            return view( $this->viewFolderPath . 'student_wise_result_analysis_report', compact('subject_report', 'subject_name', 'school_info', 'section_title', 'class_row_id', 'master_terms', 'student_details', 'std_grade'));    
        }
        
    }


    public function showTqamsReport(){
        $common_lib = new Common();
        $breadcrumb = ['TQAMS Report'];
        $pageName   = 'TQAMS Report';

        $month = $common_lib->month_array;
        $all_session = AcademicSession::all();
        $admin_info = Session::get('admin_info');
        $schools = School::orderby('school_name','asc')->get();
        return view( $this->viewFolderPath . 'show_report', compact('all_session', 'month', 'admin_info', 'schools'));
    }

    public function getTqamsReportBySession($session_id, $school_id, $eval_id, $generate_pdf) {
        $common_lib = new Common();
        $school_info = \App\Models\School::where('school_id', $school_id)->first();

        $academic_session_year = $session_id;

        $school_tqams_data = Teacher_satisfaction::where([['school_id', $school_id],['session_id', $session_id], ['cse_id', $eval_id]])->first();
        $month = $common_lib->month_array;
        //dd($school_tqams_data);
        $all_data = Cqams_schoolwise_evaluation::with('academic_session', 'school_info')->where('cse_id', $eval_id)->first();
        //dd($all_data);
        $mtitle = $month[$all_data['month']];
        $school_type = ''.$all_data['school_type'].'';
        $session_title = $all_data->academic_session->academic_session_year;
        
        $tqams_evaluation = json_decode($school_tqams_data['answer'], true);
        //dd($tqams_evaluation);
        $tqams_field = Tqams_field::whereJsonContains('inst_type', $school_type)->get();;
        //echo '<pre>'.print_r($tqams_field, true).'</pre>'; exit();
        $tqams_categories = Tqams_criteria::all();
        $tqams_sub_criteria = Tqams_sub_criteria::all();
        $cat_name = array();
        foreach ($tqams_categories as $tdata) {
            $cat_name[$tdata->evalution_criteria_row_id] = $tdata->criteria_name_en;
        }

        
        $sub_cat_name = array();
        foreach ($tqams_sub_criteria as $stdata) {
            $sub_cat_name[$stdata->evalution_criteria_row_id][$stdata->evalution_sub_criteria_row_id] = $stdata->criteria_name_en;
        }

        //dd($sub_cat_name);

        $main_category = array();

        foreach ($tqams_field as $tdata) {
            //echo $tdata->evaluation_criteria_row_id.'-'.$tdata->sub_category_row_id.'-'.$tdata->evaluation_field_row_id;
            //if(array_key_exists($tdata->evaluation_field_row_id, $tqams_evaluation)){
             $main_category[$tdata->evaluation_criteria_row_id][$tdata->sub_category_row_id][$tdata->evaluation_field_row_id] = $tqams_evaluation[$tdata->evaluation_field_row_id];
            //}
        }

        //dd($main_category);

        $category_wise_avg = array();
        $sub_category_wise_avg = array();
        $all_category_avg = 0.00;
        foreach ($main_category as $catid => $cdata) {
            $cat_counter = 0;
            $sub_cat_question_count = 0;
            foreach ($cdata as $subcatid => $subdata) {
                $sub_cat_counter = 0;
                foreach ($subdata as $key => $value) {
                    $sub_cat_counter += $value;
                }
                //$sub_category_wise_avg[$catid][$subcatid] = $sub_cat_counter;
                $sub_category_wise_avg[$catid][$subcatid] = sprintf('%0.2f', $sub_cat_counter/count($subdata));
                $cat_counter += $sub_cat_counter;
                $sub_cat_question_count += count($subdata);
            }
            $category_wise_avg[$catid] = sprintf('%0.2f', $cat_counter/$sub_cat_question_count);
            $all_category_avg += (float)($category_wise_avg[$catid]);
        }

        //dd($tqams_categories);
        

        $tqams_report = array();

        $index = 0;
        foreach ($tqams_categories as $data) {
            $tqams_report[$index]['name'] = $data->criteria_name_en;
            $tqams_report[$index]['y'] = (float)($category_wise_avg[$data->evalution_criteria_row_id]);
            $tqams_report[$index]['drilldown'] = $data->criteria_name_en;
            $index++;
        }

        $tqams_report[$index]['name'] = 'Average';
        $tqams_report[$index]['y'] = (float)(sprintf('%0.2f', $all_category_avg / $index));
        //$tqams_report[$index]['drilldown'] = 'Average';
        
        //echo $index; dd($tqams_report);

        $tqams_report = json_encode($tqams_report);

        $drilldown_array = array();
        $postindex = 0;
        foreach ($sub_category_wise_avg as $cid => $cdata) {
            $drilldown_array[$postindex]['name'] = $cat_name[$cid];
            $drilldown_array[$postindex]['id'] = $cat_name[$cid];
            $preindex = 0;
            foreach ($cdata as $key => $value) {
                $drilldown_array[$postindex]['data'][$preindex][] = $sub_cat_name[$cid][$key];
                $drilldown_array[$postindex]['data'][$preindex][] = (float)($value);
                $preindex++;
            }
            $postindex++;
        }

        // $drilldown_array[$postindex]['name'] = 'Average';
        // $drilldown_array[$postindex]['id'] = 'Average';
        // $drilldown_array[$postindex]['data'] = [];

        //dd($drilldown_array);

        $drilldown_report = json_encode($drilldown_array);
        $school_name = json_encode($school_info->school_short_name.' CQAMS Report, '.$mtitle.', '.$session_title);

        return view($this->viewFolderPath . 'tqams_report_highchart', compact('tqams_report', 'drilldown_report', 'school_name'));

    }


    public function combinedResultAnalysis(){
        $common_lib = new Common();
        $breadcrumb = ['Comparative Result (Institution)'];
        $pageName   = 'Comparative Result (Institution)';

        $academic_version_list = $common_lib->getEducationVersionList();

        $month = $common_lib->month_array;
        $academic_session_list = SchoolWiseAcademicSession::where([['school_id',session('school_id')],['is_deleted',0],['is_active',1]])->get();

        $entry_years = SchoolwiseMasterTerm::where('school_id',session('school_id'))->orderBy('entry_year', 'desc')->groupBy('entry_year')->pluck('entry_year')->toArray();

        $schoolwise_master_terms = SchoolwiseMasterTerm::with('major_exams')->where([ ['entry_year', $entry_years[0]] , ['school_id',session('school_id')] ])->orderBy('term_sort_order', 'ASC')->get()->toArray();

        //dd($schoolwise_master_terms);

        return view( $this->viewFolderPath . 'combined_result_analysis', compact('version_list', 'breadcrumb','pageName', 'academic_version_list', 'month', 'academic_session_list', 'schoolwise_master_terms'));
    }

    public function combinedReportAjax($session_id, $version_row_id, $shift_id, $generate_pdf){
        $common_lib = new Common();
        $entry_years = SchoolwiseMasterTerm::where('school_id',session('school_id'))->orderBy('entry_year', 'desc')->groupBy('entry_year')->pluck('entry_year')->toArray();
        $master_terms = SchoolwiseMasterTerm::with('major_exams')->where([ ['entry_year', $entry_years[0]] , ['school_id',session('school_id')] ])->orderBy('term_sort_order', 'ASC')->get()->toArray();
        //dd($master_terms);
        $getExamGrade = ExamGrade::where([ ['school_id', session('school_id')], ['is_others', 1] ])->get();
        $school_info = \App\Models\School::where('school_id', session('school_id'))->first();
        
        $swas = DB::table('school_wise_academic_session As swas')->where([ ['school_id', session('school_id')], ['is_active', 1], ['school_wise_academic_session_row_id', $session_id] ])->get();
        $academic_session_year = array();
        foreach ($swas as $data) {
            $academic_session_year[] = $data->academic_session_year;
        }

        $section_info = DB::table('school_and_class_wise_sections AS sws')
        ->leftjoin('master_classes AS mc', 'mc.master_class_row_id', '=', 'sws.master_class_row_id')
        ->leftjoin('education_version AS ev', 'ev.version_row_id', '=', 'sws.version_row_id')
        ->leftjoin('master_education_groups AS meg', 'meg.master_group_row_id', '=', 'sws.master_group_row_id')
        ->select('sws.*', 'mc.class_name', 'ev.version_title', 'meg.group_title', 'mc.sort_order')
        ->where([ ['sws.version_row_id', $version_row_id], ['sws.school_id', session('school_id')], ['sws.is_deleted', 0] ])
        ->whereIn('sws.academic_session_year', $academic_session_year)
        ->orderBy('mc.sort_order', 'asc')->get()->toArray();

        //dd($section_info);
        
        $termwise_array = array();
        foreach ($master_terms as $mdata) {

            $classwise_count = array();
            $classwise_array = array();
            $total_std = 0;
            $grade_count = array();

            $mterm_id = $mdata['major_exams']['exam_master_term_row_id'];

            $swttmarks = DB::table('students_final_result as sfr')->select('*')
            ->leftjoin('master_classes as mc', 'sfr.class_row_id', '=', 'mc.master_class_row_id')
            ->leftjoin('school_and_class_wise_sections AS scws', function($join)
            {
                $join->on('scws.master_section_row_id', '=', 'sfr.section_row_id')
                ->on('scws.master_class_row_id', '=', 'sfr.class_row_id')
                ->on('scws.version_row_id', '=', 'sfr.version_row_id')
                ->on('scws.school_id', '=', 'sfr.school_row_id');
            })
            ->where([ ['sfr.exam_master_term_row_id', $mterm_id], ['sfr.version_row_id', $version_row_id], 
                ['sfr.academic_session_row_id', $session_id], ['sfr.school_row_id', session('school_id')], ['scws.is_deleted', 0], ['sfr.active_status', 1]  ])
            ->whereIn('scws.academic_session_year', $academic_session_year)
            ->orderBy('sfr.class_row_id', 'asc')->get();

            
            foreach ($swttmarks as $data) {
                $classwise_count[$data->class_row_id][$data->section_row_id][] = $data;
            }

            
            foreach ($swttmarks as $data) {
                foreach($getExamGrade as $grade) {
                    //echo 'Std-GR:- '.$data->obtained_gpa.'  Point From '.$grade->point_from.'</br>';
                    if(($data->obtained_gpa >= $grade->point_from) && ($data->obtained_gpa <= $grade->point_upto)) {
                        $classwise_array[$data->class_row_id][$data->section_row_id][$grade->grade_title][] = $data;
                        break;
                    }
                }
            }

            

            foreach($section_info as $data){
                $section_wise_grade = 0;
                $section_wise_student = count($classwise_count[$data->master_class_row_id][ $data->master_section_row_id]);
                $total_std += $section_wise_student;
                if($section_wise_student > 0) {
                    foreach($getExamGrade as $grade){
                        $total_grade_count = count($classwise_array[$data->master_class_row_id][ $data->master_section_row_id][$grade->grade_title]);
                        //echo $total_grade_count.'<br>';
                        $section_wise_grade_total = ($total_grade_count > 0) ? $total_grade_count : 0;
                        $grade_count[$grade->grade_title][] =  $section_wise_grade_total;         
                    }
                }
            }

            foreach($getExamGrade as $grade){
                $tdata = (array_sum($grade_count[$grade->grade_title])*100)/$total_std;
                //echo $tdata.'<br>'; 
                $termwise_array[$mterm_id][$grade->grade_title] = !is_nan($tdata) ? (float)number_format($tdata, 2) : 0;
            }
        } // end of loop
        //dd($termwise_array);

        $grade_wise_report = array();
        $grade_wise_report[0][] = 'Grades';
        foreach ($master_terms as $mdatatag) {
            $grade_wise_report[0][] = $mdatatag['major_exams']['exam_short_tag'];
        }


        foreach($getExamGrade as $grade) {
            $ind_grade_data = array();
            $ind_grade_data[] = $grade->grade_title;
            foreach ($master_terms as $mdata) {
                $mterm_id = $mdata['major_exams']['exam_master_term_row_id'];

                if(array_key_exists($grade->grade_title, $termwise_array[$mterm_id])){
                    $ind_grade_data[] = $termwise_array[$mterm_id][$grade->grade_title];
                } else {
                    $ind_grade_data[] = 0;
                }
            }
            $grade_wise_report[] = $ind_grade_data;
        }
        $grade_wise_report = array_values($grade_wise_report);
        //dd($grade_wise_report);

        if($generate_pdf == 1){
            $dompdf = PDF::loadView( $this->viewFolderPath . 'combined_analysis_report', compact('grade_wise_report', 'school_info', 'master_terms'));
        } else {
            return view( $this->viewFolderPath . 'combined_analysis_report', compact('grade_wise_report', 'school_info', 'master_terms'));    
        }

    } // end of function

    public function getEvaluationData($session_id, $school_id){
        $all_data = Cqams_schoolwise_evaluation::with('academic_session', 'school_info')->where([ ['session_row_id', $session_id], ['school_id', $school_id] ])->get();
        //dd($all_data);
        $common_lib = new Common();
        $month = $common_lib->month_array;
        $html = '';
        foreach ($all_data as $data) {
            $html.='<option value="'.$data->cse_id.'">'.$data->school_info->school_short_name.'('.$month[$data->month].')'.'</option>';
        }

        return $html;
    }

}
