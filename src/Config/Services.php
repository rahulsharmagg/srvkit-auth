<?php

namespace SrvKit\Auth\Config;

use CodeIgniter\Config\BaseService;
use SrvKit\Auth\Auth as AuthAuth;
use SrvKit\Auth\Services\Auth;
use Srvkit\Auth\Config\Auth as AuthConfig;
use SrvKit\Auth\Services\TempLink;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{

    public static function auth(?AuthConfig $config = null, bool $getShared = true): Auth
    {
        if ($getShared) {
            return static::getSharedInstance('auth', $config);
        }

        return new Auth($config ?? config('Auth'));
    }

    public static function _auth(?AuthConfig $config = null, bool $getShared = true): AuthAuth
    {
        if ($getShared) {
            return static::getSharedInstance('_auth', $config);
        }

        return new AuthAuth($config ?? config('Auth'));
    }

    public static function tempLink(String $token = '', bool $getShared = true): TempLink
    {
        if ($getShared) {
            return static::getSharedInstance('templink', $token);
        }

        return new TempLink($token ?? '');
    }

    public static function srvkitsession(?Session $sessionConfig = null)
    {
        return \Config\Services::session($sessionConfig ?? config(Session::class));
    }
}