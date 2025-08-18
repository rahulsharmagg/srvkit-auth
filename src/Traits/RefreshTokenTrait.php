<?php
 
namespace SrvKit\Auth\Traits;

use Exception;

use CodeIgniter\Cookie\Cookie;
use CodeIgniter\HTTP\IncomingRequest;
use SrvKit\Auth\Config\Auth;
use SrvKit\Auth\Entities\Token;
use SrvKit\Auth\Models\UserTokenModel;

 trait RefreshTokenTrait {

 	/**
 	 * Refresh token
 	 */
 	protected Token $_rftoken;

 	/**
 	 * Get the refresh token from database
 	 * @return ?Token
 	 */
 	public function getRefreshToken(): ?Token
 	{
 		if(property_exists($this, '_rftoken') && isset($this->_rftoken)){
 			return $this->_rftoken;
 		}
 		return null;
 	}

 	/**
 	 * Generates a random token of 64 bytes
 	 * @return string
 	 */
 	public function generateRefreshToken():string
 	{
 		return bin2hex(random_bytes(64));
 	}

 	/**
 	 * Saves the generated token
 	 * @param  int    $userId          
 	 * @param  string $rawRefreshToken 
 	 * @return bool                  
 	 * @throws Exception
 	 */
 	public function saveRefreshToken(?int $userId, string $rawRefreshToken): bool
 	{	
 		/** @var Auth*/
 		$config = config('Auth');
 		$token = [
 			'user_id' => $userId,
 			'refresh_token_hash' => hash('sha256', $rawRefreshToken),
			'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
			'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
			'logged_in_at' => date('Y-m-d H:i:s'),
			'expires_at' => date('Y-m-d H:i:s', $config::REFRESH_TOKEN_EXP),
			'created_at' => date('Y-m-d H:i:s'),
 		];
 		$userTokenModel = new UserTokenModel();
 		if(!$userTokenModel->save($token)){
 			[0 => $error] = $userTokenModel->errors();
 			throw new Exception($error);
 		}

 		// Get the first value 
 		$this->_rftoken = $userTokenModel->find($userTokenModel->getInsertID());

 		return true;
 	}

 	/**
 	 * Verifies the refresh token
 	 * @return Token
 	 * @throws Exception
 	 */
 	public function verifyRefreshToken(): Token
 	{
 		/** @var IncomingRequest */
 		$request = service('request');
 		$refreshToken = $request->getCookie('__srvkit_refreshtoken__');
 		if(!$refreshToken) throw new Exception('Refresh token invalid or not found.',);

 		$userTokenModel = new UserTokenModel();

 		/** @var Token */
 		$token = $userTokenModel->where('refresh_token_hash', hash('sha256', $refreshToken))->first();
 		if(!$token) throw new Exception('Refresh token invalid or not found.');

 		if($token->isExpired()) {
 			throw new Exception('Refresh token is expired.');
 		}

 		return $token;
 	}

 	public function getRefreshTokenAsCookie(string $rawtoken): Cookie
 	{
 		$cookie = (new Cookie('__srvkit_refreshtoken__', $rawtoken))
 		    ->withExpires(time() + config('Auth')::REFRESH_TOKEN_EXP)
 		    ->withPath('/') 
 		    ->withHTTPOnly(true)
 		    ->withSecure(request()->isSecure())
 		    ->withSameSite('Lax');
 
 		return $cookie;
 	}

 	public function clearRefreshTokenCookie(): void
    {
        setcookie('__srvkit_refreshtoken__', '', time() - 3600, '/', '', true, true);
    }
 }
