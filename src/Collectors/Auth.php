<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace SrvKit\Auth\Collectors;

use CodeIgniter\Debug\Toolbar\Collectors\BaseCollector;
use SrvKit\Auth\Auth as SrvKitAuth;

/**
 * Debug Toolbar Collector for Auth
 */
class Auth extends BaseCollector
{
    /**
     * Whether this collector has data that can
     * be displayed in the Timeline.
     *
     * @var bool
     */
    protected $hasTimeline = false;

    /**
     * Whether this collector needs to display
     * content in a tab or not.
     *
     * @var bool
     */
    protected $hasTabContent = true;

    /**
     * Whether this collector has data that
     * should be shown in the Vars tab.
     *
     * @var bool
     */
    protected $hasVarData = false;

    /**
     * The 'title' of this Collector.
     * Used to name things in the toolbar HTML.
     *
     * @var string
     */
    protected $title = 'SrvKit';

    private readonly SrvKitAuth $auth;

    public function __construct()
    {
        $this->auth = service('_auth');
    }

    /**
     * Returns any information that should be shown next to the title.
     */
    public function getTitleDetails(): string
    {
        return 'Auth '.(\SrvKit\Auth\Config\Version::VERSION);
    }

    /**
     * Returns the data of this collector to be formatted in the toolbar
     */
    public function display(): string
    {
        /** @var SrvKitAuth [description] */
        // $this->auth = service('_auth');

        $this->auth->setAuthenticator();

        if($this->auth->loggedIn()){
            return '<p>User with is logged in.</p>';
        }
        return '<p>Not logged in.</p>';
    }

    /**
     * Gets the "badge" value for the button.
     *
     * @return int|string|null ID of the current User, or null when not logged in
     */
    public function getBadgeValue()
    {
        return null;
    }

    /**
     * Display the icon.
     *
     * Icon from https://icons8.com - 1em package
     */
    public function icon(): string
    {
        return 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+PHN2ZyBmaWxsPSJub25lIiBoZWlnaHQ9IjI0IiB2aWV3Qm94PSIwIDAgMjQgMjQiIHdpZHRoPSIyNCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xOCA1LjQxNTAzTDExLjUgMy4xMjA5MUw1IDUuNDE1MDNWMTAuODE4NEM1IDEzLjQxNzggNi4yNjI5MiAxNS44NTUyIDguMzg2NTMgMTcuMzU0MkwxMS41IDE5LjU1MTlMMTQuNjEzNSAxNy4zNTQyQzE2LjczNzEgMTUuODU1MiAxOCAxMy40MTc4IDE4IDEwLjgxODRWNS40MTUwM1pNMTEuNSAyMkwxNS43NjY4IDE4Ljk4ODFDMTguNDIxMyAxNy4xMTQzIDIwIDE0LjA2NzcgMjAgMTAuODE4NFY0TDExLjUgMUwzIDRWMTAuODE4NEMzIDE0LjA2NzcgNC41Nzg2NSAxNy4xMTQzIDcuMjMzMTcgMTguOTg4MUwxMS41IDIyWiIgZmlsbD0iYmxhY2siIGZpbGwtcnVsZT0iZXZlbm9kZCIvPjwvc3ZnPg==';
    }
}