<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Luthier\RouteBuilder;

function smarty_modifier_route_name(string $name)
{
    if (!route_exists($name)) return '';

    return RouteBuilder::getByName($name)->getName();
}