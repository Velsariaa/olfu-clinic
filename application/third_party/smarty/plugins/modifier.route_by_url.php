<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Luthier\RouteBuilder;

function smarty_modifier_route_by_url(string $url)
{
    try 
    {
        $route = RouteBuilder::getByUrl($url);

        return $route->getName();
    }
    catch(\Exception $e) 
    {
        return false;
    }
}