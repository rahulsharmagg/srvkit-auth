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

/**
 * Debug Toolbar Collector for Auth
 */
class Vite extends BaseCollector
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
    protected $title = 'Vite';


    /**
     * Vite status
     * 
     * @var bool
     */
    protected $viteStatus = false;

    public function __construct()
    {
        // require __DIR__.'/../Helpers/vite_helper.php';
        // $this->viteStatus = vite(port: 5341)->isRunning();
    }

    /**
     * Returns any information that should be shown next to the title.
     */
    public function getTitleDetails(): string
    {
        if($this->viteStatus){
            return <<<'EOD'
            <span style="--color: #baff20;font-size:x-small !important;display:inline-block;padding:2px 6px; border-radius: 25px;border:1px solid var(--color, #747576);color:var(--color, #747576);font-family: sans-serif;line-height:initial;">ACTIVE</span>
            EOD;
        }

        return <<<'EOD'
            <span style="font-size:x-small !important;display:inline-block;padding:2px 6px; border-radius: 25px;border:1px solid var(--color, #747576);color:var(--color, #747576);font-family: sans-serif;line-height:initial;">INACTIVE</span>
            EOD;
    }

    private function isViteRunning($host = 'localhost', $port = 5173) {

        $connection = @fsockopen($host, $port, $errno, $errstr, 1); // 1-second timeout
        
        if ($connection) {
            fclose($connection);
            return true;
        }

        return false;
    }

    /**
     * Returns the data of this collector to be formatted in the toolbar
     */
    public function display(): string
    {
        if($this->viteStatus){
            return <<<'EOD'
                <p style="color:lime;font-weight:bold;font-family:courier;font-size:large;">Vite is running on port 5341</p>
            EOD;
        }

        return <<<'EOD'
            <p style="color:#a2a2a2;font-weight:normal;font-family:courier;font-size:large;">Vite is not running</p>
        EOD;
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
        return 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgdmlld0JveD0iMCAwIDQxMCA0MDQiIGZpbGw9Im5vbmUiPjxwYXRoIGQ9Ik0zOTkuNjQxIDU5LjUyNDZMMjE1LjY0MyAzODguNTQ1QzIxMS44NDQgMzk1LjMzOCAyMDIuMDg0IDM5NS4zNzggMTk4LjIyOCAzODguNjE4TDEwLjU4MTcgNTkuNTU2M0M2LjM4MDg3IDUyLjE4OTYgMTIuNjgwMiA0My4yNjY1IDIxLjAyODEgNDQuNzU4NkwyMDUuMjIzIDc3LjY4MjRDMjA2LjM5OCA3Ny44OTI0IDIwNy42MDEgNzcuODkwNCAyMDguNzc2IDc3LjY3NjNMMzg5LjExOSA0NC44MDU4QzM5Ny40MzkgNDMuMjg5NCA0MDMuNzY4IDUyLjE0MzQgMzk5LjY0MSA1OS41MjQ2WiIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjMDAwMDAwIiBzdHJva2Utd2lkdGg9IjIwIi8+PHBhdGggZD0iTTI5Mi45NjUgMS41NzQ0TDE1Ni44MDEgMjguMjU1MkMxNTQuNTYzIDI4LjY5MzcgMTUyLjkwNiAzMC41OTAzIDE1Mi43NzEgMzIuODY2NEwxNDQuMzk1IDE3NC4zM0MxNDQuMTk4IDE3Ny42NjIgMTQ3LjI1OCAxODAuMjQ4IDE1MC41MSAxNzkuNDk4TDE4OC40MiAxNzAuNzQ5QzE5MS45NjcgMTY5LjkzMSAxOTUuMTcyIDE3My4wNTUgMTk0LjQ0MyAxNzYuNjIyTDE4My4xOCAyMzEuNzc1QzE4Mi40MjIgMjM1LjQ4NyAxODUuOTA3IDIzOC42NjEgMTg5LjUzMiAyMzcuNTZMMjEyLjk0NyAyMzAuNDQ2QzIxNi41NzcgMjI5LjM0NCAyMjAuMDY1IDIzMi41MjcgMjE5LjI5NyAyMzYuMjQyTDIwMS4zOTggMzIyLjg3NUMyMDAuMjc4IDMyOC4yOTQgMjA3LjQ4NiAzMzEuMjQ5IDIxMC40OTIgMzI2LjYwM0wyMTIuNSAzMjMuNUwzMjMuNDU0IDEwMi4wNzJDMzI1LjMxMiA5OC4zNjQ1IDMyMi4xMDggOTQuMTM3IDMxOC4wMzYgOTQuOTIyOEwyNzkuMDE0IDEwMi40NTRDMjc1LjM0NyAxMDMuMTYxIDI3Mi4yMjcgOTkuNzQ2IDI3My4yNjIgOTYuMTU4M0wyOTguNzMxIDcuODY2ODlDMjk5Ljc2NyA0LjI3MzE0IDI5Ni42MzYgMC44NTUxODEgMjkyLjk2NSAxLjU3NDRaIiBmaWxsPSIjMDAwMDAwIi8+PC9zdmc+';
    }
}