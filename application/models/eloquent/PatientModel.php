<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class PatientModel extends BaseModel
{
	protected $table = 'patients';

	protected $appends = [
		'date_created', 
		'date_updated', 
		'name',
		'date_birth',
		'user_name',
	];

	protected $hidden = ['created_at', 'updated_at'];

	public function getNameAttribute()
	{
		return $this->first_name.' '.$this->last_name;
	}
	public function getDateBirthAttribute()
	{
		return date('M. d, Y', strtotime($this->birth_date));
	}
	public function rel_user()
	{
		return $this->hasOne(UserModel::class, 'id', 'user_id');
	}
	public function getUserNameAttribute()
	{
		return $this->rel_user ? $this->rel_user->name : null;
	}
}