<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class UsersTransactionsController extends CI_Controller 
{
	private $parent_route = 'users-transactions';

	private $templates_dir = 'users_transactions';

	function __construct()
	{
		parent::__construct();

		$this->table_model = TransactionModel::class;

		$this->table_row = null;
	}
	public function index()
	{
		$user = userLoggedIn();

		$patient = PatientModel::where('user_id', $user->id)
		->first();

		$table = $this->table_model::with(['rel_transaction_type'])
		->where('patient_id', $patient ? $patient->id : 0)
		->orderBy('created_at', 'desc')
		->get(); 

		$this->sm->assign('table_data', $table->toJson());

		$this->sm->assign('parent_route', $this->parent_route);

		$this->sm->display($this->templates_dir.'/table.tpl');
	}
}