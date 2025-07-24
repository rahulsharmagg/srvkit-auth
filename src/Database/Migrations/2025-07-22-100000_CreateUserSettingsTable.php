<?php

namespace SrvKit\Auth\Database\Migrations;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\Migration;

class CreateUserSettingsTable extends Migration
{
    protected $DBGroup = 'default';

    public function __construct(?Forge $forge = null)
    {
        parent::__construct($forge);
    }

    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'auto_increment' => true, 'unsigned' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'key' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'value' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => false],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_settings');
    }

    public function down()
    {
        $this->forge->dropTable('user_settings');
    }
}
