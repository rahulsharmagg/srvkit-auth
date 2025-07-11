<?php

namespace SrvKit\Auth\Database\Migrations;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\Migration;

class CreateUserTokensTable extends Migration
{
    protected $DBGroup = 'default';

    public function __construct(?Forge $forge = null)
    {
        parent::__construct($forge);
    }

    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'refresh_token_hash' => ['type' => 'TEXT'],
            'user_agent' => ['type' => 'TEXT', 'null' => true],
            'logged_in_at' => ['type' => 'DATETIME'],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'expires_at' => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('__srvkit_token__');
    }

    public function down()
    {
        $this->forge->dropTable('__srvkit_token__');
    }
}
