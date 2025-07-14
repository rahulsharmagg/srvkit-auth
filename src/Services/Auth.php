<?php

namespace SrvKit\Auth\Services;

use CodeIgniter\Config\Services;
use CodeIgniter\Cookie\Cookie;
use CodeIgniter\View\View;
use Exception;
use Firebase\JWT\Key;
use Firebase\JWT\JWT;
use SrvKit\Auth\Config\Auth as AuthConfig;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Exceptions\AuthException;
use SrvKit\Auth\Models\UserModel;
use SrvKit\Auth\Traits\AccessTokenTrait;
use SrvKit\Auth\Traits\RefreshTokenTrait;

class Auth
{
    use RefreshTokenTrait;
    use AccessTokenTrait;

    public  ?User       $user;
    public  ?bool       $isValidRefreshToken;
    public  ?bool       $isValidAccessToken;
    public  UserModel   $userModel;
    public  Cookie      $cookie;
    public  String      $refreshToken;
    public  String      $action;
    public  View        $renderer;
    private AuthConfig  $config;

    public function __construct(AuthConfig $config)
    {
        $this->config = $config;
        $this->userModel = new UserModel();
    }

    public function action(string $action): self
    {
        try {
            if(!in_array($action, $this->config->actions)){
                throw new AuthException('Invalid Action.');
            }
            $this->action = $action;
            return $this;
        } catch (Exception $e) {
            throw new AuthException($e->getMessage());
        }
    }

    /**
     * Render the view of the action
     * @param  string $action [description]
     * @return [type]         [description]
     */
    public function view(string $action = '', array $data = [])
    {
        if(empty($action)){
            $action = $this->action;
        }
        return view('SrvKit\Auth\Views\action', ['action' => $action, ...$data]);
    }

    public function login(string $username, string $password): self
    {
        try {
            if (empty($username) || empty($password)) {
                throw new AuthException('Username and password are required', 401);
            }
            // $row = $this->userModel->where('username', $username)->first();
            $this->user = $this->userModel->where('username', $username)->first();

            if (!$this->user) {
                throw new AuthException('Invalid username or password (E20211)', 401);
            }
            
            if (!password_verify($password, $this->user->password)) {
                throw new AuthException('Invalid username or password (E20212)', 401);
            }

            return $this;
        } catch (\Exception $e) {
            throw new AuthException('Login failed: ' . $e->getMessage(), $e->getCode() ?: 401, $e);
        }
        return $this;
    }

    public function obtainAccessFromLogin()
    {
        if(!$this->user) throw new AuthException('Invalid Operation.');
        $refreshToken = $this->generateRefreshToken();
        $this->saveRefreshToken($this->user->id, $refreshToken);
        $this->setRefreshTokenCookie($refreshToken);

        $accessToken = $this->generateAccessToken();
        $accessUserType = $this->user->role;
        return ['access_token' => $accessToken, 'access_type' => $accessUserType];
    }
}

