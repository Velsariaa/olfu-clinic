<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function setUserdata($param1, $param2 = null)
{
	$ci =& get_instance();

	if (is_array($param1))
		$ci->session->set_userdata($param1);
	else
		$ci->session->set_userdata($param1, $param2);
}
function userdata($key)
{
	return get_instance()->session->userdata($key);
}
function unsetUserdata($key)
{
	$ci =& get_instance();

	$ci->session->unset_userdata($key);
}
function setFlashdata($param1, $param2 = null)
{
	$ci =& get_instance();

	if (is_array($param1))
		$ci->session->set_flashdata($param1);
	else
		$ci->session->set_flashdata($param1, $param2);
}
function flashdata($key=null)
{
	return get_instance()->session->flashdata($key);
}