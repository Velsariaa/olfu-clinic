<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function smarty_modifier_iif($true_value, $condition, $false_value='')
{   
    return $condition ? $true_value : $false_value;
}