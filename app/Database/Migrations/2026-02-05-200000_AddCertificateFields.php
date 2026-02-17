<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCertificateFields extends Migration
{
    private array $columnsToAdd = [
        'avaiable_at_certificate' => [
            'type'    => 'DATETIME',
            'null'    => true,
        ],
        'pdf_path_certificate' => [
            'type'       => 'VARCHAR',
            'constraint' => 255,
            'null'       => true,
        ],
        'number_certificate' => [
            'type'       => 'VARCHAR',
            'constraint' => 80,
            'null'       => true,
        ],
        'issued_at_certificate' => [
            'type'    => 'DATETIME',
            'null'    => true,
        ],
        'uploaded_by_certificate' => [
            'type'    => 'INT',
            'unsigned'=> true,
            'null'    => true,
        ],
    ];

    public function up()
    {
        foreach ($this->columnsToAdd as $column => $definition) {
            if ($this->db->fieldExists($column, 'certificates')) {
                continue;
            }

            $this->forge->addColumn('certificates', [
                $column => $definition,
            ]);
        }
    }

    public function down()
    {
        foreach (array_keys($this->columnsToAdd) as $column) {
            if (! $this->db->fieldExists($column, 'certificates')) {
                continue;
            }

            $this->forge->dropColumn('certificates', $column);
        }
    }
}
