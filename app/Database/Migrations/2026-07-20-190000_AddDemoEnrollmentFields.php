<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDemoEnrollmentFields extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('enrollments')) {
            return;
        }

        $fields = [];

        if (! $this->db->fieldExists('is_demo_enrollment', 'enrollments')) {
            $fields['is_demo_enrollment'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'after'      => 'is_manual_enrollment',
            ];
        }

        if (! $this->db->fieldExists('demo_started_at', 'enrollments')) {
            $fields['demo_started_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'is_demo_enrollment',
            ];
        }

        if (! $this->db->fieldExists('demo_expires_at', 'enrollments')) {
            $fields['demo_expires_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'demo_started_at',
            ];
        }

        if ($fields !== []) {
            foreach ($fields as $name => $definition) {
                if (! $this->db->fieldExists($name, 'enrollments')) {
                    $this->forge->addColumn('enrollments', [$name => $definition]);
                }
            }
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('enrollments')) {
            return;
        }

        foreach (['demo_expires_at', 'demo_started_at', 'is_demo_enrollment'] as $column) {
            if ($this->db->fieldExists($column, 'enrollments')) {
                $this->forge->dropColumn('enrollments', $column);
            }
        }
    }
}
