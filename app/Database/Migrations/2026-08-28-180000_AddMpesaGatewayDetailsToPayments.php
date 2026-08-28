<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMpesaGatewayDetailsToPayments extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('payments')) {
            return;
        }

        $columns = [];

        if (! $this->db->fieldExists('gateway_code_payment', 'payments')) {
            $columns['gateway_code_payment'] = [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => true,
                'after'      => 'reference_payment',
            ];
        }

        if (! $this->db->fieldExists('gateway_message_payment', 'payments')) {
            $columns['gateway_message_payment'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'gateway_code_payment',
            ];
        }

        if (! $this->db->fieldExists('gateway_txn_status_payment', 'payments')) {
            $columns['gateway_txn_status_payment'] = [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'gateway_message_payment',
            ];
        }

        if (! $this->db->fieldExists('failure_reason_payment', 'payments')) {
            $columns['failure_reason_payment'] = [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'gateway_txn_status_payment',
            ];
        }

        if ($columns !== []) {
            $this->forge->addColumn('payments', $columns);
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('payments')) {
            return;
        }

        foreach ([
            'failure_reason_payment',
            'gateway_txn_status_payment',
            'gateway_message_payment',
            'gateway_code_payment',
        ] as $column) {
            if ($this->db->fieldExists($column, 'payments')) {
                $this->forge->dropColumn('payments', $column);
            }
        }
    }
}
