<?php
namespace SrvKit\Auth\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Cookie\Cookie;
use SrvKit\Auth\Exceptions\AuthException;

use SrvKit\Auth\Controllers\BaseController;
use SrvKit\Auth\Models\UserModel;

class SignupController extends BaseController
{
	use ResponseTrait;

	public function index(int|bool $step = false){
		if($step == 2) {
			$cookie = $this->request->getCookie('__srvkit-s1__'); // check step 1 is still in progress or not
			if(!$cookie) {
				return redirect()->to('/auth/signup/1'); // if cookie not set then redirect back to step 1
			}
		}

		return view('SrvKit\Auth\Views\auth', ['path' => $this->request->getPath()]);
	}

	/**
	 * [isValidStep description]
	 * @param  int     $step [description]
	 * @return boolean       [description]
	 * @throws AuthException
	 */
	private function isValidStep(int $step):bool
	{
		$validationRule = [
			'name' => [
				'label' => 'Name',
				'rules' => 'required|min_length[2]|max_length[50]|regex_match[/^[A-Za-z\s\.\'-]+$/]',
			],
			'username' => [
				'label' => 'Username',
				'rules' => 'required|regex_match[/^[A-Za-z0-9\_]+$/]|min_length[4]|max_length[20]|is_unique[users.username,id,{id}]',
			],
			'password' => [
				'label' => 'Password',
				'rules' => 'required|min_length[6]',
			],
		];
		if($step == 2) {
			$validationRule = [
				'email' => [
					'label' => 'Email',
					'rules' => 'required|valid_email'
				],
				'role' => [
				    'label' => 'Role',
				    'rules' => 'permit_empty|in_list[member,author,admin,owner]',
				]
			];
		}

		if(SignupController::validate($validationRule)){
			return true;

		} else {
			$errors = array_values($this->validator->getErrors());
			throw new AuthException($errors[0]);
		}

		return false;
	}

	public function signup(int|bool $step = false){
		try {
			if($step == 1 && $this->isValidStep(1)){
				$name = $this->request->getPost('name');
				$username = $this->request->getPost('username');
				$password = $this->request->getPost('password');
				$cookie = new Cookie('__srvkit-s1__', json_encode([
				    'name' => $name,
				    'username' => $username,
				    'password' => $password
				]), [
				   "expire" => 3600,
				   "httponly" => true,
				   "secure" => false,
				   "samesite" => 'Strict',
				   "path"=> '/'
				]);
				$this->response->setCookie($cookie);
				$this->session->setFlashdata('message', 'info:User not created yet!, email & role is needed to proceed next.');
				return $this->response->redirect('/auth/signup/2');
			}

			// Proceed to the form,
			$step1 = json_decode($this->request->getCookie('__srvkit-s1__'), true);
			if(!$step1) throw new AuthException('Invalid form submission.');
			$this->isValidStep(2);

			$userModel = new UserModel();
			$row = [
				'name' => $step1['name'],
				'username' => $step1['username'],
				'password' => $step1['password'],
				'email' => $this->request->getPost('email'),
				'role' => 'member'
			];

			$role = $this->request->getPost('role');

			if(in_array($role, ['author', 'admin', 'owner'])){
				$row['requested_role'] = $this->request->getPost('role');
			}

			if(!$userModel->save($row)){
				$errors = $userModel->errors();
				[0 => $error] = array_values($errors);
				throw new AuthException($error);
			}

			$this->response->setcookie('__srvkit-s1__', '', time() - 1000, '', '/', '', false, true, 'Strict');
			// dd(json_decode($this->request->getCookie('__srvkit-s1__'), true));
			return $this->response->setJSON([
				'message' => 'New user created successfully.',
				'status' => 'success',
				'insert_id' => $userModel->getInsertID()
			]);
		
		} catch (AuthException $e) {
			if($this->request->header('content-type') == 'application/json'){
				return	$this->failUnauthorized($e->getMessage());
			}
			$this->session->setFlashdata('message', 'error:'.$e->getMessage());
			return redirect()->to('auth/signup/'.$step, 302)->withInput();
		}
	}
}
