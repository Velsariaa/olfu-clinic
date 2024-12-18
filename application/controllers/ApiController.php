<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class ApiController extends CI_Controller 
{
	private $templates_dir = 'api';

	function __construct()
	{
		parent::__construct();
	}
}