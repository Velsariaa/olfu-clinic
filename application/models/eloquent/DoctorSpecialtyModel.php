<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class DoctorSpecialtyModel extends BaseModel
{
	protected $table = 'doctor_specialties';

	protected $appends = ['date_created', 'date_updated'];

	protected $hidden = ['created_at', 'updated_at'];
}