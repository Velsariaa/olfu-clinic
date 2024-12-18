<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function json_response($status, ...$args)
{
    $ci =& get_instance();

    $data = array_merge(['status' => $status], ...$args);

    $ci->output
    ->set_status_header(200)
    ->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
    ->_display();

    exit;
}
function dmp($data)
{
    $ci =& get_instance();

    $ci->output
    ->set_status_header(200)
    ->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
    ->_display();

    exit;
}
function dmp2($data, $is_dmp=false) 
{
    if ($is_dmp) 
    {
        dmp($data);
    }
}
function txt($text)
{
    header('Content-Type: text/plain');
    echo $text;
    exit;
}