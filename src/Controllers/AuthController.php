<?php

namespace SrvKit\Auth\Controllers;

use CodeIgniter\API\ResponseTrait as APIResponseTrait;
use SrvKit\Auth\Auth;
use SrvKit\Auth\Authenticators\DefaultAuthenticator;
use SrvKit\Auth\Exceptions\APIResponseException;
use SrvKit\Auth\Exceptions\AuthException;
use SrvKit\Auth\Helpers\AvatarHelper;
use SrvKit\Auth\Traits\ResponseTrait;

class AuthController extends BaseController
{   
    use ResponseTrait;
    use APIResponseTrait;
    public function test()
    {
        throw APIResponseException::forInvalidJSON();
    	$this->response->setContentType('image/png');
    	return AvatarHelper::generate('Test');
    }

    public function ping()
    {
        service('toolbar')->respond = false;
        return $this->respond([
            'message' => 'pong',
            'data' => $this->request->getJSON()
        ]);
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
