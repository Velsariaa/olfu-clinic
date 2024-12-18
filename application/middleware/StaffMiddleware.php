<?php
# application/middleware/TestMiddleware.php

class StaffMiddleware implements Luthier\MiddlewareInterface
{
    public function run($args)
    {
        $user = AdminModel::find(loginId());

        if (!$user) route_redirect('logout');

        if (!in_array($user->user_type, [USER_TYPE_SUPER_ADMIN, USER_TYPE_ADMIN, USER_TYPE_NURSE, USER_TYPE_DOCTOR])) route_redirect('home');
    }
}