<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function is_mobile($mobile, $format=null) 
{
	if (!is_numeric(trim($mobile))) return false;
	
	$mobile = (int)$mobile;
	
	if (strlen($mobile) == 10 && strStartsWith($mobile, '9')) return true;
	if (strlen($mobile) == 12 && strStartsWith($mobile, '639')) return true;
	
	return false;
}
function is_date($date, $format='Y-m-d') 
{
	return is_datetime($date, $format);
}
function is_time($time, $format='H:i:s')
{
	return is_datetime($time, $format);
}
function is_datetime($datetime, $format='Y-m-d H:i:s')
{
	$d = Datetime::createFromFormat($format, $datetime);

	return $d && $d->format($format) == $datetime;
}
function is_gender($gender) 
{
	return in_array(trim(strtolower($gender)), ['male', 'female']);
}
function is_decimal($value)
{
	return is_numeric($value) && !!preg_match('/^-?\d+\.\d+$/', $value); 
}
function is_email($email)
{
	return valid_email($email);
}