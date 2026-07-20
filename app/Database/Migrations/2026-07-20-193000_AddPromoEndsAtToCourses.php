<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPromoEndsAtToCourses extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('courses')) {
            return;
        }

        if (! $this->db->fieldExists('promo_ends_at_course', 'courses')) {
            $this->forge->addColumn('courses', [
                'promo_ends_at_course' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'promo_price_course',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('courses') && $this->db->fieldExists('promo_ends_at_course', 'courses')) {
            $this->forge->dropColumn('courses', 'promo_ends_at_course');
        }
    }
}
