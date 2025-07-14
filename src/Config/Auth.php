<?php

namespace SrvKit\Auth\Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig
{

    public const PREFIX = "SRVKIT";
    /**
     * Set default secret key for ACCESS TOKEN ENCRYPTION
     */
    public const ACCESS_TOKEN_KEY = "DEFAULT_ACCESS_KEY";

    /**
     * Set default secret key for REFRESH TOKEN ENCRYPTION
     */
    public const REFRESH_TOKEN_KEY = "DEFAULT_REFRESH_KEY";

    /**
     * Access token expireation time in seconds
     */
    public const ACCESS_TOKEN_EXP = 30;

    /**
     * Set refresh token expiration time in seconds
     */
    public const REFRESH_TOKEN_EXP = 3600; // 1 Hour

    /**
     * Set Temp link expiration time in seconds
     */
    public const TEMP_LINK_EXP = 600; // 10 Mins

    /**
     * Set theme for login/signup form 
     * Options: 
     * 'dark', 'default'
     * @var string
     */
    public string $theme = 'default';


    public array $actions = [
        'forgot-password', 
        'reset-password', 
        'verify-email', 
        'update-avatar',
        'update-password',
    ];

    public array $views = [
        'layout'    => 'SrvKit\Auth\Views\layout',
        'action'    => 'SrvKit\Auth\Views\action',
        'login'     => 'SrvKit\Auth\Views\auth',
        'signup'    => 'SrvKit\Auth\Views\auth'
    ];

    /**
     * Set user database
     * @var [type]
     */
    public $database = [
        'table'     => 'users',
        'group'     => null,
        'writeable' => true,
    ];
}