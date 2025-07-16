<?php

namespace SrvKit\Auth\Utils\Avatars\Driver;

use SrvKit\Auth\Utils\Avatars\AvatarInterface;

abstract class BaseAvatarDriver implements AvatarInterface{
	protected int $width;
	protected int $height;

	public function toBase64(): string
	{
		return '';
	}
}