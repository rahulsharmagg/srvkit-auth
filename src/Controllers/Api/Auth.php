<?php

namespace SrvKit\Auth\Controllers\Api;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use SrvKit\Auth\Config\Services;
use SrvKit\Auth\Controllers\BaseController;
use SrvKit\Auth\Entities\Token;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Exceptions\APIResponseException;
use SrvKit\Auth\Exceptions\AuthException;
use SrvKit\Auth\Exceptions\ErrorCodes;
use SrvKit\Auth\Models\UserModel;
use SrvKit\Auth\Models\UserTokenModel;
use SrvKit\Auth\Utils\Handlers\Response\APIResponse;
use SrvKit\Auth\Utils\Mailer\Email;

class Auth extends BaseController
{
	public function index()
	{
		throw APIResponseException::forForbidden();
	}

	public function noResource(){
		$path = $this->request->getPath();
		throw APIResponseException::forNotFound(path: $path);
	}

	public function login(){
		if(!$this->request->is('post')) throw APIResponseException::forInvalidRequest();

		// Login Process
		$payload = $this->getPayload(true);

		$username = isset($payload['username']) ? $payload['username'] : null;
		$password = isset($payload['password']) ? $payload['password'] : null;
		$remember = isset($payload['remember']) ? filter_var($payload['remember'], FILTER_VALIDATE_BOOLEAN) : null;

		$loginPayload = [
			'username' => $username,
			'password' => $password,
			'remember' => $remember
		];
		
		if($this->validateData($loginPayload, 'login')){
			$credentials = $this->validator->getValidated();

			// Guess if username value is email or username
			if(filter_var($credentials['username'], FILTER_VALIDATE_EMAIL)){
				$credentials['email'] = $credentials['username'];
				unset($credentials['username']);
			}

			$auth = Services::auth();
			$result = $auth->setAuthenticator()->attempt($credentials);

			if(!$result->isOK()) throw APIResponseException::forUnauthorized($result->reason());

			/** @var User */
			$user = $result->extraInfo();
			$error = null;

			$auth->authenticator->login($user, $error, $access, $cookie);
			if($error) throw APIResponseException::forUnexpected($error->getMessage());

			$this->response->setCookie($cookie);

			return APIResponse::success(['user' => $user->sanitized, ...$access], 'Login Successfully');
		}

		throw APIResponseException::forValidationFailed(errors: $this->validator->getErrors());

	}

	public function logout(){

	}

	public function signup(){
		if(!$this->request->is('post')) throw APIResponseException::forInvalidRequest();
		$payload = $this->getPayload(true);

		$name     = isset($payload['name'])     ? $payload['name']     : null;
		$username = isset($payload['username']) ? $payload['username'] : null;
		$email    = isset($payload['email'])    ? $payload['email']    : null;
		$password = isset($payload['password']) ? $payload['password'] : null;
		$role     = isset($payload['role'])     ? $payload['role']     : 'member';

		$signupPayload = [
			'name'     => $name,
			'username' => $username,
			'email'    => $email,
			'password' => $password,
			'role'     => $role
		];

		if($this->validateData($signupPayload, 'signup')){
			$userModel = new UserModel();

			$row = [...$signupPayload];

			// Request the role if not member
			$role = $signupPayload['role'];
			if(in_array($role, ['author', 'admin', 'owner'])){
				$row['role'] = 'member';
				$row['requested_role'] = $role;
			}

			if(!$userModel->save($row)){
				throw APIResponseException::forUnexpected('Something went wrong while creating the new user.');
			}

			// Saved Successfully
			
			/** @var User */
			$user = $userModel->find($userModel->getInsertID());
			Events::trigger('user_registered', $user);
			return APIResponse::success(['user' => $user->sanitized], 'Signup Successful');
		}
		throw APIResponseException::forValidationFailed(errors: $this->validator->getErrors());
	}

	public function getUsernameStatus(){
		$message = "";
		$payload = $this->getPayload(true);
		$rule = ['username' => 'valid_username'];
		if($this->validateData($payload, $rule)){
			$userModel = new UserModel();

			$username = $payload['username'];

			$usernames = $userModel->asArray()->select('username')->where('username', $username)->findAll();
			$isAvailable = count($usernames) > 0;
			$message = match ($isAvailable) {
				true => 'Username \''.$username.'\' has been taken',
				false => 'Username is available',
			};
			return APIResponse::success(['available' => !$isAvailable, 'payload' => ['username' => $username]], $message);
		}

		throw APIResponseException::forValidationFailed(errors: $this->validator->getErrors());
	}

	public function resetPassword()
	{
		if(!$this->request->is('post')) throw APIResponseException::forInvalidRequest();
	    
	    $payload = $this->getPayload(true);

	    $username = isset($payload['username']) ? $payload['username'] : null;
	    $email    = isset($payload['email'])    ? $payload['email']    : null;

	    $credentials = ['username' => $username, 'email' => $email];
	    $rule = [
	    	'username' => 'required|valid_username',
	    	'email' => 'required|valid_email'
	    ];
	    if($this->validateData($credentials, $rule)){
	        $userModel = new UserModel();

	        /** @var ?User */
	        $user = $userModel->where('username', $username)->where('email', $email)->first();

	        if ($user) {
	            $tempLink = Services::tempLink();
	            $tempLink->for($user)->action('reset-password')->create();
	            $tempLink->save();
	            $link = $tempLink->link();
	            // Send email logic here
	            // 
	            $result = Email::sendResetPasswordRequest($user->email, $link);
	            $message = 'Unable to send email\r\n'.$result;
	            if($result){
		            $message = 'An email has been successfully sent to \'' . $user->email . '\', check and follow the password recovery link';
	            }

	            return APIResponse::success([], $message);
	        }

	        throw APIResponseException::forValidationFailed('Incorrect username or email');
    	}

        throw APIResponseException::forValidationFailed(errors: $this->validator->getErrors());
	}

	public function getUserByUsername(?string $username = null){
		
	}

	public function setNewPassword(){

	}

	public function setNewAvatar(){

	}

	public function test()
	{
		
		$userTokenModel = new UserTokenModel();
		/** @var Token [description] */
		[0 => $row] = $userTokenModel->where('user_id', 14)->find();
		// dd($row);
		$data = $row->toRawArray();
		// dd($row);
		Email::sendLoginReport('rahulksharma199r@gmail.com', $data);
		return APIResponse::success(['email' => 'send -success.']);
	}

	/////////////////////
	// PRIVATE METHODS //
	/////////////////////
	
	/**
	 * Checks content type from incoming request
	 * @return bool Content-Type
	 * @throws APIResponseException
	 */
	private function isValidContentType(): bool
	{
	    $contentType = $this->request->getHeaderLine('Content-Type');

	    $allowedContentType = ["application/x-www-form-urlencoded", "application/json"];

	    foreach ($allowedContentType as $type) {
	        if (stripos($contentType, $type) === 0) { // Starts with allowed type
	            return true;
	        }
	    }

	    $_ = implode(", ", $allowedContentType);
	    throw APIResponseException::forInvalidRequest("Only $_ is supported for this endpoint");
	}

	/**
	 * Get the payload from the request body
	 *
	 * Supports application/json and application/x-www-form-urlencoded
	 *
	 * @param  bool $assoc   When TRUE, returned objects will be converted into associative arrays
	 * @param  int  $depth   Maximum depth of the structure being decoded
	 * @param  int  $options Bitmask of JSON decode options
	 * @return mixed
	 * @throws APIResponseException
	 */
	private function getPayload(bool $assoc = false, int $depth = 512, int $options = 0)
	{
	    $contentType = $this->request->getHeaderLine('Content-Type');

	    if ($this->request->getBody() === null && empty($_POST)) {
	        throw APIResponseException::forInvalidRequest(
	            'The request body is empty, cannot process further.'
	        );
	    }

	    // Handle JSON
	    if (stripos($contentType, 'application/json') === 0) {
	        $result = json_decode($this->request->getBody(), $assoc, $depth, $options);

	        if (json_last_error() !== JSON_ERROR_NONE) {
	            throw APIResponseException::forInvalidJSON('Invalid JSON syntax at body.');
	        }

	        return $result;
	    }

	    // Handle x-www-form-urlencoded
	    if (stripos($contentType, 'application/x-www-form-urlencoded') === 0) {
	        return $this->request->getPost();
	    }

	    throw APIResponseException::forInvalidRequest(
	        'Only application/json or application/x-www-form-urlencoded is supported'
	    );
	}

}
