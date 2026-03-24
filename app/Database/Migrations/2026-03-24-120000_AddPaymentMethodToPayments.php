<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentMethodToPayments extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('payments')) {
            return;
        }

        if (! $this->db->fieldExists('method_payment', 'payments')) {
            $this->forge->addColumn('payments', [
                'method_payment' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'status_payment',
                ],
            ]);
        }

        if ($this->db->fieldExists('payment_method', 'payments')) {
            $this->db->query("
                UPDATE payments
                SET method_payment = payment_method
                WHERE (method_payment IS NULL OR method_payment = '')
                  AND payment_method IS NOT NULL
                  AND payment_method <> ''
            ");
        }

        $this->db->query("
            UPDATE payments
            SET method_payment = 'M-Pesa'
            WHERE (method_payment IS NULL OR method_payment = '')
              AND reference_payment LIKE 'CRS%'
        ");

        $this->db->query("
            UPDATE payments
            SET method_payment = 'Comprovativo'
            WHERE (method_payment IS NULL OR method_payment = '')
              AND proof_file_payment IS NOT NULL
              AND proof_file_payment <> ''
        ");

        $this->db->query("
            UPDATE payments
            SET method_payment = 'Nao informado'
            WHERE method_payment IS NULL OR method_payment = ''
        ");
    }

    public function down()
    {
        if ($this->db->tableExists('payments') && $this->db->fieldExists('method_payment', 'payments')) {
            $this->forge->dropColumn('payments', 'method_payment');
        }
    }
}
