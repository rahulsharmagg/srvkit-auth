<?php
namespace SrvKit\Auth\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Cookie\Cookie;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use SrvKit\Auth\Config\Services;
use SrvKit\Auth\Exceptions\AuthException;

use SrvKit\Auth\Controllers\BaseController;
use SrvKit\Auth\Exceptions\ErrorCodes;
use SrvKit\Auth\Traits\ResponseTrait as SrvKitResponseTrait;

class LoginController extends BaseController
{
	use ResponseTrait;
	use SrvKitResponseTrait;
	public function index(){
		return view($this->config->views['login'], ['path' => $this->request->getPath()]);
	}

	/**
	 * Handles login in form only
	 * @return ResponseInterface|RedirectResponse [description]
	 */
	public function login():ResponseInterface|RedirectResponse
	{
		try {
			$auth = Services::auth();

			$contentType = $this->request->getHeaderLine('Content-Type');
			$allowedContentType = ["application/x-www-form-urlencoded", "application/json"];

			if(!in_array($contentType, $allowedContentType)){
				$_ = implode(", ", $allowedContentType);
				throw new Exception("Only $_ is suppored for this endpoint", ErrorCodes::E3002_CONTENT_NOT_ALLOWED);
			}

			$payload = $this->request->getPost();

			if($contentType === "application/json"){
				$payload = $this->request->getJSON(true);
			}

			if($this->validateData($payload, 'login')){
				$credentials = $this->validator->getValidated();

				// Guess if username value is email or username
				if(filter_var($credentials['username'], FILTER_VALIDATE_EMAIL)){
					$credentials['email'] = $credentials['username'];
					unset($credentials['username']);
				}

				$result = $auth->setAuthenticator()->attempt($credentials);
				if($result->isOK()){
					$user = $result->extraInfo();
					$error = null;

					$auth->authenticator->login($user, $error, $access, $cookie);
					if($error) throw $error;


					if($this->request->isAJAX()){
						return $this->autoRespond(["type" => "success", "message" => "Login Successful", "access" => $access, "user" => $user->sanitized]);
					}

					$this->response->setCookie($cookie);
					$this->session->set('__srvkit_accesstoken__', $access['access_token']);
					return redirect()->route('/', [], 301)->with('message', 'success:Login Successful')->withCookies();
				}

				throw new AuthException($result->reason(), ErrorCodes::E1001_AUTH_INVALID_CREDENTIALS);
			}

			$errors = array_values($this->validator->getErrors());
			throw new Exception($errors[0], ErrorCodes::E2001_VALIDATION_FAILED);
		} catch (AuthException $a) {
			if($this->request->isAJAX()){
				return $this->autoRespond(["error" => ["message" => $a->getMessage(), "code" => $a->getCode()], "type" => "error"], 401);
			}

			$this->session->setFlashdata('message', 'error:'.$a->getMessage());
			return redirect()->back()->withInput();
		} catch(Exception $e) {
			if($this->request->isAJAX()){
				return $this->autoRespond(["error" => ["message" => $e->getMessage(), "code" => $e->getCode()], "type" => "error"], 400);
			}

			$this->session->setFlashdata('message', 'error:'.$e->getMessage());
			return redirect()->back()->withInput();
		}
	}

}
