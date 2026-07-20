<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseCommerceAndPreviewFields extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('courses')) {
            return;
        }

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

        if (! $this->db->fieldExists('hours_course', 'courses')) {
            $fields['hours_course'] = [
                'type'       => 'DECIMAL',
                'constraint' => '6,1',
                'null'       => true,
                'default'    => null,
                'after'      => 'promo_price_course',
            ];
        }

        if (! $this->db->fieldExists('hours_manual_course', 'courses')) {
            $fields['hours_manual_course'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'hours_course',
            ];
        }

        if (! $this->db->fieldExists('free_lessons_course', 'courses')) {
            $fields['free_lessons_course'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'after'      => 'hours_manual_course',
            ];
        }

        if (! $this->db->fieldExists('whatsapp_course', 'courses')) {
            $fields['whatsapp_course'] = [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'default'    => null,
                'after'      => 'free_lessons_course',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('courses', $fields);
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('courses')) {
            return;
        }

        foreach (['whatsapp_course', 'free_lessons_course', 'hours_manual_course', 'hours_course', 'promo_price_course'] as $column) {
            if ($this->db->fieldExists($column, 'courses')) {
                $this->forge->dropColumn('courses', $column);
            }
        }
    }
}
