<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class AppointmentModel extends BaseModel
{
	protected $table = 'appointments';

	protected $appends = [
		'date_created', 
		'date_updated',
		'user_name',
		'date_status',
		'specialty',
		'doctor',
		'date_appointment',
		'time_appointment',
	];

	protected $hidden = ['created_at', 'updated_at'];

	public function rel_user()
	{
		return $this->hasOne(UserModel::class, 'id', 'user_id');
	}
	public function getUserNameAttribute()
	{
		return $this->rel_user ? $this->rel_user->name : null;
	}
	public function getDateStatusAttribute()
	{
		return date('M. d, Y', strtotime($this->status_updated_at));
	}
	public function rel_specialty()
	{
		return $this->hasOne(SpecialtyModel::class, 'id', 'specialty_id');
	}
	public function rel_doctor()
	{
		return $this->hasOne(DoctorModel::class, 'id', 'doctor_id');
	}
	public function getSpecialtyAttribute()
	{
		return $this->rel_specialty ? $this->rel_specialty->specialty : null;
	}
	public function getDoctorAttribute()
	{
		return $this->rel_doctor ? $this->rel_doctor->name : null;
	}
	public function getDateAppointmentAttribute()
	{
		return $this->appointment_date ? date('M. d, Y', strtotime($this->appointment_date)) : null;
	}
	public function getTimeAppointmentAttribute()
	{
		return $this->appointment_time ? date('h:i A', strtotime($this->appointment_time)) : null;
	}
}