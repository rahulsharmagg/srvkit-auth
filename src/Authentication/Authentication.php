<?php

namespace SrvKit\Auth\Authentication;

use SrvKit\Auth\Authentication\DefaultAuthenticator;
use SrvKit\Auth\Config\Auth;
use SrvKit\Auth\Models\UserModel;

class Authentication{
	protected ?UserModel $userProvider = null;
	protected ?TokenProvider $tokenProvider = null;
	public function __construct(protected Auth $config)
	{
	}

	public function getAuthenticator(): AuthenticatorInterface
	{
		$authenticator = new DefaultAuthenticator($this->userProvider);
		return $authenticator;
	}

	public function setProvider(?UserModel $userModel): self
	{
		$this->userProvider = $userModel;
		return $this;
	}
}