<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseReviewsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('course_reviews')) {
            return;
        }

        $this->forge->addField([
            'id_review' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_course_review' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_user_review' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'rating_review' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
            ],
            'comment_review' => [
                'type' => 'TEXT',
                'null' => true,
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

        $this->forge->addKey('id_review', true);
        $this->forge->addUniqueKey(['id_course_review', 'id_user_review'], 'course_user_review_unique');
        $this->forge->addKey('id_course_review');
        $this->forge->createTable('course_reviews', true);
    }

    public function down()
    {
        $this->forge->dropTable('course_reviews', true);
    }
}
