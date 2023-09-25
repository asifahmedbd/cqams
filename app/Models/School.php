<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $primaryKey = 'school_id';
    protected $keyType = 'string';
    public $timestamps = false;

    public function division(){
		return $this->hasOne('App\Models\Division', 'id', 'division_id');
	}

	public function district(){
		return $this->hasOne('App\Models\Distict', 'id', 'district_id');
	}

	public function upazila(){
		return $this->hasOne('App\Models\Upazila', 'id', 'upazila_id');
	}

	public function ins_type(){
		return $this->hasOne('App\Models\InstitutionType', 'id', 'type');
	}
	public function package_name(){
		return $this->hasOne('App\Models\Package', 'p_id', 'package');
	}
}