<?php

namespace SrvKit\Auth\Controllers;
use SrvKit\Auth\Auth;
use SrvKit\Auth\Authenticators\DefaultAuthenticator;
use SrvKit\Auth\Helpers\AvatarHelper;

class AuthController extends BaseController
{   
    public function test()
    {
    	$this->response->setContentType('image/png');
    	return AvatarHelper::generate('Test');
    }

    public function auth()
    {
        $this->session->set('__m', '45');
        $config = config('Auth');
        $auth = new Auth($config);
        $result = $auth->setAuthenticator()->authenticator->attempt(['username' => 'rahul1274', 'password' => 'rahul@123']);
        // d($auth->user());
        if($result->isOK()){
            $auth->authenticator->login($auth->user());
        }

        d($auth->loggedIn());
        // $auth->logout();
        d($auth->loggedIn());
    }
}
