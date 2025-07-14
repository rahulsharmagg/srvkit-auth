<?php

namespace SrvKit\Auth\Database\Migrations;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\Migration;
use SrvKit\Auth\Config\Auth;

class CreateSrvKitActionTokensTable extends Migration
{
    protected $DBGroup = 'default';
    protected Auth $config;
    public function __construct(?Forge $forge = null)
    {
        parent::__construct($forge);

        /** @var Auth [description] */
        $this->config = config('Auth');
    }

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'token_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => '128',
                'null'       => false,
            ],
            'action' => [
                'type'       => 'ENUM',
                'constraint' => $this->config->actions,
                'null'       => false,
            ],
            'used' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
                'null'       => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'used_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');        
        $this->forge->createTable('__srvkit_action_token__', true);
    }

    public function down()
    {
        $this->forge->dropTable('__srvkit_action_token__');
    }
}
