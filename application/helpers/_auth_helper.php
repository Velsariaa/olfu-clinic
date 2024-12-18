<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function encryptPassword($password)
{
    $ci = get_instance();

    return password_hash($password.$ci->config->item('password_salt'), PASSWORD_BCRYPT);
}
function verifyPassword($password, $encrypted_password)
{
    $ci = get_instance();

    return password_verify($password.$ci->config->item('password_salt'), $encrypted_password);
}
function isLoggedIn()
{
    $ci = get_instance();

    if (!$ci->load->is_loaded('session'))
    {
        return false;
    }

    return (int)userdata('__login') === 1;
}
function loginId()
{
    return userdata('__login_id');
}
function gotoHomeIfLoggedIn() # used in LoginController
{
    if (isLoggedIn()) redirect(base_url());
}
function gotoLogin() # used in other controllers
{
    if (!isLoggedIn()) route_redirect('logout');
}
function logout()
{
    $ci =& get_instance();

    $session_id = $ci->session->session_id;

    if ($session_id) 
    {
        SessionModel::where('id', $session_id)
        ->delete();
    }

    #

    $sessions = array_keys($ci->session->all_userdata());

    $sessions = array_filter($sessions, function ($item) 
    {
        return $item != '__ci_last_regenerate';
    });

    $ci->session->unset_userdata($sessions);

    #

    if (session_status() === PHP_SESSION_ACTIVE) 
    {
        $ci->session->sess_destroy();
    }

    route_redirect('login');
}