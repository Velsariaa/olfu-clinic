<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class NurseModel extends BaseModel
{
	protected $table = 'nurses';

	protected $appends = [
		'date_created', 
		'date_updated', 
		'name', 
		'username',
		'icon_name'
	];

	protected $hidden = ['created_at', 'updated_at'];

	public function getNameAttribute()
	{
		return $this->first_name.' '.$this->last_name;
	}
	public function rel_admin() 
	{
		return $this->belongsTo(AdminModel::class, 'admin_id');
	}
	public function getUserTypeAttribute()
	{
		return USER_TYPE_NURSE;
	}
	public function getUsernameAttribute()
	{
		return $this->rel_admin ? $this->rel_admin->username : null;
	}
	public function getIconNameAttribute()
	{
		return strtoupper($this->first_name[0].$this->last_name[0]);
	}
}