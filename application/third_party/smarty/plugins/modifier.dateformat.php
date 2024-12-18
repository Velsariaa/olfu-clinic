<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function smarty_modifier_dateformat($string, $format='M. d, Y h:i A', $from_format=null)
{
    if ($from_format) {
        $d = DateTime::createFromFormat($from_format, $string);
        return $d ? $d->format($format) : '';
    }
    else {
        $t = strtotime($string);
        return $t ? date($format, $t) : '';
    }
}