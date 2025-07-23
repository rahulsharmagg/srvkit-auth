<?php

namespace SrvKit\Auth\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Paths;

class AppSetup extends BaseCommand
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
    protected $name = 'srvkit:install';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Install the srvkit authentication extension.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'srvkit:install [arguments] [options]';

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
        CLI::write('This extions is unable to install at this time.', 'yellow');
        return;
    }
}
