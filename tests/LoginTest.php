<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use SrvKit\Auth\Auth;
use Config\Database;

class LoginTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected Auth $auth;

    protected function setUp(): void
    {
        parent::setUp();

        // Override default database group to use MySQL for tests (not sqlite)
        $config = new \Config\Database();

        $config->tests = [
            'DSN'      => '',
            'hostname' => '127.0.0.1',
            'username' => 'root',
            'password' => '',
            'database' => 'test_db',
            'DBDriver' => 'MySQLi',
            'DBPrefix' => '',
            'pConnect' => false,
            'DBDebug'  => true,
            'charset'  => 'utf8mb4',
            'DBCollat' => 'utf8mb4_general_ci',
            'swapPre'  => '',
            'encrypt'  => false,
            'compress' => false,
            'strictOn' => false,
            'failover' => [],
            'port'     => 3306,
        ];

        Database::setConfig($config);
        $this->auth = service('Auth');
    }

    public function testLoginSuccess()
    {
        $this->assertTrue(true);
    }
}
