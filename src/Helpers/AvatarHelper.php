<?php
namespace SrvKit\Auth\Helpers;

use SrvKit\Auth\Config\Auth;
use SrvKit\Auth\Utils\Avatars\Driver\BaseAvatarDriver;

class AvatarHelper
{
	
	public static function generate(string $name)
	{
		/** @var Auth [description] */
		$config = config('Auth');

		/** @var BaseAvatarDriver [description] */
		$avatarDriver = $config->avatarDriver;
		$driver = new $avatarDriver();
		return $driver->create($name);
	}

	public static function toUrl(?string $binary)
	{
		if(!$binary) return '';
		$base64Data = base64_encode($binary);
		return 'data:image/png;base64,'.$base64Data;
	}
}