<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGuestCheckoutSupport extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('payments')) {
            $paymentFields = [];

            if (! $this->db->fieldExists('guest_email_payment', 'payments')) {
                $paymentFields['guest_email_payment'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                    'after'      => 'reference_payment',
                ];
            }

            if (! $this->db->fieldExists('guest_name_payment', 'payments')) {
                $paymentFields['guest_name_payment'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'guest_email_payment',
                ];
            }

            if ($paymentFields !== []) {
                $this->forge->addColumn('payments', $paymentFields);
            }
        }

        if ($this->db->tableExists('pending_users')) {
            $pendingFields = [];

            if (! $this->db->fieldExists('payment_id', 'pending_users')) {
                $pendingFields['payment_id'] = [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'course_id',
                ];
            }

            if (! $this->db->fieldExists('setup_token', 'pending_users')) {
                $pendingFields['setup_token'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 64,
                    'null'       => true,
                    'after'      => 'status',
                ];
            }

            if (! $this->db->fieldExists('setup_expires_at', 'pending_users')) {
                $pendingFields['setup_expires_at'] = [
                    'type'  => 'DATETIME',
                    'null'  => true,
                    'after' => 'setup_token',
                ];
            }

            if ($pendingFields !== []) {
                $this->forge->addColumn('pending_users', $pendingFields);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('pending_users')) {
            foreach (['setup_expires_at', 'setup_token', 'payment_id'] as $field) {
                if ($this->db->fieldExists($field, 'pending_users')) {
                    $this->forge->dropColumn('pending_users', $field);
                }
            }
        }

        if ($this->db->tableExists('payments')) {
            foreach (['guest_name_payment', 'guest_email_payment'] as $field) {
                if ($this->db->fieldExists($field, 'payments')) {
                    $this->forge->dropColumn('payments', $field);
                }
            }
        }
    }
}
