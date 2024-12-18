<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Ramsey\Uuid\Uuid;

function strContains($string, $contains) {
	return strpos($string, $contains) !== false;
}
function strProperCase($string) {
	return strIsEmpty($string) ? $string : ucwords(strtolower(strCondense($string))); 
}
function strLowerCase($string) {
	return strIsEmpty($string) ? $string : strtolower($string); 	
}
function strUpperCase($string) {
	return strIsEmpty($string) ? $string : strtoupper($string); 	
}
function strCondense($string, $separator=' ') {
	return strIsEmpty($string) ? $string : join($separator, array_values(array_filter(explode($separator, $string))));
}
function strStartsWith($string, $starts_with) {
    return substr($string, 0, strlen($starts_with)) === $starts_with; 
}
function strEndsWith($string, $ends_with) 
{	
	if (is_string($ends_with)) $ends_with = [$ends_with];

	foreach ($ends_with as $ew) {
		if (substr($string, -strlen($ew)) === $ew) return true;
	}

    return false; 
}
function strNullWhenEmpty($string) { 
	return strIsEmpty($string) ? null : $string;
}
function strIsEmpty($string, $trim=true) {
	if ($trim) $string = trim($string);
	return !strlen($string);
}
function strToInt($string) {
	return is_numeric($string) ? (int)$string : 0;
}
function strFilters(&$var, $filters=null) 
{
	if (is_null($filters)) return;
	if (is_array($filters) && empty($filters)) return;
	if (is_string($filters) && !strlen(trim($filters))) return;

	if (is_string($filters)) $filters = explode('.', $filters);

	foreach ($filters as $filter) {
		if (function_exists($filter)) {
			$var = call_user_func($filter, $var);
		}
	}
}
function strFieldToName($string, $field_delimiter='_')
{
	return strProperCase(join(' ', array_filter(explode($field_delimiter, $string))));
}
function strFieldToLabel($string, $delimiter='_')
{
	$string = array_values(array_filter(explode($delimiter, $string)));

	return strProperCase(join(' ', $string));
}
function strRepeat($string, $repeat, $delimiter=',')
{
	return implode($delimiter, array_fill(0, $repeat, $string));
}
function strToArray($string) {
	return !is_array($string) ? [$string] : $string;
}
function uuid($uppercase=false)
{
	$id = uniqid().dechex(time());
	return $uppercase ? strtoupper($id) : strtolower($id);
}
function asCheckbox($string)
{	
	return ($string ?? '') == 'on';
} 
function strNullWhenZero($string)
{
	if (strval($string) === "0") return null;

	return $string;
}
function strUcFirstLowerRest($str)
{
	return ucfirst(strtolower($str));
}
function modelToSql($model, $dump=false)
{
	$sql = $model->toSql();

	foreach ($model->getBindings() as $value)
		$sql = preg_replace('/\?/', "'".$value."'", $sql, 1);

	if ($dump)
	{
		txt($sql);
	}
	
	return $sql;
}
function uuid4()
{
	$uuid = Uuid::uuid4();

	return $uuid->toString();
}
