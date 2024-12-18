<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class AdminModel extends BaseModel
{
	protected $table = 'admins';

	protected $appends = ['date_created', 'date_updated', 'icon_name'];

	protected $hidden = ['created_at', 'updated_at'];

	public function getIconNameAttribute()
	{
		return strtoupper($this->first_name[0].$this->last_name[0]);
	}
}