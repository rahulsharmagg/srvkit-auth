<?php
 
 namespace SrvKit\Auth\Traits;

use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use SrvKit\Auth\Config\Auth as AuthConfig;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Exceptions\AuthException;
use stdClass;

/**
 * @property AuthConfig $config Auth Config Class
 */
trait AccessTokenTrait {

	public function generateAccessToken(array $payload): string
	{
	    $issuedAt = time();
	    $expiration = $issuedAt + $this->config::ACCESS_TOKEN_EXP;

	    $data = [...$payload];

	    $payload = [
	        'iss' => base_url(),
	        'iat' => $issuedAt,
	        'exp' => $expiration,
	        'data' => $data,
	    ];

	    return JWT::encode($payload, AuthConfig::ACCESS_TOKEN_KEY, 'HS256');
	}

	public function verifyAccessToken(string $jwt): ?stdClass
	{	
		try {
			$key = new Key($this->config::ACCESS_TOKEN_KEY, 'HS256');
			$payload = JWT::decode($jwt, $key);
			return $payload;
		} catch (ExpiredException $e) {
			throw new AuthException($e->getMessage(), 'E20401', $e);
		} catch (Exception $e) {
			log_message('error', $e->getMessage());
			throw new AuthException($e->getMessage(), 'E20402', $e);
		}
	}
}
