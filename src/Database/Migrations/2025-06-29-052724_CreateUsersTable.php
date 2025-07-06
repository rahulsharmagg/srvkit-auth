<?php

namespace SrvKit\Auth\Database\Migrations;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\Migration;
use SrvKit\Auth\Config\Auth;

class CreateUsersTable extends Migration
{

    public Auth $config;

    public function __construct(?Forge $forge = null)
    {
        $this->config  = config('Auth');
        $this->DBGroup = $this->config->database['group'] ?? null;

        parent::__construct($forge);
    }

    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'username' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'email' => ['type' => 'VARCHAR', 'constraint' => '150'],
            'avatar' => ['type' => 'LONGBLOB', 'null' => true, ],
            'password' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'role' => ['type' => 'ENUM', 'constraint' => ['owner', 'member', 'author', 'admin'], 'default' => 'member'],
            'requested_role'    => ['type' => 'ENUM', 'constraint' => ['author', 'admin'], 'null' => true],
            'is_role_approved'  => ['type' => 'BOOLEAN', 'default' => false],
            'is_email_verified'  => ['type' => 'BOOLEAN', 'default' => false],
            'email_verification_date'  => ['type' => 'DATETIME', 'null' => true],
            'role_approved_date'  => ['type' => 'DATETIME', 'null' => true],
            'requested_role_date'  => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable($this->config->database['table'], true);
    }

    public function down()
    {
        $this->forge->dropTable($this->config->database['table']);
    }
}
