<?php
 
 namespace SrvKit\Auth\Traits;

use CodeIgniter\Cookie\Cookie;
use Exception;
use SrvKit\Auth\Config\Auth;
use SrvKit\Auth\Models\UserTokenModel;

 trait RefreshTokenTrait {
 	protected function generateRefreshToken():string
 	{
 		return bin2hex(random_bytes(64));
 	}

 	/**
 	 * Saves the generated token
 	 * @param  int    $userId          [description]
 	 * @param  string $rawRefreshToken [description]
 	 * @return [type]                  [description]
 	 */
 	protected function saveRefreshToken(?int $userId, string $rawRefreshToken): bool
 	{
 		$token = [
 			'user_id' => $userId,
 			'refresh_token_hash' => password_hash($rawRefreshToken, PASSWORD_ARGON2I),
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

 	protected function setRefreshTokenCookie(string $rawtoken):void
 	{
 		$response = service('response');
 		$cookie = new Cookie('__srvkit_refreshtoken__', $rawtoken, [
 			"expire" => $this->config::REFRESH_TOKEN_EXP,
		   	"httponly" => true,
		   	"secure" => false,
		   	"samesite" => 'Strict',
		   	"path"=> '/'
 		]);
 		$response->setcookie($cookie);
 	}

 	protected function clearRefreshTokenCookie(): void
    {
        setcookie('__srvkit_refreshtoken__', '', time() - 3600, '/', '', true, true);
    }
 }
