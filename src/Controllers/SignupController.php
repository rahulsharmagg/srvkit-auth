<?php
namespace SrvKit\Auth\Controllers;

use Exception;
use CodeIgniter\Cookie\Cookie;
use CodeIgniter\API\ResponseTrait as APIResponseTrait;

use SrvKit\Auth\Models\UserModel;
use SrvKit\Auth\Exceptions\ErrorCodes;
use SrvKit\Auth\Exceptions\AuthException;
use SrvKit\Auth\Controllers\BaseController;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Traits\ResponseTrait as SrvKitResponseTrait;

class SignupController extends BaseController
{
	use APIResponseTrait;
	use SrvKitResponseTrait;

	public function index(int|bool $step = false){
		$cookie = null;
		if($step == 2) {
			$cookie = $this->request->getCookie('__srvkit-s1__'); // check step 1 is still in progress or not
			if(!$cookie) {
				return redirect()->to('/auth/signup/1'); // if cookie not set then redirect back to step 1
			}
		}

		return view('SrvKit\Auth\Views\auth', ['path' => $this->request->getPath()]);
	}

	/**
	 * Handle Signup Request
	 * @return [type] [description]
	 */
	public function signup(){
		try {
			$contentType = $this->request->getHeaderLine('Content-Type');
			$allowedContentType = ["application/x-www-form-urlencoded", "application/json"];

			if(!in_array($contentType, $allowedContentType)){
				$_ = implode(", ", $allowedContentType);
				throw new Exception("Only $_ is suppored for this endpoint", ErrorCodes::E3002_CONTENT_NOT_ALLOWED);
			}

			/* Forward the signup process to _signup */
			$step = $this->request->getGet('step');
			if(!empty($step)){
				if(in_array($step, [1, 2])){
					$this->_signup($step);
				}
			}


			$payload = $this->request->getPost();
			if($contentType === "application/json"){
				$payload = $this->request->getJSON(true);
			}

			$result = $this->create($payload);
			
			// Autologin
			if(!$result) throw new Exception("Signup Unsuccessful", ErrorCodes::E9999_UNKNOWN_ERROR);
			
			if($this->request->isAJAX()){
				return $this->autoRespond([
					'type' => 'success',
					'message' => 'User created successfully',
					'user' => $result
				]);
			}

		} catch (Exception $e) {
			if($this->request->isAJAX()){
				return $this->autoRespond(["error" => ["message" => $e->getMessage(), "code" => $e->getCode()], "type" => "error"], 400);
			}

			$this->session->setFlashdata('message', 'error:'.$e->getMessage());
			return redirect()->back()->withInput();
		}
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
				'rules' => 'required|valid_username|is_unique[users.username,id,{id}]',
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

	/**
	 * Lagacy signup method for in view
	 * @param  bool|boolean $step
	 * @return ResponseInterface          
	 */
	public function _signup(int|bool $step = false){
		try {
			if($step == 1 && $this->isValidStep(1)){
				$name = $this->request->getPost('name');
				$username = $this->request->getPost('username');
				$password = $this->request->getPost('password');
				$cookie = (new Cookie('__srvkit-s1__', json_encode([
				    'name'     => $name,
				    'username' => $username,
				    'password' => $password,
				])))
				    ->withExpires(time() + 600)
				    ->withPath('/') 
				    ->withHTTPOnly(true)
				    ->withSecure(request()->isSecure())
				    ->withSameSite('Lax');

				$this->session->setFlashdata('message', 'info:User not created yet!, email & role is needed to proceed next.');
				$this->response->setCookie($cookie);
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
			return redirect()->to('/auth/signup/'.$step, 302)->withInput();
		}
	}

	public function cancel(){
		$username = $this->request->getPost('username');
		$cookie = $this->request->getCookie('__srvkit-s1__');
		if($username && $cookie){
			$cookie = json_decode($cookie);
			if($cookie->username == $username){
				$cookie = (new Cookie('__srvkit-s1__', ''))
				    ->withExpires(time() - 3600)
				    ->withPath('/') 
				    ->withHTTPOnly(true)
				    ->withSecure(request()->isSecure())
				    ->withSameSite('Lax');

				$this->session->setFlashdata('message', 'info:Signup Cancelled!');
				return $this->response->setCookie($cookie)->redirect(base_url('auth/signup/1'));
				
			}
		}
		return redirect()->back();
	}

	/**
	 * Creates new user
	 * @param  array  $payload
	 * @return null|array
	 * @throws Exception
	 */
	public function create(array $payload): ?array
	{
		if($this->validateData($payload, 'signup')){
			$data = $this->validator->getValidated();

			$userModel = new UserModel();
			
			$row = [
				'name' => $data['name'],
				'username' => $data['username'],
				'password' => $data['password'],
				'email' => $data['email'],
				'role' => 'member',
			];

			// Request the role if not member
			$role = isset($data['role']) ?? 'member';
			if(in_array($role, ['author', 'admin', 'owner'])){
				$row['requested_role'] = $role;
			}

			if(!$userModel->save($row)){
				$errors = $userModel->errors();
				[0 => $error] = array_values($errors);
				log_message('errors', $error, $errors);
				throw new Exception($error, ErrorCodes::E7001_DATABASE_SAVE_FAILED);
			}


			// Saved Successfully
			
			/** @var User */
			$user = $userModel->find($userModel->getInsertID());
			return $user->sanitized;
		}

		$errors = array_values($this->validator->getErrors());
		throw new Exception($errors[0], ErrorCodes::E2001_VALIDATION_FAILED);
	}

	public function checkUserName()
	{
		$message = "";
		$data = $this->request->getJSON();
		$username = $data->username;
		$rule = ['username' => 'valid_username'];
		if($this->validate($rule)){
			$userModel = new UserModel();
			$usernames = $userModel->asArray()->select('username')->where('username', $username)->findAll();
			$isAvailable = count($usernames) > 0;
			$message = match ($isAvailable) {
				true => 'Username \''.$username.'\' has been taken',
				false => 'Username is available',
			};
			return $this->respond(['available' => !$isAvailable, 'message' => $message, 'type' => 'success', 'payload' => ['username' => $username]]);
		} else {
			// invalid username checking
			return $this->respond(['error' => ['message' => $this->validator->getError('username')], 'type' => 'error'], 400);
		}
	}
}
