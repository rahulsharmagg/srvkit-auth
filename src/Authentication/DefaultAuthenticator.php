<?php

namespace SrvKit\Auth\Authentication;

use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Session\Session;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Models\UserModel;
use SrvKit\Auth\Entities\Token;
use SrvKit\Auth\Models\UserTokenModel;

class DefaultAuthenticator implements AuthenticatorInterface
{
	// User states
    private const STATE_UNKNOWN   = 0;
    private const STATE_ANONYMOUS = 1;
    private const STATE_PENDING   = 2;
    private const STATE_LOGGED_IN = 3;

	protected ?User $user = null;
	protected int $userState = self::STATE_UNKNOWN;
	public function __construct(protected UserModel $provider)
	{

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
			return new AuthenticatorResult(['success' => false, 'reason' => 'Invalid credentials', 'extraInfo' => null]);
		}

		if (!password_verify($password, $user->password)){
			return new AuthenticatorResult(['success' => false, 'reason' => 'Invalid Password', 'extraInfo' => null]);
		}

		$this->user = $user;

		return new AuthenticatorResult(['success' => true, 'extraInfo' => $user]);
	}

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function login(User $user):void
	{
		d($user);
		/** @var Session [description] */
		$session = service('srvkitsession');
		$session->set('id', $user->id);
	}

	public function logout():void
	{
		/** @var Session [description] */
		$session = service('srvkitsession');
		$session->remove('id');
		// d('Logout');
	}

	public function loggedIn(): bool
	{
		$this->checkUserState();
		return $this->userState === self::STATE_LOGGED_IN;
	}

	private function checkUserState(): void
	{
		/** @var IncomingRequest $request [description] */
		$request = service('request');
		$refreshToken = $request->getCookie('__srvkit_refreshtoken__');
		if($refreshToken !== null){
			$userTokenModel = new UserTokenModel();

			/** @var Token */
			$token = $userTokenModel->join('users', "ueers.id = {$userTokenModel->table}.user_id")
									->where('refresh_token_hash', hash('sha256', $refreshToken))
									->first();
			d($token);
			$this->userState = self::STATE_LOGGED_IN;
		}

		return;
	}
}