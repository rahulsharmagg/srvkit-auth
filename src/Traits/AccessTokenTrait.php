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

	/**
	 * [generateAccessToken description]
	 * @param  array{'role': string, 'scope': string[]}  $payload [description]
	 * @return [type]          [description]
	 */
	public function generateAccessToken(array $payload): string
	{
	    $issuedAt = time();
	    /** @var AuthConfig */
	    $config = config('Auth');
	    $expiration = $issuedAt + $config::ACCESS_TOKEN_EXP;

	    $data = (object) $payload;

	    $payload = [
	        'iss' => base_url(),
	        'iat' => $issuedAt,
	        'exp' => $expiration,
	        'role' => $data->role,
	        'scope' => implode(",", $data->scope)
	    ];

	    return JWT::encode($payload, $config::ACCESS_TOKEN_KEY, 'HS256');
	}

	public function verifyAccessToken(string $jwt): ?stdClass
	{	
		try {
			/** @var AuthConfig */
			$config = config('Auth');
			$key = new Key($config::ACCESS_TOKEN_KEY, 'HS256');
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
