<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLessonAttachments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('lessons', [
            'attachment_path_lesson' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'video_url_lesson',
            ],
            'attachment_name_lesson' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'attachment_path_lesson',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('lessons', 'attachment_path_lesson');
        $this->forge->dropColumn('lessons', 'attachment_name_lesson');
    }
}
