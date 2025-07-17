<?php

namespace SrvKit\Auth\Controllers;

use SrvKit\Auth\Helpers\AvatarHelper;

class AuthController extends BaseController
{   
    public function test()
    {
    	$this->response->setContentType('image/png');
    	return AvatarHelper::generate('Test');
    }
}
