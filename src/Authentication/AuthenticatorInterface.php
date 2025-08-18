<?php

namespace SrvKit\Auth\Authentication;

use CodeIgniter\Cookie\Cookie;
use SrvKit\Auth\Entities\User;
use SrvKit\Auth\Exceptions\AuthException;

interface AuthenticatorInterface{
	public function getUser(): ?User;

	public function attempt(array $credentials): AuthenticatorResult;

	public function login(User $user, ?AuthException &$error, ?array &$access, ?Cookie &$cookie): void;

	public function loggedIn(): bool;

	public function logout(): void;
}

