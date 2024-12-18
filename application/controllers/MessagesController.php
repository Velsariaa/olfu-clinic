<?php
defined('BASEPATH') or exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class MessagesController extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	public function user()
	{
		$user = userLoggedIn();

		$messages = MessageModel::where('user_id', $user->id)
		->get();

		$this->sm->assign('messages', $messages);

		$this->sm->display('messages/user.tpl');
	}
	public function user_message()
	{
		$user = userLoggedIn();
		$admin = adminLoggedIn();

		$message = new MessageModel;
		$message->user_id = $user->id;
		$message->sender_id = $user->id;
		$message->message = post('message');
		$message->user_type = $admin->user_type;
		$message->name = $user->name;
		$message->icon = $user->icon_name;

		if (!strlen(trim($message->message))) 
		{
			json_response('false');
		}

		$message->save();
	}
	public function staff()
	{
		$ids = MessageModel::select(DB::raw('MAX(id) as id'))
		->where('user_type', USER_TYPE_USER)
		->groupBy('user_id')
		->pluck('id')
		->toArray();

		$table = MessageModel::from(MessageModel::getTableName('me'))
		->whereIn('id', $ids)
		->orderBy('created_at', 'desc')
		->get(); 

		$table->each(function ($item, $key)
		{
			$item->action = el('a[href='.route('messages.staff-user', $item->user_id).']', [
				el('button.btn.btn-success.btn-sm', [
					'View'
				])
			]);
		});

		$this->sm->assign('table_data', $table->toJson());

		$this->sm->display('messages/table.tpl');
	}
	public function staff_user($user_id)
	{
		$user = UserModel::find($user_id);

		if (!$user) die();

		$messages = MessageModel::where('user_id', $user->id)
		->get();

		$this->sm->assign('save_url', route('messages.staff-user-message', $user->id));

		$this->sm->assign('messages', $messages);

		$this->sm->display('messages/staff.tpl');
	}
	public function staff_user_message($user_id)
	{
		$user = UserModel::find($user_id);

		if (!$user) json_response(false);

		$admin = adminLoggedIn();

		switch ($admin->user_type) {
			case USER_TYPE_SUPER_ADMIN:
			case USER_TYPE_ADMIN:
				$staff = adminLoggedIn();
				break;
			case USER_TYPE_NURSE:
				$staff = nurseLoggedIn();
				break;
			case USER_TYPE_DOCTOR:
				$staff = doctorLoggedIn();
				break;
			default:
				json_response(false);
				break;
		}

		$message = new MessageModel;
		$message->user_id = $user->id;
		$message->sender_id = $staff->id;
		$message->message = post('message');
		$message->user_type = $admin->user_type;
		$message->name = $staff->name;
		$message->icon = $staff->icon_name;

		if (!strlen(trim($message->message))) 
		{
			json_response('false');
		}

		$message->save();
	}
}
