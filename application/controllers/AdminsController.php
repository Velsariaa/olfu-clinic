<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminsController extends CI_Controller 
{
	private $parent_route = 'admins';

	private $templates_dir = 'admins';

	function __construct()
	{
		parent::__construct();

		$this->table_model = AdminModel::class;

		$this->table_row = null;
	}
	public function index()
	{
		$admin = AdminModel::find(loginId());

		$table = $this->table_model::whereIn('user_type', [USER_TYPE_SUPER_ADMIN, USER_TYPE_ADMIN])
		->when($admin->user_type != USER_TYPE_SUPER_ADMIN, function ($query)
		{
			return $query->where('user_type', '<>', USER_TYPE_SUPER_ADMIN);
		})
		->get(); 

		$table->each(function ($item, $key)
		{
			$item->date = date('M. d, Y', strtotime($item->date));

			ci()->add_table_row_action($item);
		});

		$this->sm->assign('table_data', $table->toJson());

		$this->sm->assign('parent_route', $this->parent_route);

		$this->sm->display($this->templates_dir.'/table.tpl');
	}
	public function new()
	{
		# $this->set_masterdata();

		$this->sm->assign('parent_route', $this->parent_route);

		$html = $this->sm->fetch($this->templates_dir.'/entry.tpl');

		json_response(true, ['html' => $html]);
	}
	private function pre_create()
	{
		$this->table_row->user_type = USER_TYPE_ADMIN;
	}
	private function pre_save()
	{
		$this->table_row->name = $this->table_row->first_name.' '.$this->table_row->last_name;
		
		if (array_key_exists('password', $_POST) && post('password'))
		{
			$this->table_row->password = encryptPassword($this->table_row->password);
		}
	}
	public function create()
	{	
		$this->set_table_row();

		$this->set_table_row_fields();

		$this->validate_table_row_fields();

		$this->pre_create();

		$this->pre_save();

		$this->table_row->save();

		$this->add_table_row_action();

		json_response(true, ['data' => $this->table_row]);
	}
	public function edit($id)
	{
		$this->set_table_row($id);

		$this->sm->assign('row', $this->table_row);

		# $this->set_masterdata();

		$this->sm->assign('parent_route', $this->parent_route);

		$html = $this->sm->fetch($this->templates_dir.'/entry.tpl');

		json_response(true, ['html' => $html]);
	}
	private function pre_update()
	{

	}
	public function update()
	{
		$this->set_table_row(post('id'));

		$this->set_table_row_fields();

		$this->validate_table_row_fields();

		$this->pre_update();

		$this->pre_save();

		$this->table_row->save();

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

			return;
		}

		$this->table_row = $this->table_model::find($id);

		if (!$this->table_row) json_response(false, ['message' => 'Invalid request']);
	}
	private function set_table_row_fields()
	{
		$this->table_row->first_name 	= post('first_name', 'strProperCase');
		$this->table_row->last_name 	= post('last_name', 'strProperCase');
		$this->table_row->username 		= post('username', 'trim');

		if (array_key_exists('password', $_POST) && post('password'))
		{
			$this->table_row->password = post('password', 'trim');			
		}
	}
	private function validate_table_row_fields()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('first_name', 'first name', 'required');
		$this->form_validation->set_rules('last_name', 'last name', 'required');
		$this->form_validation->set_rules('username', 'username', [
			'required',
			AdminModel::is_unique('username', post('id'))
		]);

		if (!$this->table_row->id || (array_key_exists('password', $_POST) && post('password')))
		{
			$this->form_validation->set_rules('password', 'password', 'required');
		}

		$this->form_validation->set_data($this->table_row->toArray());

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

		if ($table_row->user_type != 1)
		{
			$items[] = el('a.dropdown-item.delete-row[href=#]', [
				el('i.ph-trash.me-2'),
				'Delete'
			]);
		}

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