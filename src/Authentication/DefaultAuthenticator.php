<?php

namespace SrvKit\Auth\Authentication;

use CodeIgniter\Cookie\Cookie;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Session\Session;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Models\UserModel;
use SrvKit\Auth\Entities\Token;
use SrvKit\Auth\Exceptions\AuthException;
use SrvKit\Auth\Models\UserTokenModel;
use SrvKit\Auth\Utils\Mailer\Email;
use Throwable;

class DefaultAuthenticator implements AuthenticatorInterface
{
	// User states
    private const STATE_UNKNOWN   = 0;
    private const STATE_ANONYMOUS = 1;
    private const STATE_PENDING   = 2;
    private const STATE_LOGGED_IN = 3;

	protected ?User $user = null;
	protected int $userState = self::STATE_UNKNOWN;
	protected TokenProvider $tokenProvider;
	protected EmailProvider $emailProvider;

	public function __construct(protected UserModel $provider)
	{
		$this->tokenProvider = new TokenProvider();
		$this->emailProvider = new EmailProvider();
	}

	public function attempt(array $credentials): AuthenticatorResult
	{
		if(empty($credentials['password']) || count($credentials) < 2){
			return new AuthenticatorResult(['success' => false, 'reason' => 'Bad credentials']);
		}

		$password = $credentials['password'];

		unset($credentials['password']);
		$user = $this->provider->where($credentials)->first();

		if($user === null){
			return new AuthenticatorResult(['success' => false, 'reason' => 'Invalid username or password.', 'extraInfo' => null]);
		}

		if (!password_verify($password, $user->password)){
			return new AuthenticatorResult(['success' => false, 'reason' => 'Invalid username or password.', 'extraInfo' => null]);
		}

		$this->user = $user;

		return new AuthenticatorResult(['success' => true, 'extraInfo' => $user]);
	}

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function login(User $user, &$error, &$access, &$cookie):void
	{		
		$tokenProvider = $this->tokenProvider;
		$refreshToken = $tokenProvider->generateRefreshToken();
		if($tokenProvider->saveRefreshToken($user->id, $refreshToken)){
			$scope = config('Auth')->getRoleScope($user->role);
			$payload = ['role' => $user->role, 'scope' => $scope, 'id' => $user->id];
			$accessToken = $tokenProvider->generateAccessToken($payload);
			$access = [
				'refresh_token' => $refreshToken,
				'access_token' => $accessToken
			];

			// check if first time login or not
			$firstTime = false;

			$cookie = $tokenProvider->getRefreshTokenAsCookie($refreshToken);
			$data = $this->tokenProvider->getRefreshToken();
			if(!$firstTime){
				$this->emailProvider::sendLoginReport($user->email, $data->toArray());
			}

			return;
		}
		$error = new AuthException('Oops! Something went wrong while logging in');
	}

	public function logout():void
	{
		/** @var Session */
		$session = service('srvkitsession');
		$tokenProvider = $this->tokenProvider;
		$session->remove('__srvkit_accesstoken__');
		$tokenProvider->clearRefreshTokenCookie();
	}

	public function loggedIn(): bool
 	{
		$this->checkUserState();
		return $this->userState === self::STATE_LOGGED_IN;
	}

	private function checkUserState(): void
	{
		/** @var IncomingRequest $request */
		$request = service('request');
		$refreshToken = $request->getCookie('__srvkit_refreshtoken__');
		if($refreshToken !== null){
			$userTokenModel = new UserTokenModel();

			/** @var Token */
			$token = $userTokenModel->select($userTokenModel->table.'.*, users.username')->join('users', "users.id = {$userTokenModel->table}.user_id")
									->where('refresh_token_hash', hash('sha256', $refreshToken))
									->first();
			$this->user = $token->user;
			$this->userState = self::STATE_LOGGED_IN;
		}

		return;
	}
}