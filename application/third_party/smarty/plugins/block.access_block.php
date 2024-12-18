<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function smarty_block_access_block($params, $content, &$smarty, &$repeat)
{
    if (!$repeat) 
    {
        if (isset($params['access']) && boolval($params['access']))
        {
            return $content;
        }
    }
}