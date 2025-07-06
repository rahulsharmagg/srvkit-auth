<?php

namespace SrvKit\Auth\Database\Migrations;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\Migration;
use Config\Session;
use SrvKit\Auth\Config\Session as SessionConfig;

class CreateSrvKitSessionsTable extends Migration
{
    protected $DBGroup = 'default';
    protected Session $config;
    public function __construct(?Forge $forge = null)
    {
        $this->config  = config(SessionConfig::class);
        $this->DBGroup = $this->config->DBGroup ?? null;

        parent::__construct($forge);
    }

    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => false],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => false],
            'timestamp timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL',
            'data' => ['type' => 'BLOB', 'null' => false],
         ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('timestamp');
        $this->forge->createTable($this->config->cookieName, true);
    }

    public function down()
    {
        $this->forge->dropTable($this->config->cookieName, true);
    }
}
