<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeCertificateNumberNullable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('certificates') && $this->db->fieldExists('number_certificate', 'certificates')) {
            $this->forge->modifyColumn('certificates', [
                'number_certificate' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'default'    => null,
                ],
            ]);

            // Unique index allows multiple NULLs; normalize old empty values to NULL
            $this->db->query("UPDATE `certificates` SET `number_certificate` = NULL WHERE `number_certificate` = ''");
        }
    }

    public function down()
    {
        if ($this->db->tableExists('certificates') && $this->db->fieldExists('number_certificate', 'certificates')) {
            // Convert NULLs back to empty string for NOT NULL column
            $this->db->query("UPDATE `certificates` SET `number_certificate` = '' WHERE `number_certificate` IS NULL");

            $this->forge->modifyColumn('certificates', [
                'number_certificate' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => false,
                    'default'    => '',
                ],
            ]);
        }
    }
}

