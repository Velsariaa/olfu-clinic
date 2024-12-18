<?php
# application/middleware/TestMiddleware.php

class AdminMiddleware implements Luthier\MiddlewareInterface
{
    public function run($args)
    {
        $user = AdminModel::find(loginId());

        if (!$user) route_redirect('logout');

        if (!in_array($user->user_type, [USER_TYPE_SUPER_ADMIN, USER_TYPE_ADMIN])) route_redirect('home');
    }
}