<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyLessonContentToText extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('lessons', [
            'content_lesson' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('lessons', [
            'content_lesson' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
    }
}
