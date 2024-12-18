<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Illuminate\Database\Capsule\Manager as DB;

class RegisterController extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    public function new()
    {
        gotoHomeIfLoggedIn();

        $this->sm->display('register/register.tpl');
    }
    public function create()
    {
        $this->load->library('form_validation');

		$this->form_validation->set_rules('first_name', 'first name', 'required');

        $this->form_validation->set_rules('last_name', 'last name', 'required');

        $this->form_validation->set_rules('email', 'email', 'required|is_unique[users.email]', [
            'is_unique' => 'Email already exists.'
        ]);

        $this->form_validation->set_rules('contact', 'contact', 'required');

        $this->form_validation->set_rules('username', 'username', 'required|is_unique[admins.username]', [
            'is_unique' => 'User already exists.'
        ]);

        $this->form_validation->set_rules('password', 'password', 'required');

        if ($this->form_validation->run() == FALSE)
		{
            setFlashdata('form_data', $_POST);
            setFlashdata('error_message', implode('</br>', $this->form_validation->error_array()));
            route_redirect('register');
		}

        DB::beginTransaction();

		try
		{
            $user = new UserModel;
            $user->first_name = post('first_name', 'strProperCase');
            $user->last_name = post('last_name', 'strProperCase');
            
            $admin = new AdminModel;
            $admin->first_name = $user->first_name;
            $admin->last_name = $user->last_name;
            $admin->name = $user->name;
            $admin->user_type = $user->user_type;
            $admin->username = post('username', 'trim');
            $admin->password = post('password', 'trim.encryptPassword');
            $admin->save();
            
            $user->email = post('email', 'trim');
            $user->contact = post('contact', 'trim');
            $user->admin_id = $admin->id;
            $user->save();

			DB::commit();

            setFlashdata('success_message', 'Account successfully created');    
            setFlashdata('form_data', $_POST);
            route_redirect('login');
		}
		catch(\Exception $e) 
		{
			DB::rollback();

            setFlashdata('form_data', $_POST);
            setFlashdata('error_message', $e->getMessage());
            route_redirect('register');
		}
    }
}
