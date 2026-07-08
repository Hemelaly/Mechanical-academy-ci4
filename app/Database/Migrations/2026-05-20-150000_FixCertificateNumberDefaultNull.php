<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixCertificateNumberDefaultNull extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('certificates') || ! $this->db->fieldExists('number_certificate', 'certificates')) {
            return;
        }

        // Garante DEFAULT NULL (se o DEFAULT for '', inserts que omitirem a coluna podem colidir na unique key)
        $this->db->query("ALTER TABLE `certificates` MODIFY `number_certificate` VARCHAR(50) NULL DEFAULT NULL");

        // Normaliza dados antigos
        $this->db->query("UPDATE `certificates` SET `number_certificate` = NULL WHERE `number_certificate` = ''");
    }

    public function down()
    {
        if (! $this->db->tableExists('certificates') || ! $this->db->fieldExists('number_certificate', 'certificates')) {
            return;
        }

        $this->db->query("UPDATE `certificates` SET `number_certificate` = '' WHERE `number_certificate` IS NULL");
        $this->db->query("ALTER TABLE `certificates` MODIFY `number_certificate` VARCHAR(50) NULL DEFAULT ''");
    }
}

