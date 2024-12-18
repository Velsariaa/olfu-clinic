<?php
# application/middleware/TestMiddleware.php

class UserMiddleware implements Luthier\MiddlewareInterface
{
    public function run($args)
    {
        $user = AdminModel::find(loginId());

        if (!$user) route_redirect('logout');

        if (!in_array($user->user_type, [USER_TYPE_USER])) route_redirect('home');   
    }
}