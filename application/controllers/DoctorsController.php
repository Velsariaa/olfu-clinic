<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class DoctorsController extends CI_Controller 
{
	private $parent_route = 'doctors';

	private $templates_dir = 'doctors';

	private $doctor_day_names = [];

	private $doctor_day_names_ids = [];

	private $doctor_specialties = [];

	private $doctor_specialties_ids = [];

	function __construct()
	{
		parent::__construct();

		$this->table_model = DoctorModel::class;

		$this->table_row = null;
	}
	public function index()
	{
		$table = $this->table_model::with(['rel_specialties', 'rel_day_names', 'rel_admin']);

		modelToSql($table, get('dq'));
		
		$table = $table->get(); 

		if (get('dd')) dmp($table);

		$table->each(function ($item, $key)
		{
			ci()->add_table_row_action($item);
		});

		$this->sm->assign('table_data', $table->toJson());

		$this->sm->assign('parent_route', $this->parent_route);

		$this->sm->display($this->templates_dir.'/table.tpl');
	}
	public function new()
	{
		$this->set_masterdata();

		$this->sm->assign('parent_route', $this->parent_route);

		$html = $this->sm->fetch($this->templates_dir.'/entry.tpl');

		json_response(true, ['html' => $html]);
	}
	protected function handle_transaction()
	{
		DB::beginTransaction();

		try
		{
			$this->pre_save();

			$this->admin->save();

			$this->table_row->admin_id = $this->admin->id;
	
			$this->table_row->save();

			$this->post_save();

			DB::commit();
		}
		catch(\Exception $e) 
		{
			DB::rollback();

			json_response(false, ['message' => $e->getMessage()]);
		}
	}
	private function pre_create()
	{
		$this->admin->user_type = $this->table_row->user_type;
	}
	private function pre_save()
	{
		$this->table_row->contact = '0'.substr($this->table_row->contact, -10);

		$this->admin->first_name = $this->table_row->first_name;
		$this->admin->last_name = $this->table_row->last_name;
		$this->admin->name = $this->table_row->first_name.' '.$this->table_row->last_name;

		if (array_key_exists('password', $_POST) && strlen(post('password')))
		{
			$this->admin->password = encryptPassword($this->admin->password);
		}
	}
	private function post_save()
	{
		// if ($this->doctor_day_names_ids)
		// {
		// 	DoctorDayNameModel::where('doctor_id', $this->table_row->id)
		// 	->whereNotIn('day_name_id', $this->doctor_day_names_ids)
		// 	->delete();

		// 	foreach($this->doctor_day_names_ids as $day_name_id)
		// 	{
		// 		$doctor_day_name = DoctorDayNameModel::where('doctor_id', $this->table_row->id)
		// 		->where('day_name_id', $day_name_id)
		// 		->first();

		// 		if (!$doctor_day_name)
		// 		{
		// 			$doctor_day_name = new DoctorDayNameModel;
		// 			$doctor_day_name->doctor_id = $this->table_row->id;
		// 			$doctor_day_name->day_name_id = $day_name_id;
		// 			$doctor_day_name->save();
		// 		}
		// 	}
		// }
		// else
		// {
		// 	DoctorDayNameModel::where('doctor_id', $this->table_row->id)
		// 	->delete();
		// }

		// if ($this->doctor_specialties_ids)
		// {
		// 	DoctorSpecialtyModel::where('doctor_id', $this->table_row->id)
		// 	->whereNotIn('specialty_id', $this->doctor_specialties_ids)
		// 	->delete();

		// 	foreach($this->doctor_specialties_ids as $specialty_id)
		// 	{
		// 		$doctor_specialty = DoctorSpecialtyModel::where('doctor_id', $this->table_row->id)
		// 		->where('specialty_id', $specialty_id)
		// 		->first();

		// 		if (!$doctor_specialty)
		// 		{
		// 			$doctor_specialty = new DoctorSpecialtyModel;
		// 			$doctor_specialty->doctor_id = $this->table_row->id;
		// 			$doctor_specialty->specialty_id = $specialty_id;
		// 			$doctor_specialty->save();
		// 		}
		// 	}
		// }
		// else
		// {
		// 	DoctorSpecialtyModel::where('doctor_id', $this->table_row->id)
		// 	->delete();
		// }
	}
	public function create()
	{	
		$this->set_table_row();

		$this->set_table_row_fields();

		$this->validate_table_row_fields();

		$this->pre_create();

		$this->handle_transaction();

		$this->table_row = $this->table_model::with(['rel_specialties', 'rel_day_names', 'rel_admin'])
		->find($this->table_row->id);

		$this->add_table_row_action();

		json_response(true, ['data' => $this->table_row]);
	}
	public function edit($id)
	{
		$this->set_table_row($id);

		$this->sm->assign('row', $this->table_row);

		$this->sm->assign('admin', $this->admin);

		$this->sm->assign('doctor_day_names_ids', $this->doctor_day_names_ids);

		$this->sm->assign('doctor_specialties_ids', $this->doctor_specialties_ids);

		$this->set_masterdata();

		$this->sm->assign('parent_route', $this->parent_route);

		$html = $this->sm->fetch($this->templates_dir.'/entry.tpl');

		json_response(true, ['html' => $html]);
	}
	public function update()
	{
		$this->set_table_row(post('id'));

		$this->set_table_row_fields();

		$this->validate_table_row_fields();

		$this->handle_transaction();

		$this->table_row = $this->table_model::with(['rel_specialties', 'rel_day_names', 'rel_admin'])
		->find($this->table_row->id);

		$this->add_table_row_action();

		json_response(true, ['data' => $this->table_row]);
	}
	public function delete($id)
	{
		$this->set_table_row($id);

		$this->table_row->delete();

		json_response(true, ['message' => 'Record has been deleted']);
	}
	private function set_table_row($id=null)
	{
		if (is_null($id))
		{
			$this->table_row = new $this->table_model;

			$this->admin = new AdminModel;

			return;
		}

		$this->table_row = $this->table_model::find($id);

		if (!$this->table_row) json_response(false, ['message' => 'Invalid request']);

		$this->doctor_day_names = DoctorDayNameModel::where('doctor_id', $this->table_row->id)
		->get();

		$this->doctor_day_names_ids = $this->doctor_day_names->pluck('day_name_id')->toArray();

		#

		$this->doctor_specialties = DoctorSpecialtyModel::where('doctor_id', $this->table_row->id)
		->get();

		$this->doctor_specialties_ids = $this->doctor_specialties->pluck('specialty_id')->toArray();

		$this->admin = AdminModel::find($this->table_row->admin_id);

		if (!$this->admin)
		{
			$this->admin = new AdminModel;
		}
	}
	private function set_table_row_fields()
	{
		$this->table_row->first_name = post('first_name', 'strProperCase');

		if (strlen(post('middle_name')))
		{
			$this->table_row->middle_name = post('middle_name', 'strProperCase');
		}
		else 
		{
			$this->table_row->middle_name = null;
		}
		
		$this->table_row->last_name = post('last_name', 'strProperCase');

		$this->table_row->email = post('email', 'trim');
		
		$this->table_row->contact = post('contact', 'trim');

		$this->doctor_day_names_ids = array_key_exists('day_names_ids', $_POST) ? post('day_names_ids[]') : [];
		
		$this->doctor_specialties_ids = array_key_exists('specialties_ids', $_POST) ? post('specialties_ids[]') : [];

		$this->admin->username = post('username', 'trim');
		
		if (array_key_exists('password', $_POST) && strlen(post('password')))
		{
			$this->admin->password = post('password', 'trim');			
		}
	}
	private function validate_table_row_fields()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('first_name', 'first name', 'required');
		
		$this->form_validation->set_rules('last_name', 'last name', 'required');

		$this->form_validation->set_rules('email', 'email', 'required');

		$this->form_validation->set_rules('contact', 'contact', 'required|valid_mobile');
		
		// $this->form_validation->set_rules('day_name_ids[]', 'schedules', 'callback_check_day_names');

		// $this->form_validation->set_rules('specialties_ids[]', 'specialties', 'callback_check_specialties');

		$this->form_validation->set_rules('username', 'username', [
			'required',
			AdminModel::is_unique('username', $this->admin ? $this->admin->id : 0)
		]);

		if (!$this->table_row->id || !$this->admin->id)
		{
			$this->form_validation->set_rules('password', 'password', 'required');
		}
		else 
		{
			if (array_key_exists('password', $_POST) && strlen(post('password')))
			{
				$this->form_validation->set_rules('password', 'password', 'required');
			}
		}

		$form_data = $this->table_row->toArray();

		$form_data['username'] = $this->admin->username;
		$form_data['password'] = $this->admin->password;

		$this->form_validation->set_data($form_data);

		if ($this->form_validation->run() == FALSE)
		{
			json_response(false, ['message' => implode('</br>', $this->form_validation->error_array())]);
		}
	}
	private function add_table_row_action(&$table_row=null)
	{	
		if (is_null($table_row)) $table_row =& $this->table_row;

		$items = [];

		$items[] = el('a.dropdown-item.edit-row[href=#]', [
			el('i.ph-pencil-simple-line.me-2'),
			'Edit'
		]);

		$items[] = el('a.dropdown-item.delete-row[href=#]', [
			el('i.ph-trash.me-2'),
			'Delete'
		]);

		$table_row->action = el('.d-inline-flex > .dropdown', [
			el('a.text-body.actions[href=#][data-bs-toggle=dropdown]', [
				el('i.ph-list')
			]),
			el('.dropdown-menu dropdown-menu-end', $items)
		]);

		$table_row->action_edit = route($this->parent_route.'.edit', $table_row->id);

		$table_row->action_update = route($this->parent_route.'.update', $table_row->id);

		$table_row->action_delete = route($this->parent_route.'.delete', $table_row->id);
	}
	private function set_masterdata()
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
	}
	public function check_day_names($data)
	{
		if (!$this->doctor_day_names_ids)
		{
			return true;
		}

		$day_names = DayNameModel::whereIn('id', $this->doctor_day_names_ids)
		->count();

		if ($day_names != count($this->doctor_day_names_ids))
		{
			$this->form_validation->set_message(__FUNCTION__, 'Some day does not exists.');
			
			return false;
		}

		return true;
	}
	public function check_specialties($data)
	{
		if (!$this->doctor_specialties_ids)
		{
			return true;
		}

		$specialties = SpecialtyModel::whereIn('id', $this->doctor_specialties_ids)
		->count();

		if ($specialties != count($this->doctor_specialties_ids))
		{
			$this->form_validation->set_message(__FUNCTION__, 'Some specialty does not exists.');
			
			return false;
		}

		return true;
	}
}