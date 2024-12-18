<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function smarty_modifier_ci_config_item($key)
{   
    $ci = get_instance();

    return $ci->config->item($key);
}