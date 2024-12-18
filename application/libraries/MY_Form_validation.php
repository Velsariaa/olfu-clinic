<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class MY_Form_validation extends CI_Form_validation
{
	protected $ci;

	function __construct($config = array())
	{
		parent::__construct($config);

		$this->ci =& get_instance();
	}
	public function valid_date($date, $format=null)
	{
		$format = $format ? $format : 'Y-m-d';

		if (!is_date($date, $format))
		{
			$this->ci->form_validation->set_message(__FUNCTION__, "{$date} is not a valid date.");

			return false;
		}

		return true;
	}
	public function valid_time($date, $format=null)
	{
		$format = $format ? $format : 'H:i:s';

		if (!is_time($date, $format))
		{
			$this->ci->form_validation->set_message(__FUNCTION__, "{$date} is not a valid time.");

			return false;
		}

		return true;
	}
	public function valid_mobile($str, $type)
	{
		if (!is_mobile($str))
		{
			$this->ci->form_validation->set_message(__FUNCTION__, "{$str} is not a mobile.");

			return false;
		}

		return true;
	}
	public function is_unique2($str, $field)
	{	
		sscanf($field, '%[^.].%[^.].%[^.].%[^.]', $table, $key_name, $id, $fieldname);

		$q = DB::table(DB::raw($table))
		->where($fieldname, trim($str));

		if ($id) $q = $q->where('id', '<>', $id);

		$rows = $q->count();

		if ($rows)
		{
			$this->ci->form_validation->set_message(__FUNCTION__, "The {field} field must contain a unique value.");

			return false;	
		}

		return true;
	}
	public function is_exists($str, $field)
	{
		sscanf($field, '%[^.].%[^.]', $table, $field);

		$q = DB::table(DB::raw($table))
		->where($field, trim($str));

		$rows = $q->count();

		if (!$rows)
		{
			$this->ci->form_validation->set_message(__FUNCTION__, "The {field} field does not exists.");

			return false;	
		}

		return true;
	}
	public function is_not_exists($str, $field)
	{
		sscanf($field, '%[^.].%[^.]', $table, $field);

		$q = DB::table(DB::raw($table))
		->where($field, trim($str));

		$rows = $q->count();

		if ($rows > 0)
		{
			$this->ci->form_validation->set_message(__FUNCTION__, "The {field} field already exists.");

			return false;	
		}

		return true;
	}
	public function is_all_exists($values, $field)
	{
		sscanf($field, '%[^.].%[^.]', $table, $field);

		$q = DB::table(DB::raw($table))
		->where($field, trim($str));

		$rows = $q->count();

		if (!$rows)
		{
			$this->ci->form_validation->set_message(__FUNCTION__, "The {field} field does not exists.");

			return false;	
		}

		return true;
	}
}