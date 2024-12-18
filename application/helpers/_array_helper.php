<?php
function findItem($parent, $keys, $default=null)
{
	$keys = explode('.', $keys);

	$key = array_shift($keys);

	$child = $default;

	if (is_object($parent)) {
		if (method_exists($parent, $key))
			$child = $parent->$key();
		else
			$child = $parent->$key ?? $default;
	}
	elseif (is_array($parent)) {
		$child = $parent[$key] ?? $default;
	}

	if (is_null($child)) return $child;

	if (count($keys) > 0) 
		return findItem($child, implode('.', $keys), $default);
	else
		return $child;
}
/* NP3 | 2021-08-31 | new version of findItem */
function find(&$items, $keys, $default=null, $unset=false, $debug=false)
{
	if (!is_array($keys)) $keys = explode('.', $keys);

	$key = array_shift($keys);

	$function = __FUNCTION__;

	if ($keys) {
		if (is_object($items) && property_exists($items, $key)) # property
			return $function($items->$key, $keys, $default, $unset, $debug);
		elseif (is_object($items) && method_exists($items, $key)) # method
			return $function($items->$key(), $keys, $default, $unset, $debug);
		elseif (is_array($items) && array_key_exists($key, $items)) # array item
			return $function($items[$key], $keys, $default, $unset, $debug);
		else
			return $default;
	}
	else {
		if (is_object($items) && property_exists($items, $key)) { # property
			$default = $items->$key;

			if ($unset) unset($items->$key);
		}
		elseif (is_object($items) && method_exists($items, 'getAttribute')) { # eloquent | not universal use
			$default = $items->getAttribute($key) ?? $default;
		}
		elseif (is_object($items) && method_exists($items, $key)) # method
			$default = $items->$key();
		elseif (is_array($items) && array_key_exists($key, $items)) { # array item
			$default = $items[$key];

			if ($unset) unset($items[$key]);
		}
		else
			return $default;
	}

	return $default;
}
function findAndUnset(&$items, $keys, $default=null)
{
	if (!is_array($keys)) $keys = explode('.', $keys);

	$key = array_shift($keys);

	$function = __FUNCTION__;

	if ($keys) {
		if (is_object($items) && isset($items->$key)) # property
			return $function($items->$key, $keys);
		elseif (is_object($items) && method_exists($items, $key)) # method
			return $function($items->$key(), $keys, $default);
		elseif (is_array($items) && isset($items[$key])) # array item
			return $function($items[$key], $keys);
		else
			return false;
	}
	else {
		if (is_object($items) && isset($items->$key)) { # property
			$default = $items[$key];
			unset($items->$key);
		}
		elseif (is_object($items) && method_exists($items, $key)) # method | unset n/a
			return $items->$key();
		elseif (is_array($items) && isset($items[$key])) { # array item
			$default = $items[$key];
			unset($items[$key]); 
		}
		else
			return $default;
	}

	return $default;
}
function isList($array) {
    return array_keys($array) === range(0, count($array) - 1);
}

function isAssoc($array) {
    return count(array_filter(array_keys($array), 'is_string')) == count($array);
}
function arrayType($array)
{
	if (isList($array))
		return ARRAY_TYPE_LIST;
	elseif (isAssoc($array))
		return ARRAY_TYPE_ASSOC;
	else
		return ARRAY_TYPE_UNKNOWN;
}
/*
	converts value to array if not
 */
function _array($var, $ignore_is_array=false) {
	return $var ? ( is_array($var) && !$ignore_is_array ? $var : [$var] ) : $var;
}
function _array_merge(...$args)
{
	$args = array_filter($args);

	return array_merge(...$args);
}