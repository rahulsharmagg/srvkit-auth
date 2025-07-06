<?php
namespace SrvKit\Auth\Controllers;

use CodeIgniter\API\ResponseTrait;
use SrvKit\Auth\Exceptions\AuthException;

use SrvKit\Auth\Controllers\BaseController;

class LoginController extends BaseController
{
	use ResponseTrait;
	public function index(){
		// return redirect()->to('/auth/signup')->with('name', 'tempsession');
		return view('SrvKit\Auth\Views\auth', ['path' => $this->request->getPath()]);
	}

	public function login(){
		try {
			return null;
		} catch (AuthException $e) {
			if($this->request->header('content-type') == 'application/json'){
				return	$this->failUnauthorized($e->getMessage());
			}
			return $this->response->setStatusCode(401)->redirect('auth/login', 'auto', 302);
		}
	}
}
