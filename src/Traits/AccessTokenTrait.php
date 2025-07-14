<?php
 
 namespace SrvKit\Auth\Traits;

use Exception;
use Firebase\JWT\JWT;
use SrvKit\Auth\Config\Auth as AuthConfig;
use SrvKit\Auth\Entities\User;

trait AccessTokenTrait {

	/** @var User $user  */
	public function generateAccessToken(array $payload): string
	{
	    $issuedAt = time();
	    $expiration = $issuedAt + AuthConfig::ACCESS_TOKEN_EXP;

	    $data = [...$payload];

	    $payload = [
	        'iss' => base_url(),
	        'iat' => $issuedAt,
	        'exp' => $expiration,
	        'data' => $data,
	    ];

	    return JWT::encode($payload, AuthConfig::ACCESS_TOKEN_KEY, 'HS256');
	}
}
