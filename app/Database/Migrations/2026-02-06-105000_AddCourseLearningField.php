<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseLearningField extends Migration
{
    private array $columnsToAdd = [
        'learning_course' => [
            'type' => 'TEXT',
            'null' => true,
        ],
        'url_video_course' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => true,
        ],
    ];

    public function up()
    {
        foreach ($this->columnsToAdd as $column => $definition) {
            if ($this->db->fieldExists($column, 'courses')) {
                continue;
            }

            $this->forge->addColumn('courses', [
                $column => $definition,
            ]);
        }
    }

    public function down()
    {
        foreach (array_keys($this->columnsToAdd) as $column) {
            if (! $this->db->fieldExists($column, 'courses')) {
                continue;
            }

            $this->forge->dropColumn('courses', $column);
        }
    }
}
