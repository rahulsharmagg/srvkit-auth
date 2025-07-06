<?php

namespace App\Commands;

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
    protected $name = 'srvkit:setup';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Setup the authentication.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'app:setup [arguments] [options]';

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
        CLI::write('The app setup command is not active.', 'yellow');
        return;
    }
}
