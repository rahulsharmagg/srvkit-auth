<?php

namespace SrvKit\Auth\Utils\Avatars;

interface AvatarInterface
{
    public function create(string $name): ?string;
    public function toBase64(): string;
}
