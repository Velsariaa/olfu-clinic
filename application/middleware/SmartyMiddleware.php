<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Luthier\Utils;
use Luthier\RouteBuilder;

class SmartyMiddleware implements Luthier\MiddlewareInterface
{
    public function run($args)
    {
        $ci =& get_instance();

        $ci->load->library(['Smartyci' => 'sm']);

        $ci->sm->assign('assets_url', $ci->config->item('assets_url'));

        if (isLoggedIn())
        {
            $user = AdminModel::find(loginId());

            if (!$user)
            {
                route_redirect('logout');
            }

            $ci->sm->assign('user', $user);

            $ci->sm->assign('is_logged_in', true);
        }
        
        $ci->sm->assign('sidebar_visible', isLoggedin());

        $ci->sm->assign('navbar_visible', isLoggedin());

        $ci->sm->assign('current_url_name', RouteBuilder::getByUrl(Utils::currentUrl())->getName());

        #

        $admin = AdminModel::find(loginId());

        if (!$admin) return;

        if (!in_array($admin->user_type, [USER_TYPE_USER])) return;

        $route_name = RouteBuilder::getByUrl(Utils::currentUrl())->getName();

        if ($route_name == 'profile') return;

        $student = UserModel::where('admin_id', $admin->id)->first();

        if (!$student) return;

        if (
            empty($student->first_name) ||
            empty($student->middle_name) ||
            empty($student->last_name) ||
            empty($student->contact) ||
            empty($student->email) ||
            empty($student->student_no) ||
            empty($student->address) ||
            empty($student->year_level) ||
            empty($student->birth_date) ||
            empty($student->gender) ||
            empty($student->civil_status) ||
            empty($student->religion) ||
            empty($student->nationality) ||
            empty($student->mother_name) ||
            empty($student->mother_occupation) ||
            empty($student->mother_contact) ||
            empty($student->father_name) ||
            empty($student->father_occupation) ||
            empty($student->father_contact) ||
            empty($student->emergency_contact_name) ||
            empty($student->emergency_contact) ||
            empty($student->hospital_choice)
        ) {
            $ci->sm->assign('complete_user_information', route('profile'));
        }
    }
}