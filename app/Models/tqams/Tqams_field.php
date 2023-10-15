<?php

namespace App\Models\tqams;

use Illuminate\Database\Eloquent\Model;

class Tqams_field extends Model
{
    protected $primaryKey = 'evaluation_field_row_id';

    protected $casts = [
        'inst_type' => 'array',
    ];

    public function scale(){
		return $this->hasOne('App\Models\Scale_set', 'id', 'scale_set');
	}

	public function sub_category(){
          return $this->hasOne('App\Models\tqams\Tqams_sub_criteria', 'evalution_sub_criteria_row_id', 'sub_category_row_id');
     }

	public function main_category(){
          return $this->hasOne('App\Models\tqams\Tqams_criteria', 'evalution_criteria_row_id', 'evaluation_criteria_row_id');
     }
}
