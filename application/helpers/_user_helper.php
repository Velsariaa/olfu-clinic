<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function adminLoggedIn()
{
	return AdminModel::find(loginId()) ?? collect(['id' => -1]);
}
function userLoggedIn()
{
	return accountLoggedIn(UserModel::class);
}
function nurseLoggedIn()
{
	return accountLoggedIn(NurseModel::class);
}
function doctorLoggedIn()
{
	return accountLoggedIn(DoctorModel::class);
}
function accountLoggedIn($model, $with=[])
{
	return $model::with($with)->where('admin_id', adminLoggedIn()->id)->first();
}