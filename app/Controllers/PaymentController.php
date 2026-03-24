<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\PaymentModel;
use App\Models\EnrollmentModel;

class PaymentController extends Controller
{
    public function createPayment($idCourse, $pendingUserId)
    {
        $paymentModel    = new PaymentModel();
        $enrollmentModel = new EnrollmentModel();

        // Busca pending_user (em vez de user logado)
        $pendingUser = db_connect()
            ->table('pending_users')
            ->where('id', $pendingUserId)
            ->get()
            ->getRow();

        if (!$pendingUser) {
            return redirect()->back()->with('error', 'Usuário pendente não encontrado.');
        }

        // Upload do comprovativo
        $file = $this->request->getFile('proof_file_payment');
        $filePath = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/img/', $newName);
            $filePath = 'assets/img/' . $newName;
        }

        $post = $this->request->getPost();

        // Salvar pagamento vinculado ao pending_user
        $dataPayment = [
            'id_user_payment'     => $pendingUserId,
            'id_course_payment'   => $idCourse,
            'amount_payment'      => $post['amount_payment'] ?? 0,
            'status_payment'      => 'Pendente',
            'method_payment'      => 'Comprovativo',
            'proof_file_payment'  => $filePath,
            'created_at'          => date('Y-m-d H:i:s'),
        ];

        $idPayment = $paymentModel->insert($dataPayment, true);
        if ($idPayment === false) {
            return redirect()->back()->with('error', implode(', ', $paymentModel->errors()));
        }

        // Criar referência
        $reference = 'PAY-' . date('Y') . '-' . str_pad($idPayment, 6, '0', STR_PAD_LEFT);
        $paymentModel->update($idPayment, ['reference_payment' => $reference]);

        return redirect()->back()
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Pedido enviado!',
                'text'  => 'O comprovativo foi submetido e será analisado pelo instrutor.'
        ]);
    }
}
