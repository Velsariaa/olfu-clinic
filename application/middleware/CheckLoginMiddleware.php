<?php

class CheckLoginMiddleware implements Luthier\MiddlewareInterface
{
    public function run($args)
    {
        if (!isLoggedIn()) route_redirect('logout');
    }
}