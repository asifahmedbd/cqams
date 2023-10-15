<?php

namespace App\Models\tqams;

use Illuminate\Database\Eloquent\Model;

class Cqams_schoolwise_evaluation extends Model
{
    protected $primaryKey = 'cse_id';
    protected $table = 'cqams_schoolwise_evaluation';

    public function academic_session(){
        return $this->hasOne('App\Models\AcademicSession', 'academic_session_row_id', 'session_row_id');
    }

    public function school_info(){
        return $this->hasOne('App\Models\School', 'school_id', 'school_id');
    }
}
