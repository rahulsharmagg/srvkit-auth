<?php
namespace SrvKit\Auth;

use Exception;
use SrvKit\Auth\Authentication\Authentication;
use SrvKit\Auth\Authentication\AuthenticatorInterface;
use SrvKit\Auth\Authentication\AuthenticatorResult;
use SrvKit\Auth\Config\Auth as AuthConfig;
use SrvKit\Auth\Models\UserModel;

/**
 * @method void login
 * @method void logout
 * @method bool loggedIn
 * @method AuthenticatorResult attempt
 */

class Auth{

	public const VERSION = \SrvKit\Auth\Config\Version::VERSION;

	protected ?UserModel $userProvider = null;

	public AuthenticatorInterface $authenticator;
	
	public function __construct(protected AuthConfig $config)
	{

	}

	public function setAuthenticator(string $authenticator = null): self
	{

		$authentication = new Authentication($this->config);

		if ($this->userProvider === null) {
			$className = $this->config->userProvider;
			$this->userProvider = new $className();
		}

		$authentication->setProvider($this->userProvider);
		$this->authenticator = $authentication->getAuthenticator($authenticator);

		return $this;
	}


	public function authenticate(?array $credentials)
	{
		return $this->authenticator->attempt($credentials);
	}

	/**
	 * Gets current logged in user
	 * @return [type] [description]
	 */
	public function user()
	{
		if($this->authenticator !== null){
			return $this->authenticator->getUser();
		}
		return null;
	}

	public function __call($name, $arguments)
	{
		if($this->authenticator && method_exists($this->authenticator, $name)){
			return $this->authenticator->{$name}(...$arguments);
		}
	}
}