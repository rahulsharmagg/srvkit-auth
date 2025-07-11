<?php

namespace SrvKit\Auth\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;
use SrvKit\Auth\Config\Services;
use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Exceptions\PageNotFoundException;
use DateTime;
use SrvKit\Auth\Config\Auth;
use SrvKit\Auth\Exceptions\AuthException;
use SrvKit\Auth\Models\UserModel;
use SrvKit\Auth\Traits\ActionTrait;
use SrvKit\Auth\Traits\RefreshTokenTrait;

class AuthController extends BaseController
{   
    use ActionTrait;
    use RefreshTokenTrait;
    public function index()
    {
        $action = $this->request->getGet('action');
        $for = $this->request->getGet('for');
        $auth = Services::auth();
        
        if($for){
            return $auth->action($for)->view(data: ['action' => 'message-block', 'name' => $for]);
        }

        if ($action) {
            return $auth->action($action)->view();
        }

        throw new PageNotFoundException('Page not found.');
    }

    /*public function verify()
    {
        [0 => $action, 1 => $token] = explode('/', $this->request->getBody());
        $this->session->setFlashdata('token', $token);
        return redirect()->to('auth?action='.$action);
    }*/

    public function action(string $actionName)
    {
        try {
            $allowed = config(Auth::class)->actions;
            if (!in_array($actionName, $allowed, true)) {
                return $this->response->setStatusCode(403)->setBody('Action not allowed');
            }
            $actionMethod = str_replace('-', '', lcfirst(ucwords($actionName, '-')));
            if(!method_exists($this, $actionMethod)){
                return $this->response->setStatusCode(404)->setBody('Action not found');
            }

            return $this->$actionMethod();
        } catch (\Throwable $e) {
            return $this->response
                            ->setStatusCode(500)
                            ->setBody('Error calling action: ' . $e->getMessage());
        }
    }

}
