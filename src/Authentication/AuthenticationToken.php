<?php

namespace SrvKit\Auth\Authentication;

use SrvKit\Auth\Traits\AccessTokenTrait;
use SrvKit\Auth\Traits\RefreshTokenTrait;

class AuthenticationToken
{
	use AccessTokenTrait;
	use RefreshTokenTrait;
	public function __construct()
	{}
}