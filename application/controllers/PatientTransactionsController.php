<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class PatientTransactionsController extends CI_Controller 
{
	private $parent_route = 'patient-transaction';

	private $templates_dir = 'patient_transactions';

	function __construct()
	{
		parent::__construct();

		$this->table_model = TransactionModel::class;

		$this->table_row = null;
	}
	public function index($patient_id)
	{
		$table = $this->table_model::with([])
		->where('patient_id', $patient_id);
		
		$table = $table->get(); 

		$table->each(function ($item, $key)
		{
			ci()->add_table_row_action($item);
		});
		
		json_response(true, ['data' => $table]);
	}
	public function new($patient_id)
	{
		$patient = PatientModel::find($patient_id);

		$this->sm->assign('patient', $patient);

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
	}
	private function post_save()
	{
	}
	public function create($patient_id)
	{	
		$patient = PatientModel::find($patient_id);

		$this->set_table_row();

		$this->set_table_row_fields();

		$this->validate_table_row_fields();

		$this->pre_create();

		$this->handle_transaction();

		$this->table_row = $this->table_model::with([])
		->find($this->table_row->id);

		$this->add_table_row_action();

		json_response(true, ['data' => $this->table_row]);
	}
	public function edit($patient_id, $id)
	{
		$patient = PatientModel::find($patient_id);

		$this->sm->assign('patient', $patient);

		$this->set_table_row($id);

		$this->sm->assign('row', $this->table_row);

		$this->set_masterdata();

		$this->sm->assign('parent_route', $this->parent_route);

		$html = $this->sm->fetch($this->templates_dir.'/entry.tpl');

		json_response(true, ['html' => $html]);
	}
	public function update($patient_id, $id)
	{
		$patient = PatientModel::find($patient_id);

		$this->set_table_row(post('id'));

		$this->set_table_row_fields();

		$this->validate_table_row_fields();

		$this->handle_transaction();

		$this->table_row = $this->table_model::with([])
		->find($this->table_row->id);

		$this->add_table_row_action();

		json_response(true, ['data' => $this->table_row]);
	}
	public function delete($patient_id, $id)
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
		$this->table_row->patient_id = post('patient_id', 'intval');

		$this->table_row->transaction_type_id = post('transaction_type_id', 'intval');

		$this->table_row->doctor_id = post('doctor_id', 'intval');

		$this->table_row->weight = post('weight', 'floatval');

		$this->table_row->height = post('height', 'floatval');

		$this->table_row->remarks = post('remarks', 'trim');
	}
	private function validate_table_row_fields()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('patient_id', 'patient', 'required');
		
		$this->form_validation->set_rules('transaction_type_id', 'transaction type', 'required');

		$this->form_validation->set_rules('doctor_id', 'doctor', 'required');

		$this->form_validation->set_rules('weight', 'weight', 'required');

		$this->form_validation->set_rules('height', 'height', 'required');

		$this->form_validation->set_rules('remarks', 'remarks', 'required');

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

		$items[] = el('a.dropdown-item.edit-record-row[href=#]', [
			el('i.ph-pencil-simple-line.me-2'),
			'Edit'
		]);

		$items[] = el('a.dropdown-item.delete-record-row[href=#]', [
			el('i.ph-trash.me-2'),
			'Delete'
		]);

		$table_row->action = el('.d-inline-flex > .dropdown', [
			el('a.text-body.actions[href=#][data-bs-toggle=dropdown]', [
				el('i.ph-list')
			]),
			el('.dropdown-menu dropdown-menu-end', $items)
		]);

		$table_row->action_edit = route($this->parent_route.'.edit', [
			'patient_id' => $table_row->patient_id,
			'id' => $table_row->id
		]);

		$table_row->action_update = route($this->parent_route.'.update', [
			'patient_id' => $table_row->patient_id,
			'id' => $table_row->id
		]);

		$table_row->action_delete = route($this->parent_route.'.delete', [
			'patient_id' => $table_row->patient_id,
			'id' => $table_row->id
		]);

		$table_row->modal_title = 'Transaction';
	}
	private function set_masterdata()
	{
		$transaction_types = TransactionTypeModel::select2('transaction_type');

		$this->sm->assign('transaction_types', $transaction_types);

		$doctors = DoctorModel::select2('first_name', null, false, function ($table)
		{
		}, 
		"concat_ws(' ', first_name, last_name)");

		$this->sm->assign('doctors', $doctors);	
	}
}