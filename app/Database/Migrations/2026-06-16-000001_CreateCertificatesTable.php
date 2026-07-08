<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCertificatesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_certificate' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'code_certificate' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'unique' => true,
            ],
            'name_student_certificate' => [
                'type' => 'VARCHAR',
                'constraint' => 180,
            ],
            'name_course_certificate' => [
                'type' => 'VARCHAR',
                'constraint' => 180,
            ],
            'concluded_at_certificate' => [
                'type' => 'DATE',
            ],
            'trainer_name_certificate' => [
                'type' => 'VARCHAR',
                'constraint' => 180,
                'null' => true,
            ],
            'director_name_certificate' => [
                'type' => 'VARCHAR',
                'constraint' => 180,
                'null' => true,
            ],
            'file_path_certificate' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'status_certificate' => [
                'type' => 'ENUM',
                'constraint' => ['valid', 'revoked'],
                'default' => 'valid',
            ],
            'verification_url_certificate' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_certificate', true);
        $this->forge->addKey('code_certificate');
        $this->forge->createTable('certificates', true);
    }

    public function down()
    {
        $this->forge->dropTable('certificates', true);
    }
}
