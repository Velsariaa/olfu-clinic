<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class PatientsController extends CI_Controller 
{
	private $parent_route = 'patients';

	private $templates_dir = 'patients';

	function __construct()
	{
		parent::__construct();

		$this->table_model = PatientModel::class;

		$this->table_row = null;
	}
	public function index()
	{
		$table = $this->table_model::with(['rel_user']);

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
	}
	private function pre_save()
	{
		$this->table_row->contact = '0'.substr($this->table_row->contact, -10);

		$this->table_row->user_id = $this->table_row->user_id ? $this->table_row->user_id : null;
	}
	private function post_save()
	{
	}
	public function create()
	{	
		$this->set_table_row();

		$this->set_table_row_fields();

		$this->validate_table_row_fields();

		$this->pre_create();

		$this->handle_transaction();

		$this->table_row = $this->table_model::with(['rel_user'])
		->find($this->table_row->id);

		$this->add_table_row_action();

		json_response(true, ['data' => $this->table_row]);
	}
	public function edit($id)
	{
		$this->set_table_row($id);

		$this->sm->assign('row', $this->table_row);

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

		$this->table_row = $this->table_model::with(['rel_user'])
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

		$this->table_row->gender = post('gender', '');

		$this->table_row->birth_date = post('birth_date', '');

		$this->table_row->birth_place = post('birth_place', '');

		$this->table_row->address = post('address', '');

		$this->table_row->contact = post('contact', '');

		$this->table_row->email = post('email', '');

		$this->table_row->user_id = post('user_id', '');
	}
	private function validate_table_row_fields()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('first_name', 'first name', 'required');
		
		$this->form_validation->set_rules('last_name', 'last name', 'required');
		
		$this->form_validation->set_rules('gender', 'gender', 'required');

		$this->form_validation->set_rules('birth_date', 'birth date', 'required');

		$this->form_validation->set_rules('birth_place', 'birth place', 'required');

		$this->form_validation->set_rules('address', 'address', 'required');

		$this->form_validation->set_rules('contact', 'contact', 'required|valid_mobile');

		$this->form_validation->set_rules('email', 'email', 'required');

		if ($this->table_row->user_id)
		{
			$this->form_validation->set_rules('user_id', 'user', [
				UserModel::is_exists()
			]);
		}

		$form_data = $this->table_row->toArray();

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

		$items[] = el('a.dropdown-item.records-row[href=#]', [
			el('i.ph-list-checks.me-2'),
			'Records'
		]);

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

		$table_row->action_records = route($this->parent_route.'.records', $table_row->id);

		#

		$table_row->action_transactions = route('patient-transaction', [
			'patient_id' => $table_row->id
		]);
		
		$table_row->action_new_transaction = route('patient-transaction.new', [
			'patient_id' => $table_row->id
		]);

		$table_row->action_create_transaction = route('patient-transaction.create', [
			'patient_id' => $table_row->id
		]);

		$table_row->action_edit_transaction = route('patient-transaction.edit', [
			'patient_id' => $table_row->id,
			'id' => 0
		]);

		$table_row->action_update_transaction = route('patient-transaction.update', [
			'patient_id' => $table_row->id,
			'id' => 0
		]);

		$table_row->action_delete_transaction = route('patient-transaction.delete', [
			'patient_id' => $table_row->id,
			'id' => 0
		]);
	}
	private function set_masterdata()
	{
		$users = UserModel::select2('first_name', null, true, function ($table)
		{
			$table->select([
				'first_name',
				'id as value',
				DB::raw("concat_ws(' ', first_name, last_name) as text")
			]);
		});

		$this->sm->assign('users', $users);
	}
	public function records($id)
	{
		$this->set_table_row($id);

		$this->sm->assign('row', $this->table_row);

		$html = $this->sm->fetch($this->templates_dir.'/records.tpl');

		json_response(true, ['html' => $html]);
	}
}