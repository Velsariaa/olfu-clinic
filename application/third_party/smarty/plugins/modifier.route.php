<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function smarty_modifier_route(string $name, $param=null)
{
    if (!route_exists($name)) return '';

    return route($name, $param);
}