<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJitsiRecordings extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('jitsi_recordings')) {
            return;
        }

        $this->forge->addField([
            'id_jitsi_recording' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_jitsi_session' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'recording_url' => [
                'type' => 'VARCHAR',
                'constraint' => 2048,
            ],
            'provider_recording_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'recording_mode' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'file',
            ],
            'status_recording' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'ready',
            ],
            'duration_seconds' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'is_published' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'published_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_jitsi_recording', true);
        $this->forge->addKey('id_jitsi_session');
        $this->forge->addKey('status_recording');
        $this->forge->addKey('is_published');
        $this->forge->addForeignKey(
            'id_jitsi_session',
            'jitsi',
            'id_jitsi',
            'CASCADE',
            'CASCADE',
            'fk_jitsi_recordings_session'
        );
        $this->forge->createTable('jitsi_recordings', true);
    }

    public function down()
    {
        if (! $this->db->tableExists('jitsi_recordings')) {
            return;
        }

        $this->forge->dropTable('jitsi_recordings', true);
    }
}
