<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLessonDiscussionsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('lesson_discussions')) {
            return;
        }

        $this->forge->addField([
            'id_discussion' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_lesson_discussion' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'id_course_discussion' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'id_user_discussion' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'id_parent_discussion' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'body_discussion' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_discussion', true);
        $this->forge->addKey('id_lesson_discussion');
        $this->forge->addKey('id_course_discussion');
        $this->forge->addKey('id_user_discussion');
        $this->forge->addKey('id_parent_discussion');
        $this->forge->addKey('created_at');
        $this->forge->createTable('lesson_discussions');
    }

    public function down()
    {
        $this->forge->dropTable('lesson_discussions', true);
    }
}
