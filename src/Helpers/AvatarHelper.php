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
}