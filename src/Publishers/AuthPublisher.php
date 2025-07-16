<?php

namespace SrvKit\Auth\Publishers;

use CodeIgniter\Publisher\Publisher;

class AuthPublisher extends Publisher
{
    protected $source = VENDORPATH .'/srvkit/auth/public';
    protected $destination = FCPATH;
    protected $directories = ['fonts', 'images'];
}
