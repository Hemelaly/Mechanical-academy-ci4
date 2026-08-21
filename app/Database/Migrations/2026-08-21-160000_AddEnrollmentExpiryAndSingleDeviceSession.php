<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnrollmentExpiryAndSingleDeviceSession extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('enrollments') && ! $this->db->fieldExists('expires_at_enrollment', 'enrollments')) {
            $this->forge->addColumn('enrollments', [
                'expires_at_enrollment' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'enrolled_at_enrollment',
                ],
            ]);

            // Backfill em PHP para evitar datas 0000-00-00 no MySQL strict.
            $rows = $this->db->table('enrollments')
                ->select('id_enrollment, enrolled_at_enrollment, created_at')
                ->where('expires_at_enrollment', null)
                ->groupStart()
                    ->where('is_demo_enrollment', null)
                    ->orWhere('is_demo_enrollment', 0)
                ->groupEnd()
                ->where('status_enrollment', 'ativa')
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $start = trim((string) ($row['enrolled_at_enrollment'] ?? ''));
                if ($start === '' || str_starts_with($start, '0000-00-00')) {
                    $start = trim((string) ($row['created_at'] ?? ''));
                }
                if ($start === '' || str_starts_with($start, '0000-00-00')) {
                    $start = date('Y-m-d');
                }

                $expires = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($start) ?: time()));
                $this->db->table('enrollments')
                    ->where('id_enrollment', (int) $row['id_enrollment'])
                    ->update(['expires_at_enrollment' => $expires]);
            }
        }

        if ($this->db->tableExists('users')) {
            $fields = [];

            if (! $this->db->fieldExists('active_device_token', 'users')) {
                $fields['active_device_token'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 64,
                    'null'       => true,
                    'after'      => 'role',
                ];
            }

            if (! $this->db->fieldExists('active_device_at', 'users')) {
                $fields['active_device_at'] = [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'active_device_token',
                ];
            }

            if ($fields !== []) {
                $this->forge->addColumn('users', $fields);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('enrollments') && $this->db->fieldExists('expires_at_enrollment', 'enrollments')) {
            $this->forge->dropColumn('enrollments', 'expires_at_enrollment');
        }

        if ($this->db->tableExists('users')) {
            foreach (['active_device_at', 'active_device_token'] as $column) {
                if ($this->db->fieldExists($column, 'users')) {
                    $this->forge->dropColumn('users', $column);
                }
            }
        }
    }
}
