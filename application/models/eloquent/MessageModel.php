<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class MessageModel extends BaseModel
{
	protected $table = 'messages';

	protected $appends = ['date_created', 'date_updated', 'time_ago'];

	protected $hidden = ['created_at', 'updated_at'];

	public function getTimeAgoAttribute()
	{
		return get_time_ago(strtotime($this->created_at->format('Y-m-d H:i:s')));
	}
}