<?php

namespace SrvKit\Auth\Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig
{
    public const REFRESH_KEY = "DEFAULT_REFRESH_KEY";
    public const ACCESS_KEY = "DEFAULT_ACCESS_KEY";

    /**
     * Access token expireation time in seconds
     */
    public const ACCESS_TOKEN_EXP = 30;

    public string $theme = 'default';

    /**
     * Set refresh token expiration time in seconds
     */
    public const REFRESH_TOKEN_EXP = 3600;

    public $database = [
        'table'     => 'users',
        'group'     => null,
        'writeable' => true,
    ];
}