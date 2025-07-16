<?php 

namespace SrvKit\Auth\Utils\Avatars\Driver;

class DicebearAvatar extends BaseAvatarDriver{
	private string $style;

	public function create(string $name): ?string
	{	
		/** @var \SrvKit\Auth\Config\Auth */
		$config = config('Auth');
		empty($config->diceBearStyle) ? $this->style = 'glass' : $this->style = $config->diceBearStyle;
		$encodedName = urlencode($name);
	    $url = "https://api.dicebear.com/9.x/{$this->style}/png?seed={$encodedName}";
		$binary = file_get_contents($url);
	    return $binary;
	}
}

