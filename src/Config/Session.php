<?php 

namespace SrvKit\Auth\Config;

use CodeIgniter\Session\Handlers\BaseHandler;
use CodeIgniter\Session\Handlers\DatabaseHandler;

class Session extends \Config\Session
{
	/**
	 * --------------------------------------------------------------------------
	 * Session Driver
	 * --------------------------------------------------------------------------
	 *
	 * The session storage driver to use:
	 * - `CodeIgniter\Session\Handlers\FileHandler`
	 * - `CodeIgniter\Session\Handlers\DatabaseHandler`
	 * - `CodeIgniter\Session\Handlers\MemcachedHandler`
	 * - `CodeIgniter\Session\Handlers\RedisHandler`
	 *
	 * @var class-string<BaseHandler>
	 */
	public string 	$driver = 'CodeIgniter\Session\Handlers\DatabaseHandler';
	public string 	$savePath = '__srvkit_session__';
	public string 	$cookieName = '__srvkit_session__';
	public int 		$expiration = 3600;
	public bool 	$matchIP = false;
	public int 		$timeToUpdate = 300;
	public bool 	$regenerateDestroy = false;
	public ?string 	$DBGroup = null;
}
