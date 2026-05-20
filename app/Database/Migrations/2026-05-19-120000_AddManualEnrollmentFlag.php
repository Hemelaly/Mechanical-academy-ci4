<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddManualEnrollmentFlag extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('enrollments')) {
            return;
        }

        $fields = [];

        if (! $this->db->fieldExists('is_manual_enrollment', 'enrollments')) {
            $fields['is_manual_enrollment'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'after'      => 'status_enrollment',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('enrollments', $fields);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('enrollments') && $this->db->fieldExists('is_manual_enrollment', 'enrollments')) {
            $this->forge->dropColumn('enrollments', 'is_manual_enrollment');
        }
    }
}

