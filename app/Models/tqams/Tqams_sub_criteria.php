<?php

namespace App\Models\tqams;

use Illuminate\Database\Eloquent\Model;

class Tqams_sub_criteria extends Model
{
     protected $primaryKey = 'evalution_sub_criteria_row_id';

     public function main_category(){
          return $this->hasOne('App\Models\tqams\Tqams_criteria', 'evalution_criteria_row_id', 'evalution_criteria_row_id');
     }
}
