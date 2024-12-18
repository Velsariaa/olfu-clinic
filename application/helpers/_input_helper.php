<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function get($key=null)
{
	return get_instance()->input->get($key);
}
function post($key=null, $filters=null, $clean_xss=true)
{
	$data = get_instance()->input->post($key, $clean_xss);

	if (!is_null($filters)) strFilters($data, $filters);

	return $data;
}