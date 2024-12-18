<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class UsersController extends CI_Controller 
{
	private $parent_route = 'users';

	private $templates_dir = 'users';

	function __construct()
	{
		parent::__construct();

		$this->table_model = UserModel::class;

		$this->table_row = null;
	}
	public function index()
	{
		$table = $this->table_model::with(['rel_admin']);

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
	}
	public function create()
	{	
		$this->set_table_row();

		$this->set_table_row_fields();

		$this->validate_table_row_fields();

		$this->pre_create();

		$this->handle_transaction();

		$this->table_row = $this->table_model::with(['rel_admin'])
		->find($this->table_row->id);

		$this->add_table_row_action();

		json_response(true, ['data' => $this->table_row]);
	}
	public function edit($id)
	{
		$this->set_table_row($id);

		$this->sm->assign('row', $this->table_row);

		$this->sm->assign('admin', $this->admin);

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

		$this->table_row = $this->table_model::with(['rel_admin'])
		->find($this->table_row->id);

		$this->add_table_row_action();

		json_response(true, ['data' => $this->table_row]);
	}
	public function delete($id)
	{
		$this->set_table_row($id);

		if ($this->table_row)
		{
			$patient = PatientModel::where(DB::raw("ifnull(user_id, 0)"), $this->table_row->id)
			->first();

			json_response(false, ['message' => 'This account is associated to patient '.$patient->name.'.</br>Please remove it first.']);	
		}

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

		$this->table_row->contact = post('contact', '');

		$this->table_row->email = post('email', '');

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

		$this->form_validation->set_rules('contact', 'contact', 'required|valid_mobile');

		$this->form_validation->set_rules('email', 'email', 'required');

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
	}
}