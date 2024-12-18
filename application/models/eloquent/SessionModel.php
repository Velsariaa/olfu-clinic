<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class SessionModel extends BaseModel
{
	protected $table = 'sessions';
}