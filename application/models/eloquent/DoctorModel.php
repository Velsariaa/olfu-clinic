<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class DoctorModel extends BaseModel
{
	protected $table = 'doctors';

	protected $appends = [
		'date_created', 
		'date_updated', 
		'name', 
		'day_names_values', 
		'day_names',
		'specialties_values', 
		'specialties',
		'username',
		'icon_name'
	];

	protected $hidden = ['created_at', 'updated_at'];

	public function rel_day_names() 
	{
		return $this->belongsToMany(DayNameModel::class, 'doctor_day_names', 'doctor_id', 'day_name_id');
	}
	public function rel_specialties() 
	{
		return $this->belongsToMany(SpecialtyModel::class, 'doctor_specialties', 'doctor_id', 'specialty_id');
	}
	public function getNameAttribute()
	{
		return $this->first_name.' '.$this->last_name;
	}
	public function getDayNamesValuesAttribute()
	{
		return $this->rel_day_names->pluck('day_name')->toArray();
	}
	public function getDayNamesAttribute()
	{
		return implode(', ', $this->getDayNamesValuesAttribute());
	}
	public function getSpecialtiesValuesAttribute()
	{
		return $this->rel_specialties->pluck('specialty')->toArray();
	}
	public function getSpecialtiesAttribute()
	{
		return implode(', ', $this->getSpecialtiesValuesAttribute());
	}
	public function rel_admin() 
	{
		return $this->belongsTo(AdminModel::class, 'admin_id');
	}
	public function getUserTypeAttribute()
	{
		return USER_TYPE_DOCTOR;
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