<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AppointmentLimitsController extends CI_Controller 
{
	private $parent_route = 'appointment-limits';

	private $templates_dir = 'appointment_limits';

	function __construct()
	{
		parent::__construct();

		$this->table_model = AppointmentLimitModel::class;

		$this->table_row = null;
	}
	public function index()
	{
		$table = $this->table_model::get(); 

		$table->each(function ($item, $key)
		{
			ci()->add_table_row_action($item);
		});

		#

		$appointment_limits = [];

		foreach ($table as $item) {
			$appointment_limits[] = [
				'title' => 'Limit '.$item->_limit,
				'start' => $item->_date,
			];
		}

		$this->sm->assign('appointment_limits', json_encode($appointment_limits));

		#

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
	public function create()
	{	
		$this->set_table_row();

		$this->set_table_row_fields();

		$this->validate_table_row_fields();

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
	public function update()
	{
		$this->set_table_row(post('id'));

		$this->set_table_row_fields();

		$this->validate_table_row_fields();

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
		$this->table_row->_date = post('_date', 'trim');

		$this->table_row->_limit = post('_limit', 'trim.intval.abs');
	}
	private function validate_table_row_fields()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('_date', 'date', [
			'required',
			AppointmentLimitModel::is_unique('_date')
		]);

		$this->form_validation->set_rules('_limit', 'limit', [
			'required'
		]);

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
	public function date_limits()
	{
		$table = $this->table_model::get(); 

		$appointment_limits = [];

		foreach ($table as $item) {
			$appointment_limits[] = [
				'title' => 'Limit '.$item->_limit,
				'start' => $item->_date,
			];
		}

		json_response(true, ['data' => $appointment_limits]);
	}
}