<?php

namespace SrvKit\Auth\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

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
    protected $name = 'srvkit:version';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Gets the version of srvkit extension';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'srvkit:version [arguments] [options]';

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
        $version = \SrvKit\Auth\Config\Version::VERSION;
        CLI::write("Current Version: $version", 'cyan');
    }
}
