<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class DoctorDayNameModel extends BaseModel
{
	protected $table = 'doctor_day_names';

	protected $appends = ['date_created', 'date_updated'];

	protected $hidden = ['created_at', 'updated_at'];
}