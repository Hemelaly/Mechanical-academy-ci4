<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserNotificationsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('user_notifications')) {
            return;
        }

        $this->forge->addField([
            'id_notification' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'type_notification' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
            ],
            'title_notification' => [
                'type'       => 'VARCHAR',
                'constraint' => 180,
            ],
            'body_notification' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'link_notification' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'context_notification' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'read_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id_notification', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('type_notification');
        $this->forge->addKey('created_at');
        $this->forge->addKey(['user_id', 'read_at']);
        $this->forge->createTable('user_notifications');
    }

    public function down()
    {
        $this->forge->dropTable('user_notifications', true);
    }
}
