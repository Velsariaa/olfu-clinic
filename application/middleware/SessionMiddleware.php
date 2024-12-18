<?php
# application/middleware/TestMiddleware.php

class SessionMiddleware implements Luthier\MiddlewareInterface
{
    public function run($args)
    {
        $ci =& get_instance();

        $ci->load->database();

        $ci->load->library('Session');

        $oneWeekAgo = (new DateTime())->modify('-1 day')->format('Y-m-d H:i:s');

        SessionModel::where('created_at', '<', $oneWeekAgo)
        ->delete();
    }
}