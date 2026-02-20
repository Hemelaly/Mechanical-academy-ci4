<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropUniqueUsernameOnUsers extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('users')) {
            return;
        }

        $indexes = $this->db->query("SHOW INDEX FROM `users` WHERE Key_name = 'username'")->getResultArray();
        if ($indexes !== []) {
            $this->db->query('ALTER TABLE `users` DROP INDEX `username`');
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('users')) {
            return;
        }

        $indexes = $this->db->query("SHOW INDEX FROM `users` WHERE Key_name = 'username'")->getResultArray();
        if ($indexes === []) {
            $this->db->query('ALTER TABLE `users` ADD UNIQUE KEY `username` (`username`)');
        }
    }
}
