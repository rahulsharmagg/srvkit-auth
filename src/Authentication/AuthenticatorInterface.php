<?php

namespace SrvKit\Auth\Authentication;

use SrvKit\Auth\Entities\User;

interface AuthenticatorInterface{
	public function getUser(): ?User;

	public function attempt(array $credentials): AuthenticatorResult;

	public function login(User $user): void;

	public function loggedIn(): bool;

	public function logout(): void;
}

