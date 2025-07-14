<?php

namespace SrvKit\Auth\Services;

use Exception;
use CodeIgniter\Cookie\Cookie;
use CodeIgniter\View\View;
use SrvKit\Auth\Config\Auth as AuthConfig;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Exceptions\AuthException;
use SrvKit\Auth\Models\UserModel;
use SrvKit\Auth\Traits\AccessTokenTrait;
use SrvKit\Auth\Traits\ActionTrait;
use SrvKit\Auth\Traits\RefreshTokenTrait;

class Auth
{
    use ActionTrait;
    use AccessTokenTrait;
    use RefreshTokenTrait;

    public  ?User       $user;
    public  UserModel   $userModel;
    private Array       $data;
    private AuthConfig  $config;

    public function __construct(AuthConfig $config)
    {
        $this->config = $config;
        $this->userModel = new UserModel();
    }

    public function __set($key, $value){
        $this->data[$key] =  $value;
    }

    public function __get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function login(string $username, string $password): self
    {
        if (empty($username) || empty($password)) {
            throw new AuthException('Username and password are required', 'E20210');
        }

        $this->user = $this->userModel->where('username', $username)->first();

        if (!$this->user) {
            throw new AuthException('Invalid username or password', 'E20211');
        }
        
        if (!password_verify($password, $this->user->password)) {
            throw new AuthException('Invalid username or password', 'E20212');
        }

        return $this;
    }

    public function logout(): self
    {
        $this->clearRefreshTokenCookie();
        return $this;
    }

    public function obtainAccessFromLogin(): array
    {
        if(!$this->user) throw new AuthException('Invalid Operation.');
        $refreshToken = $this->generateRefreshToken();
        $this->saveRefreshToken($this->user->id, $refreshToken);
        $this->setRefreshTokenCookie($refreshToken);
        $payload = ['username' => $this->user->username];
        $accessToken = $this->generateAccessToken($payload);
        $accessUserType = $this->user->role;
        return array("access" => ['token' => $accessToken, 'type' => $accessUserType]);
    }
}

