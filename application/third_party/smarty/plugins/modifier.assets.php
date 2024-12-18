<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function smarty_modifier_assets($asset)
{   
    $ci = get_instance();

    return str_replace('@assets', $ci->config->item('assets_url'), $asset);
}