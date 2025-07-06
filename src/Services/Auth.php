<?php

namespace SrvKit\Auth\Services;

use Firebase\JWT\Key;
use Firebase\JWT\JWT;
use SrvKit\Auth\Config\Auth as AuthConfig;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Exceptions\AuthException;
use SrvKit\Auth\Models\UserModel;

class Auth
{
    public ?User $user;

    public ?bool $isValidRefreshToken;

    public ?bool $isValidAccessToken;
    
    private $refreshKey, $accessKey;

    public function __construct(AuthConfig $config)
    {
        $this->refreshKey = $config::REFRESH_KEY;
        $this->accessKey = $config::ACCESS_KEY;
        $this->userModel = new UserModel();
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
                throw new AuthException('Invalid username or password', 401);
            }

            if (!password_verify($password, $this->user->password)) {
                throw new AuthException('Invalid username or password', 401);
            }

            return $this;
        } catch (\Exception $e) {
            throw new AuthException('Login failed: ' . $e->getMessage(), $e->getCode() ?: 401, $e);
        }
        return $this;
    }


    /**
     * Generates a JWT token
     * @return string JWT token
     */
    public function generateRefreshToken(): string
    {
        $issuedAt = time();
        $expiration = $issuedAt + AuthConfig::REFRESH_TOKEN_EXP;

        $data = [
            "username" => $this->user->username,
        ];

        $payload = [
            'iss' => base_url(),
            'iat' => $issuedAt,
            'exp' => $expiration,
            'data' => $data,
        ];

        return JWT::encode($payload, $this->refreshKey, 'HS256');
    }

    public function generateAccessToken(): string
    {
        $issuedAt = time();
        $expiration = $issuedAt + AuthConfig::ACCESS_TOKEN_EXP;

        $data = [
            "username" => $this->user->username,
        ];

        $payload = [
            'iss' => base_url(),
            'iat' => $issuedAt,
            'exp' => $expiration,
            'data' => $data,
        ];

        return JWT::encode($payload, $this->accessKey, 'HS256');
    }

    public function validateRefreshToken(string $token): self
    {   
        try {
            $payload = JWT::decode($token, new Key($this->refreshKey, 'H256'));
            $this->user = $this->userModel->where(['username' => $payload->data->username, 'email' => $payload->data->email]);
            $this->isValidRefreshToken = true;
        } catch (Exception $e) {
            $this->isValidRefreshToken = false;
        }
        return $this;
    }
}

