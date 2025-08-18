<?php

namespace SrvKit\Auth\Config;

use CodeIgniter\Config\BaseConfig;
use SrvKit\Auth\Models\UserModel;
use SrvKit\Auth\Utils\Avatars\Driver\BaseAvatarDriver;

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
     * Avatar driver class name.
     *
     * This should be a subclass of BaseAvatarDriver.
     *
     * @var class-string<BaseAvatarDriver>
     * @example - \SrvKit\Auth\Utils\Avatars\Driver\GDAvatar::class
     * @example - \SrvKit\Auth\Utils\Avatars\Driver\DicebearAvatar::class
     */
    public $avatarDriver = \SrvKit\Auth\Utils\Avatars\Driver\DicebearAvatar::class;

    /**
     * Dicebear available styles
     * 
     * **Possible values:**
     * - **`'glass'`**
     * `'icons'`
     * `'dylan'`
     * `'bottts-neutral'`
     * 
     * @var string
     */
    public $diceBearStyle = 'icons';


    /**
     * Set theme for view
     * Options: 
     * 'dark', 'light'
     * @var string
     */
    public string $theme = 'light';


    public array $actions = [
        'logout',
        'forgot-password', 
        'reset-password', 
        'verify-email', 
        'update-avatar',
        'update-password',
        'update-setting'
    ];

    public array $views = [
        'layout'    => 'SrvKit\Auth\Views\layout',
        'action'    => 'SrvKit\Auth\Views\action',
        'login'     => 'SrvKit\Auth\Views\auth',
        'signup'    => 'SrvKit\Auth\Views\auth',
        'dashboard' => 'SrvKit\Auth\Views\dashboard',
        'error'     => 'SrvKit\Auth\Views\error'
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

    public $userProvider = UserModel::class;
}