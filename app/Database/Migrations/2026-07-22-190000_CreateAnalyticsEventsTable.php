<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnalyticsEventsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('analytics_events')) {
            return;
        }

        $this->forge->addField([
            'id_analytics' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'event_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
            ],
            'path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'route_label' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
            ],
            'referrer' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'element' => [
                'type'       => 'VARCHAR',
                'constraint' => 180,
                'null'       => true,
            ],
            'persona' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'guest',
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'visitor_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
            ],
            'session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'user_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'device' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'meta_json' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id_analytics', true);
        $this->forge->addKey('event_type');
        $this->forge->addKey('path');
        $this->forge->addKey('persona');
        $this->forge->addKey('user_id');
        $this->forge->addKey('visitor_id');
        $this->forge->addKey('created_at');
        $this->forge->addKey(['event_type', 'created_at']);
        $this->forge->createTable('analytics_events');
    }

    public function down()
    {
        $this->forge->dropTable('analytics_events', true);
    }
}
