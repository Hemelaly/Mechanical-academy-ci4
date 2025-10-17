<?php

// app/Database/Migrations/2025-10-15-AddIndexesModulesLessons.php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesModulesLessons extends Migration
{
    public function up()
    {
        // cria só se não existir
        $idx1 = $this->db->query("SHOW INDEX FROM `lessons` WHERE Key_name = 'idx_lessons_module'")->getResultArray();
        if (!$idx1) $this->db->query("ALTER TABLE `lessons` ADD INDEX `idx_lessons_module` (`id_module_lesson`)");

        $idx2 = $this->db->query("SHOW INDEX FROM `modules` WHERE Key_name = 'idx_modules_course'")->getResultArray();
        if (!$idx2) $this->db->query("ALTER TABLE `modules` ADD INDEX `idx_modules_course` (`id_course_module`)");
    }

    public function down()
    {
        $idx1 = $this->db->query("SHOW INDEX FROM `lessons` WHERE Key_name = 'idx_lessons_module'")->getResultArray();
        if ($idx1) $this->db->query("ALTER TABLE `lessons` DROP INDEX `idx_lessons_module`");

        $idx2 = $this->db->query("SHOW INDEX FROM `modules` WHERE Key_name = 'idx_modules_course'")->getResultArray();
        if ($idx2) $this->db->query("ALTER TABLE `modules` DROP INDEX `idx_modules_course`");
    }
}
