<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Paths;

class AppInfo extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'SrvKit Auth';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'srvkit:info';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Get info about the application.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'srvkit:info [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $phpVersion = phpversion();
        $version = \Config\Version::VERSION;

        CLI::write("Version: $version", 'cyan');
        CLI::write("PHP Version: $phpVersion", "cyan");
    }
}
