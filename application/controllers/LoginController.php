<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class LoginController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    public function login()
    {
        gotoHomeIfLoggedIn();

        $this->sm->display('login/login.tpl');
    }
    public function login_check()
    {
        gotoHomeIfLoggedIn();

        $username = post('username') ?? '';

        $user = AdminModel::where('username', DB::raw("BINARY '{$username}'")) 
        ->first();

        $match = verifyPassword(post('password'), $user->password ?? null); 

        if (!$match)
        {
            setFlashdata('error_message', 'Invalid username or password');
            route_redirect('login');
        }

        setUserdata(['__login' => true, '__login_id' => $user->id]);
        
        redirect(base_url());
    }
    public function logout()
    {
        logout();
    }
}
