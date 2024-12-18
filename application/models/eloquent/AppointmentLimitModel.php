<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class AppointmentLimitModel extends BaseModel
{
	protected $table = 'appointment_limits';

	protected $appends = ['date_created', 'date_updated', 'date_limit'];

	protected $hidden = ['created_at', 'updated_at'];

	public function getDateLimitAttribute()
	{
		return $this->_date ? date('M. d, Y', strtotime($this->_date)) : null;
	}
}