<?php
# application/middleware/TestMiddleware.php

class SuperAdminMiddleware implements Luthier\MiddlewareInterface
{
    public function run($args)
    {
        $user = AdminModel::find(loginId());

        if (!$user) route_redirect('logout');

        if ($user->user_type != USER_TYPE_SUPER_ADMIN) route_redirect('home');
    }
}