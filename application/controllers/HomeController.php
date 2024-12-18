<?php
defined('BASEPATH') or exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class HomeController extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	public function home()
	{
		$announcements = AnnouncementModel::orderBy('updated_at', 'desc')
		->get();

		$this->sm->assign('announcements', $announcements);

		#

		$js_events = [];

		$admin = adminLoggedIn();

		if ($admin->user_type == USER_TYPE_USER) {
			$user = userLoggedIn();
			$patient = PatientModel::where('user_id', $user->id)->first();

			$appointments = AppointmentModel::where('user_id', $user->id)
				->where('status', STATUS_APPROVED)
				->get();

			foreach ($appointments as $appointment) {
				$js_events[] = [
					'title' => 'My Appointment',
					'start' => $appointment->appointment_date,
					'url' => route('users-appointments')
				];
			}
		} else {
			$appointments = AppointmentModel::where('status', STATUS_APPROVED)
				->where('appointment_date', '>=', date('Y-m-d'))
				->get();

			foreach ($appointments as $appointment) {
				$js_events[] = [
					'title' => 'Appointment',
					'start' => $appointment->appointment_date,
					'url' => route('appointments')."?date_from={$appointment->appointment_date}&date_to={$appointment->appointment_date}"
				];
			}
		}

		$this->sm->assign('events', json_encode($js_events));

		$this->sm->display('home/home.tpl');
	}
	public function about_us()
	{
		$this->sm->display('home/about_us.tpl');
	}
}
