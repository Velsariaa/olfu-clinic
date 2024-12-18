<?php
# application/middleware/TestMiddleware.php

class DbMiddleware implements Luthier\MiddlewareInterface
{
    public function run($args)
    {
        $ci =& get_instance();

        $ci->load->database();
    }
}