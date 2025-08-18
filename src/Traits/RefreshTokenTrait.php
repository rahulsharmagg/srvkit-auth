<?php
 
namespace SrvKit\Auth\Traits;

use Exception;

use CodeIgniter\Cookie\Cookie;
use CodeIgniter\HTTP\IncomingRequest;

use SrvKit\Auth\Entities\Token;
use SrvKit\Auth\Models\UserTokenModel;

 trait RefreshTokenTrait {
 	public function generateRefreshToken():string
 	{
 		return bin2hex(random_bytes(64));
 	}

 	/**
 	 * Saves the generated token
 	 * @param  int    $userId          [description]
 	 * @param  string $rawRefreshToken [description]
 	 * @return [type]                  [description]
 	 */
 	public function saveRefreshToken(?int $userId, string $rawRefreshToken): bool
 	{
 		$token = [
 			'user_id' => $userId,
 			'refresh_token_hash' => hash('sha256', $rawRefreshToken),
			'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
			'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
			'logged_in_at' => date('Y-m-d H:i:s'),
			'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
			'created_at' => date('Y-m-d H:i:s'),
 		];
 		$userTokenModel = new UserTokenModel();
 		if(!$userTokenModel->save($token)){
 			[0 => $error] = $userTokenModel->errors();
 			throw new Exception($error);
 		}

 		return true;
 	}

 	/**
 	 * Verifies the refresh token
 	 * @return Token [description]
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

 	public function setRefreshTokenCookie(string $rawtoken):void
 	{
 		$response = service('response');

 		$cookie = (new Cookie('__srvkit_refreshtoken__', $rawtoken))
 		    ->withExpires(time() + $this->config::REFRESH_TOKEN_EXP)
 		    ->withPath('/') 
 		    ->withHTTPOnly(true)
 		    ->withSecure(request()->isSecure())
 		    ->withSameSite('Lax');
 
 		$response->setcookie($cookie);
 	}

 	public function clearRefreshTokenCookie(): void
    {
        setcookie('__srvkit_refreshtoken__', '', time() - 3600, '/', '', true, true);
    }
 }
