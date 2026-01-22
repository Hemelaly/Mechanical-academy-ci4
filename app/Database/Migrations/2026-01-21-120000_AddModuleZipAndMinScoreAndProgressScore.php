<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddModuleZipAndMinScoreAndProgressScore extends Migration
{
    public function up()
    {
        $this->forge->addColumn('modules', [
            'content_zip_module' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'description_module',
            ],
            'min_score_module' => [
                'type' => 'TINYINT',
                'constraint' => 3,
                'unsigned' => true,
                'default' => 75,
                'after' => 'content_zip_module',
            ],
        ]);

        $this->forge->addColumn('progress', [
            'score_progress' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'after' => 'completed_at_progress',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('modules', 'content_zip_module');
        $this->forge->dropColumn('modules', 'min_score_module');
        $this->forge->dropColumn('progress', 'score_progress');
    }
}
