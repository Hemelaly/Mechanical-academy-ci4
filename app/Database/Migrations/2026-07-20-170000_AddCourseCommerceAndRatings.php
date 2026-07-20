<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseCommerceAndRatings extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('courses')) {
            $fields = [];

            if (! $this->db->fieldExists('promo_price_course', 'courses')) {
                $fields['promo_price_course'] = [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'price_course',
                ];
            }

            if (! $this->db->fieldExists('hours_mode_course', 'courses')) {
                $fields['hours_mode_course'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => false,
                    'default'    => 'auto',
                    'after'      => 'promo_price_course',
                ];
            }

            if (! $this->db->fieldExists('hours_manual_course', 'courses')) {
                $fields['hours_manual_course'] = [
                    'type'       => 'DECIMAL',
                    'constraint' => '6,1',
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'hours_mode_course',
                ];
            }

            if (! $this->db->fieldExists('free_lessons_count_course', 'courses')) {
                $fields['free_lessons_count_course'] = [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => false,
                    'default'    => 0,
                    'after'      => 'hours_manual_course',
                ];
            }

            if (! $this->db->fieldExists('whatsapp_contact_course', 'courses')) {
                $fields['whatsapp_contact_course'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'free_lessons_count_course',
                ];
            }

            if ($fields !== []) {
                foreach ($fields as $name => $definition) {
                    if (! $this->db->fieldExists($name, 'courses')) {
                        $this->forge->addColumn('courses', [$name => $definition]);
                    }
                }
            }
        }

        if (! $this->db->tableExists('course_ratings')) {
            $this->forge->addField([
                'id_rating' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'id_course_rating' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'id_user_rating' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'id_enrollment_rating' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'score_rating' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'unsigned'   => true,
                ],
                'comment_rating' => [
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
            $this->forge->addKey('id_rating', true);
            $this->forge->addUniqueKey(['id_course_rating', 'id_user_rating']);
            $this->forge->addKey('id_course_rating');
            $this->forge->createTable('course_ratings', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('course_ratings')) {
            $this->forge->dropTable('course_ratings', true);
        }

        if ($this->db->tableExists('courses')) {
            foreach ([
                'whatsapp_contact_course',
                'free_lessons_count_course',
                'hours_manual_course',
                'hours_mode_course',
                'promo_price_course',
            ] as $column) {
                if ($this->db->fieldExists($column, 'courses')) {
                    $this->forge->dropColumn('courses', $column);
                }
            }
        }
    }
}
