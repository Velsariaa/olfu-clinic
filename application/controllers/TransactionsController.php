<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransactionsController extends CI_Controller 
{
	private $parent_route = 'transactions';

	private $templates_dir = 'transactions';

	function __construct()
	{
		parent::__construct();

		$this->table_model = TransactionModel::class;
	}
	public function index()
	{
		$table = $this->table_model::with(['rel_patient', 'rel_transaction_type', 'rel_doctor']);

		$table = $table->orderBy('created_at', 'desc')->get();

		$table->each(function ($item, $key)
		{
			# ci()->add_table_row_action($item);
		});

		$this->sm->assign('table_data', $table->toJson());

		$this->sm->assign('parent_route', $this->parent_route);

		$this->sm->display($this->templates_dir.'/table.tpl');
	}
	public function new()
	{
	}
	public function create()
	{	
	}
	public function edit($id)
	{
	}
	public function update()
	{
	}
	public function delete($id)
	{
	}
	private function set_table_row($id=null)
	{
	}
	private function set_table_row_fields()
	{
	}
	private function validate_table_row_fields()
	{
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