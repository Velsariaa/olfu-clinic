<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class AppointmentsController extends CI_Controller 
{
	private $parent_route = 'appointments';

	private $templates_dir = 'appointments';

	function __construct()
	{
		parent::__construct();

		$this->table_model = AppointmentModel::class;

		$this->table_row = null;
	}
	public function index()
	{
		$table = $this->table_model::with(['rel_user', 'rel_specialty', 'rel_doctor']);

		if (get('date_from') && get('date_to'))
		{
			$table = $table->whereBetween('appointment_date', [
				get('date_from'), get('date_to')
			]);
		}
		
		$table = $table->orderBy('created_at', 'desc')->get();

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
	public function create()
	{	
		$this->set_table_row();

		$this->set_table_row_fields();

		$this->validate_table_row_fields();

		// if ($this->table_row->appointment_date)
		// {
		// 	$limit = AppointmentLimitModel::where('_date', $this->table_row->appointment_date)
		// 	->count();

		// 	if ($limit > 0)
		// 	{
		// 		$count = $this->table_model::where('appointment_date', $this->table_row->appointment_date)
		// 		->count();

		// 		if ($count > $limit)
		// 		{
		// 			json_response(false, ['message' => "Only {$count} appointment is allowed"]);
		// 		}
		// 	}
		// }

		$this->table_row->save();

		$this->table_row = $this->table_model::with(['rel_user', 'rel_specialty', 'rel_doctor'])
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

		$admin = adminLoggedIn();

		if ($this->table_row->status != $this->table_row->getOriginal('status'))
		{
			if (in_array($admin->user_type, [USER_TYPE_ADMIN, USER_TYPE_SUPER_ADMIN]))
			{
				$this->table_row->status_updated_by = $admin->name;
			}
			else if ($admin->user_type == USER_TYPE_NURSE)
			{
				$this->table_row->status_updated_by = nurseLoggedIn()->name;
			}
			else if ($admin->user_type == USER_TYPE_DOCTOR)
			{
				$this->table_row->status_updated_by = doctorLoggedIn()->name;
			}

			$this->table_row->status_updated_at = current_datetime();
		}

		if ($this->table_row->appointment_date)
		{
			$limit = AppointmentLimitModel::where('_date', $this->table_row->appointment_date)
			->count();

			if ($limit > 0)
			{
				$count = $limit;

				if ($this->table_row->getOriginal('appointment_date'))
				{
					if ($this->table_row->getOriginal('appointment_date') != $this->table_row->appointment_date)
					{
						$count = $this->table_model::where('appointment_date', $this->table_row->appointment_date)
						->count();	
					}
				}
				else
				{
					$count = $this->table_model::where('appointment_date', $this->table_row->appointment_date)
					->count();
				}
				
				if ($count > $limit)
				{
					json_response(false, ['message' => "Only {$count} appointment is allowed"]);
				}
			}
		}

		$this->table_row->save();

		$this->table_row = $this->table_model::with(['rel_user', 'rel_specialty', 'rel_doctor'])
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
		# $this->table_row->doctor_id = post('doctor_id');

		# $this->table_row->appointment_date = post('appointment_date');

		# $this->table_row->appointment_date = $this->table_row->appointment_date ? $this->table_row->appointment_date : null;

		# $this->table_row->appointment_time = post('appointment_time');
		
		# $this->table_row->appointment_time = $this->table_row->appointment_time ? $this->table_row->appointment_time : null;

		$this->table_row->status = post('status');

		# $this->table_row->status_remarks = post('status_remarks');
	}
	private function validate_table_row_fields()
	{
		$this->load->library('form_validation');

		// $this->form_validation->set_rules('doctor_id', 'doctor', [
		// 	'required',
		// 	DoctorModel::is_exists()
		// ]);

		// if (in_array($this->table_row->status, ['Approved']))
		// {
		// 	$this->form_validation->set_rules('appointment_date', 'appointment date', 'required');	

		// 	$this->form_validation->set_rules('appointment_time', 'appointment time', 'required');	
		// }

		// if (in_array($this->table_row->status, ['Disapproved']))
		// {
		// 	$this->form_validation->set_rules('status_remarks', 'status remarks', 'required');
		// }

		$this->form_validation->set_rules('status', 'status', 'required');

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

		// $items[] = el('a.dropdown-item.delete-row[href=#]', [
		// 	el('i.ph-trash.me-2'),
		// 	'Delete'
		// ]);

		// $table_row->action = el('.d-inline-flex > .dropdown', [
		// 	el('a.text-body.actions[href=#][data-bs-toggle=dropdown]', [
		// 		el('i.ph-list')
		// 	]),
		// 	el('.dropdown-menu dropdown-menu-end', $items)
		// ]);

		if (in_array($table_row->status, ['Pending']))
		{
			$table_row->action = el('div.btn-group', [
				el('button.btn.btn-primary.btn-sm.btn-appointment-status[data-status=Approved]', ['Approved']),
				el('button.btn.btn-danger.btn-sm.btn-appointment-status[data-status=Disapproved]', ['Disapproved']),
			]);
		}
		else
		{
			$table_row->action = null;
		}

		$table_row->action_edit = route($this->parent_route.'.edit', $table_row->id);

		$table_row->action_update = route($this->parent_route.'.update', $table_row->id);

		$table_row->action_delete = route($this->parent_route.'.delete', $table_row->id);
	}
	private function set_masterdata()
	{
		$doctors = DoctorModel::select2('first_name', null, true, function ($table)
		{
			$table->whereExists(function ($query)
			{
				$query->select(DB::raw(1))
				->from('doctor_specialties')
				->where('doctor_id', DB::raw('doctors.id'))
				->where('specialty_id', $this->table_row->specialty_id);
			});
		}, 
		"concat_ws(' ', first_name, last_name)");

		$this->sm->assign('doctors', $doctors);
	}
}