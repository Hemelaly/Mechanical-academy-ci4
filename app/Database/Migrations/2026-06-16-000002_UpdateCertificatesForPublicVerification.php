<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCertificatesForPublicVerification extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('certificates')) {
            return;
        }

        if (! $this->db->fieldExists('status_certificate', 'certificates')) {
            $this->forge->addColumn('certificates', [
                'status_certificate' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'null'       => false,
                    'default'    => 'available',
                    'after'      => 'hash_certificate',
                ],
            ]);
        }

        if (! $this->db->fieldExists('available_at_certificate', 'certificates')) {
            $this->forge->addColumn('certificates', [
                'available_at_certificate' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'avaiable_at_certificate',
                ],
            ]);
        }

        if ($this->db->fieldExists('number_certificate', 'certificates')) {
            $this->db->query("ALTER TABLE `certificates` MODIFY `number_certificate` VARCHAR(80) NULL DEFAULT NULL");
            $this->db->query("UPDATE `certificates` SET `number_certificate` = NULL WHERE `number_certificate` = ''");
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('certificates')) {
            return;
        }

        if ($this->db->fieldExists('available_at_certificate', 'certificates')) {
            $this->forge->dropColumn('certificates', 'available_at_certificate');
        }

        if ($this->db->fieldExists('status_certificate', 'certificates')) {
            $this->forge->dropColumn('certificates', 'status_certificate');
        }
    }
}
