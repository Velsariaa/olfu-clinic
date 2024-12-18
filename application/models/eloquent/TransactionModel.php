<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class TransactionModel extends BaseModel
{
	protected $table = 'transactions';

	protected $appends = [
		'date_created', 
		'date_updated', 
		'patient', 
		'transaction_type',
		'doctor',
	];

	protected $hidden = ['created_at', 'updated_at'];

	public function rel_patient()
	{
		return $this->hasOne(PatientModel::class, 'id', 'patient_id');
	}
	public function rel_transaction_type()
	{
		return $this->hasOne(TransactionTypeModel::class, 'id', 'transaction_type_id');
	}
	public function rel_doctor()
	{
		return $this->hasOne(DoctorModel::class, 'id', 'doctor_id');
	}
	public function getPatientAttribute()
	{
		return $this->rel_patient ? $this->rel_patient->name : null;
	}
	public function getTransactionTypeAttribute()
	{
		return $this->rel_transaction_type ? $this->rel_transaction_type->transaction_type : null;
	}
	public function getDoctorAttribute()
	{
		return $this->rel_doctor ? $this->rel_doctor->name : null;
	}
}