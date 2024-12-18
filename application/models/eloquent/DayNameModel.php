<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class DayNameModel extends BaseModel
{
	protected $table = 'day_names';

	protected $appends = ['date_created', 'date_updated'];

	protected $hidden = ['created_at', 'updated_at'];

	public function rel_doctors()
    {
        return $this->belongsToMany(DoctorModel::class, 'doctor_day_names', 'day_name_id', 'doctor_id');
    }
}