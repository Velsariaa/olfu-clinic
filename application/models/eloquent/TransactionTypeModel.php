<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class TransactionTypeModel extends BaseModel
{
	protected $table = 'transaction_types';

	protected $appends = ['date_created', 'date_updated'];

	protected $hidden = ['created_at', 'updated_at'];
}