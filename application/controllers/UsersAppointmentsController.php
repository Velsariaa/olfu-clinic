<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class UsersAppointmentsController extends CI_Controller 
{
	private $parent_route = 'users-appointments';

	private $templates_dir = 'users_appointments';

	function __construct()
	{
		parent::__construct();

		$this->table_model = AppointmentModel::class;

		$this->table_row = null;
	}
	public function index()
	{
		$user = userLoggedIn();

		$table = $this->table_model::with(['rel_specialty', 'rel_doctor'])
		->where('user_id', $user->id)
		->orderBy('created_at', 'desc')
		->get(); 

		$table->each(function ($item, $key)
		{
			ci()->add_table_row_action($item);
		});

		#

		$limits = AppointmentLimitModel::where('_limit', '>', 0)
		->get()
		->pluck('_date')
		->toArray();

		$this->sm->assign('limit_dates', json_encode($limits));

		#

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
	public function create()
	{	
		$this->set_table_row();

		$this->set_table_row_fields();

		$this->validate_table_row_fields();

		$this->table_row->user_id = userLoggedIn()->id;

		#

		$limit = AppointmentLimitModel::where('_date', $this->table_row->appointment_date)
		->first();

		if (!$limit) json_response(false, ['message' => 'Appointment limit is missing']);

		$count = $this->table_model::where('appointment_date', $this->table_row->appointment_date)
		->count();

		if ($count > $limit->_limit)
		{
			json_response(false, ['message' => "Only {$limit->_limit} appointment is allowed"]);
		}
		
		#

		$this->table_row->save();

		$this->table_row = $this->table_model::with(['rel_specialty', 'rel_doctor'])
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

		#
		
		$limit = AppointmentLimitModel::where('_date', $this->table_row->appointment_date)
		->first();

		if (!$limit) json_response(false, ['message' => 'Appointment limit is missing']);

		$count = $limit->_limit;

		if ($this->table_row->getOriginal('appointment_date'))
		{
			if ($this->table_row->getOriginal('appointment_date') != $this->table_row->appointment_date)
			{
				$count = $this->table_model::where('appointment_date', $this->table_row->appointment_date)
				->count() + 1;	
			}
		}
		else
		{
			$count = $this->table_model::where('appointment_date', $this->table_row->appointment_date)
			->count();
		}
		
		if ($count > $limit->_limit)
		{
			json_response(false, ['message' => "Only {$limit->_limit} appointment is allowed"]);
		}
		
		#

		$this->table_row->save();

		$this->table_row = $this->table_model::with(['rel_specialty', 'rel_doctor'])
		->find($this->table_row->id);

		$this->add_table_row_action();

		json_response(true, ['data' => $this->table_row]);
	}
	public function delete($id)
	{
		$this->set_table_row($id);

		if ($this->table_row->status == 'Approved')
		{
			json_response(true, ['message' => 'Appointment is already approved']);	
		}
		else if ($this->table_row->status == 'Dispproved')
		{
			json_response(true, ['message' => 'Appointment is already disapproved']);	
		}
		else if ($this->table_row->status == 'Cancelled')
		{
			json_response(true, ['message' => 'Appointment is already cancelled']);	
		}

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
		# $this->table_row->specialty_id = post('specialty_id', 'intval');

		# $this->table_row->doctor_id = post('doctor_id');

		$this->table_row->appointment_date = post('appointment_date', 'trim');

		$this->table_row->appointment_time = post('appointment_time', 'trim');

		$this->table_row->reason = post('reason', 'trim');
	}
	private function validate_table_row_fields()
	{
		$this->load->library('form_validation');

		// $this->form_validation->set_rules('specialty_id', 'specialty', [
		// 	'required',
		// 	SpecialtyModel::is_exists()
		// ]);

		// if (post('doctor_id'))
		// {
		// 	$this->form_validation->set_rules('doctor_id', 'doctor', [
		// 		DoctorModel::is_exists()
		// 	]);
		// }

		$this->form_validation->set_rules('reason', 'reason', 'required');

		$this->form_validation->set_rules('appointment_date', 'appointment date', 'required');

		$this->form_validation->set_rules('appointment_time', 'appointment time', 'required');

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

		if ($table_row->status == STATUS_PENDING)
		{
			if ($table_row)
			{
				$items[] = el('a.dropdown-item.cancel-row[href=#]', [
					el('i.ph-x.me-2'),
					'Cancel'
				]);
			}
	
			$items[] = el('a.dropdown-item.edit-row[href=#]', [
				el('i.ph-pencil-simple-line.me-2'),
				'Edit'
			]);
	
			$items[] = el('a.dropdown-item.delete-row[href=#]', [
				el('i.ph-trash.me-2'),
				'Delete'
			]);
		}

		if ($items)
		{
			$table_row->action = el('.d-inline-flex > .dropdown', [
				el('a.text-body.actions[href=#][data-bs-toggle=dropdown]', [
					el('i.ph-list')
				]),
				el('.dropdown-menu dropdown-menu-end', $items)
			]);
		}
		else
		{
			$table_row->action = null;
		}

		$table_row->action_edit = route($this->parent_route.'.edit', $table_row->id);

		$table_row->action_update = route($this->parent_route.'.update', $table_row->id);

		$table_row->action_delete = route($this->parent_route.'.delete', $table_row->id);

		$table_row->action_cancel_form = route($this->parent_route.'.cancel-form', $table_row->id);

		$table_row->action_cancel = route($this->parent_route.'.cancel', $table_row->id);
	}
	private function set_masterdata()
	{
		$specialties = SpecialtyModel::select2('specialty');

		$this->sm->assign('specialties', $specialties);
	}
	public function cancel_form($id) 
	{
		$this->set_table_row($id);

		$this->sm->assign('row', $this->table_row);

		$this->sm->assign('parent_route', $this->parent_route);

		$html = $this->sm->fetch($this->templates_dir.'/cancel.tpl');

		json_response(true, ['html' => $html]);
	}
	public function cancel($id)
	{
		$this->set_table_row($id);

		if (!$this->table_row->status == 'Approved')
		{
			json_response(false, ['message' => 'Appointment is already approved']);
		}
		if (!$this->table_row->status == 'Disapproved')
		{
			json_response(false, ['message' => 'Appointment is already disapproved']);
		}
		if (!$this->table_row->status == 'Cancelled')
		{
			json_response(false, ['message' => 'Appointment is already cancelled']);
		}
		if (!post('status_remarks'))
		{
			json_response(false, ['message' => 'Reason is required']);
		}
		
		$this->table_row->status = 'Cancelled';
		$this->table_row->status_remarks = post('status_remarks');
		$this->table_row->status_updated_by = userLoggedIn()->name;
		$this->table_row->status_updated_at = date('Y-m-d H:i:s');
		
		$this->table_row->save();

		$this->table_row = $this->table_model::with(['rel_specialty', 'rel_doctor'])
		->find($this->table_row->id);

		$this->add_table_row_action();

		json_response(true, ['data' => $this->table_row]);
	}
	public function specialty_doctors()
	{
		$this->set_table_row(get('id'));

		$this->sm->assign('row', $this->table_row);

		$doctors = DoctorModel::select2('first_name', null, true, function ($table)
		{
			$table->whereExists(function ($query)
			{
				$query->select(DB::raw(1))
				->from('doctor_specialties')
				->where('doctor_id', DB::raw('doctors.id'))
				->where('specialty_id', get('specialty_id'));
			});
		}, 
		"concat_ws(' ', first_name, last_name)");

		$this->sm->assign('doctors', $doctors);

		$html = $this->sm->fetch($this->templates_dir.'/select_doctors.tpl');
		
		json_response(true, ['doctors' => $doctors, 'html' => $html]);
	}
}