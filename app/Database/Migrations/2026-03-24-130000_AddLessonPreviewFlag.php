<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLessonPreviewFlag extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('lessons')) {
            return;
        }

        if (! $this->db->fieldExists('is_preview_lesson', 'lessons')) {
            $this->forge->addColumn('lessons', [
                'is_preview_lesson' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => false,
                    'default'    => 0,
                    'after'      => 'video_url_lesson',
                ],
            ]);
        }

        $this->db->query('UPDATE lessons SET is_preview_lesson = 0 WHERE is_preview_lesson IS NULL');
    }

    public function down()
    {
        if ($this->db->tableExists('lessons') && $this->db->fieldExists('is_preview_lesson', 'lessons')) {
            $this->forge->dropColumn('lessons', 'is_preview_lesson');
        }
    }
}
