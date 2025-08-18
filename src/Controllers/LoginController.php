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

class LoginController extends BaseController
{
	use ResponseTrait;
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
			$username = $this->request->getPost('username');
			$password = $this->request->getPost('password');
			$access = $auth->login($username, $password)->obtainAccessFromLogin();
			
			$this->session->set('__srvkit_accesstoken__', $access);
			return redirect()->route('dashboard', [$username], 301)->with('message', 'success:Login Successful')->withCookies();
		} catch (AuthException $e) {
			if($this->request->header('content-type') == 'application/json'){
				return	$this->failUnauthorized($e->getMessage());
			}
			$this->session->setFlashdata('message', 'error:'.$e->getMessage());
			return redirect()->back()->withInput();
		}
	}

	/**
	 * Handles login from xhr, fetch, axios etc.
	 * @return [type] [description]
	 */
	public function asyncLogin(): ResponseInterface
	{
		try {
			$auth = Services::auth();

			if($this->validate('login')){
				['username' => $username, 'password' => $password] = $this->validator->getValidated();
			} else {
				$errors = array_values($this->validator->getErrors());
				throw new AuthException($errors[0], 'E20204');
			}

			// Get access from auth service
			['access' => $access] = $auth->login($username, $password)->obtainAccessFromLogin();

			return $this->respond([
				'message' => 'Login Successful',
				'status' => 'success',
				'access' => $access
			], 200);
		} catch (AuthException $e) {
			return $this->respond([
				"status" => "error",
				"error" => [
					"message" => $e->getMessage(),
					"code" => $e->getErrorCode()
				],
			], 401, 'Authentication Error');
		} catch (Exception $e) {
			return $this->respond([
				"status" => "error",
				"error" => [
					"message" => $e->getMessage(),
					"code" => $e->getCode()
				],
			], 400, 'Unexpected Error');
		}
	}
}
