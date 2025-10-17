<?php


namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnrollmentLessons extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_progress' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'id_enrollment_progress' => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'id_lesson_progress'     => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'completed_at_progress'  => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        // PK
        $this->forge->addKey('id_progress', true);

        // Índices (NÃO use IF NOT EXISTS aqui; o Forge cria junto com a tabela)
        $this->forge->addKey('id_enrollment_progress', false, false, 'idx_el_enrollment');
        $this->forge->addKey('id_lesson_progress', false, false, 'idx_el_lesson');

        // Único para evitar duplicados (enrollment x lesson)
        $this->forge->addUniqueKey(['id_enrollment_progress', 'id_lesson_progress'], 'uq_el_enrollment_lesson');

        // FKs (requer InnoDB)
        $this->forge->addForeignKey('id_enrollment_progress', 'enrollments', 'id_enrollment', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_lesson_progress',     'lessons',     'id_lesson',     'CASCADE', 'CASCADE');

        // Atributos opcionais: engine/charset
        $this->forge->createTable('progress', false, [
            'ENGINE'  => 'InnoDB',
            'COMMENT' => 'Progresso por aula dentro da matrícula',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('progress', true);
    }
}
