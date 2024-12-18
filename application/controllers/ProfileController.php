<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class ProfileController extends CI_Controller 
{
	private $parent_route = 'profile';

	function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$profile_admin = AdminModel::find(loginId());

		$tables = [
			USER_TYPE_USER => UserModel::class,
			USER_TYPE_DOCTOR => DoctorModel::class,
			USER_TYPE_NURSE => NurseModel::class,
		];

		$profile_user = collect([]);

		if (!in_array($profile_admin->user_type, [USER_TYPE_ADMIN, USER_TYPE_SUPER_ADMIN]))
		{
			$profile_user = $tables[$profile_admin->user_type]::where('admin_id', $profile_admin->id)
			->first();
		}

		$this->sm->assign('profile_admin', $profile_admin);

		$this->sm->assign('profile_user', $profile_user);

		foreach ($tables as $table_key => $table_value) 
		{
			if ($table_key != $profile_admin->user_type)
			{
				continue;
			}
			
			$method = 'init_'.strtolower($table_key);

			if (method_exists($this, $method)) {
				$this->{$method}($profile_user);
			}
		}

		$this->sm->assign('parent_route', $this->parent_route);

		$this->sm->display('profile/profile_'.strtolower($profile_admin->user_type).'.tpl');
	}
	public function password()
	{
		$user = AdminModel::find(loginId());
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('password', 'old password', 'required|callback_check_password');
		$this->form_validation->set_rules('new_password', 'new password', 'required');
		$this->form_validation->set_rules('confirm_new_password', 'confirm new password', 'required|matches[new_password]');

		if ($this->form_validation->run() == FALSE)
		{
			json_response(false, ['message' => implode('</br>', $this->form_validation->error_array())]);
		}

		$user->password = post('new_password', 'trim.encryptPassword');
		$user->save();

		json_response(true);
	}
	public function check_password($password)
	{
		$user = AdminModel::find(loginId());

		$match = verifyPassword($password, $user->password); 

		if (!$match)
		{
			$this->form_validation->set_message(__FUNCTION__, 'password did not match');
			return false;
		}

		return true;
	}
	private function init_doctor($user)
	{
		if (!DayNameModel::count()) json_response(false, ['message' => 'No schedule found']);

		$day_names = DayNameModel::select2('day_name', null, false, function ($table)
		{
			$table->select([
				'id as value',
				'day_name as text',
				'sorting',
			])
			->orderBy('sorting');
		});

		$this->sm->assign('day_names', $day_names);

		#

		if (!SpecialtyModel::count()) json_response(false, ['message' => 'No specialty found']);

		$specialties = SpecialtyModel::select2('specialty');

		$this->sm->assign('specialties', $specialties);

		#

		$doctor_day_names = DoctorDayNameModel::where('doctor_id', $user->id)
		->get();

		$doctor_day_names_ids = $doctor_day_names->pluck('day_name_id')->toArray();

		#

		$doctor_specialties = DoctorSpecialtyModel::where('doctor_id', $user->id)
		->get();

		$doctor_specialties_ids = $doctor_specialties->pluck('specialty_id')->toArray();

		$this->sm->assign('doctor_day_names_ids', $doctor_day_names_ids);

		$this->sm->assign('doctor_specialties_ids', $doctor_specialties_ids);
	}
	public function save_doctor()
	{
		$doctor = DoctorModel::find(post('doctor_id'));

		if (!$doctor)
		{
			json_response(false, ['message' => 'Invalid request']);
		}

		$doctor_day_names_ids = array_key_exists('day_names_ids', $_POST) ? post('day_names_ids[]') : [];
		
		$doctor_specialties_ids = array_key_exists('specialties_ids', $_POST) ? post('specialties_ids[]') : [];

		if ($doctor_day_names_ids)
		{
			DoctorDayNameModel::where('doctor_id', $doctor->id)
			->whereNotIn('day_name_id', $doctor_day_names_ids)
			->delete();

			foreach($doctor_day_names_ids as $day_name_id)
			{
				$doctor_day_name = DoctorDayNameModel::where('doctor_id', $doctor->id)
				->where('day_name_id', $day_name_id)
				->first();

				if (!$doctor_day_name)
				{
					$doctor_day_name = new DoctorDayNameModel;
					$doctor_day_name->doctor_id = $doctor->id;
					$doctor_day_name->day_name_id = $day_name_id;
					$doctor_day_name->save();
				}
			}
		}
		else
		{
			DoctorDayNameModel::where('doctor_id', $doctor->id)
			->delete();
		}

		if ($doctor_specialties_ids)
		{
			DoctorSpecialtyModel::where('doctor_id', $doctor->id)
			->whereNotIn('specialty_id', $doctor_specialties_ids)
			->delete();

			foreach($doctor_specialties_ids as $specialty_id)
			{
				$doctor_specialty = DoctorSpecialtyModel::where('doctor_id', $doctor->id)
				->where('specialty_id', $specialty_id)
				->first();

				if (!$doctor_specialty)
				{
					$doctor_specialty = new DoctorSpecialtyModel;
					$doctor_specialty->doctor_id = $doctor->id;
					$doctor_specialty->specialty_id = $specialty_id;
					$doctor_specialty->save();
				}
			}
		}
		else
		{
			DoctorSpecialtyModel::where('doctor_id', $doctor->id)
			->delete();
		}

		json_response(true);
	}
	public function update_user_profile()
	{
		$user = userLoggedIn();
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('first_name', 'first name', 'required');
		$this->form_validation->set_rules('last_name', 'last name', 'required');
		$this->form_validation->set_rules('contact', 'contact', 'required');
		$this->form_validation->set_rules('email', 'email', 'required');
		$this->form_validation->set_rules('student_no', 'student no', 'required');
		$this->form_validation->set_rules('address', 'address', 'required');
		$this->form_validation->set_rules('year_level', 'year level', 'required');
		$this->form_validation->set_rules('birth_date', 'birth date', 'required');
		$this->form_validation->set_rules('gender', 'gender', 'required');
		$this->form_validation->set_rules('civil_status', 'civil status', 'required');
		$this->form_validation->set_rules('religion', 'religion', 'required');
		$this->form_validation->set_rules('nationality', 'nationality', 'required');
		$this->form_validation->set_rules('mother_name', 'mother_name', 'required');
		$this->form_validation->set_rules('mother_occupation', 'mother_occupation', 'required');
		$this->form_validation->set_rules('mother_contact', 'mother contact', 'required');
		$this->form_validation->set_rules('father_name', 'father name', 'required');
		$this->form_validation->set_rules('father_occupation', 'father occupation', 'required');
		$this->form_validation->set_rules('father_contact', 'father contact', 'required');
		$this->form_validation->set_rules('emergency_contact_name', 'emergency contact name', 'required');
		$this->form_validation->set_rules('emergency_contact', 'emergency contact', 'required');
		$this->form_validation->set_rules('hospital_choice', 'hospital choice', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			json_response(false, ['message' => implode('</br>', $this->form_validation->error_array())]);
		}

		$user->first_name = post('first_name');
		$user->middle_name = post('middle_name');
		$user->last_name = post('last_name');
		$user->contact = post('contact');
		$user->email = post('email');
		$user->student_no = post('student_no');
		$user->address = post('address');
		$user->year_level = post('year_level');
		$user->birth_date = post('birth_date');
		$user->gender = post('gender');
		$user->civil_status = post('civil_status');
		$user->religion = post('religion');
		$user->nationality = post('nationality');
		$user->mother_name = post('mother_name');
		$user->mother_occupation = post('mother_occupation');
		$user->mother_contact = post('mother_contact');
		$user->father_name = post('father_name');
		$user->father_occupation = post('father_occupation');
		$user->father_contact = post('father_contact');
		$user->emergency_contact_name = post('emergency_contact_name');
		$user->emergency_contact = post('emergency_contact');
		$user->hospital_choice = post('hospital_choice');
		$user->save();

		json_response(true);
	}
}