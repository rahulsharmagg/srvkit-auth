<?php

namespace SrvKit\Auth\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class Info extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'SrvKit';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'auth:info';

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
    protected $usage = 'auth:info [arguments] [options]';

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
        $auth = config('Auth');
        CLI::write("SrvKit Authentication");
        $data = [
            ['Key' => 'Version', 'Value' => $version],
            ['Key' => 'Views', 'Value' => implode(', ', array_values($auth->views ?? []))],
            ['Key' => 'API Path', 'Value' => base_url($auth->apiPath ?? 'api/auth')],
        ];
        CLI::table($data);
    }
}
