<?php
namespace SrvKit\Auth\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Cookie\Cookie;
use SrvKit\Auth\Config\Services;
use SrvKit\Auth\Exceptions\AuthException;

use SrvKit\Auth\Controllers\BaseController;

class LoginController extends BaseController
{
	use ResponseTrait;
	public function index(){
		return view('SrvKit\Auth\Views\auth', ['path' => $this->request->getPath()]);
	}

	public function login(){
		try {
			$auth = Services::auth();
			$username = $this->request->getPost('username');
			$password = $this->request->getPost('password');
			$access = $auth->login($username, $password)->obtainAccessFromLogin();
			
			return $this->respond([
				'message' => 'Login Successful',
				'status' => 'success',
				'access' => $access
			]);
		} catch (AuthException $e) {
			if($this->request->header('content-type') == 'application/json'){
				return	$this->failUnauthorized($e->getMessage());
			}
			$this->session->setFlashdata('message', 'error:'.$e->getMessage());
			return redirect()->back()->withInput();
		}
	}
}
