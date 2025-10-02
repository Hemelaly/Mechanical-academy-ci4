<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use CodeIgniter\Controller;
use CodeIgniter\Shield\Models\UserModel;

class Register extends Controller
{
    public function createPendingUser($idCourse)
    {
        $db = db_connect();
        $post = $this->request->getPost();

        $users = model(UserModel::class);

        // 1. Verificar se email já existe na tabela users
        $existingUser = $users->findByCredentials(['email' => $post['email']]);
        
        $isLoggedIn   = auth()->loggedIn();

        if ($existingUser && !$isLoggedIn) {
            return redirect()->back()->with('swal', [
                'icon'  => 'warning',
                'title' => 'Já tem conta!',
                'text'  => 'Esse email já está registrado. Por favor, faça login para continuar.',
            ]);
        }

        // 2. Criar pending_user (apenas se email for novo ou se já existir mas o user está logado)
        $db->table('pending_users')->insert([
            'email'      => $post['email'],
            'username'   => $post['username'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $pendingId = $db->insertID();
        if (!$pendingId) {
            return redirect()->back()->with('error', 'Erro ao criar usuário pendente.');
        }

        // 3. Upload do comprovativo
        $file = $this->request->getFile('proof_file_payment');
        $filePath = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/img/', $newName);
            $filePath = 'assets/img/' . $newName;
        }

        // 4. Criar pagamento pendente vinculado ao pending_user
        $paymentModel = new PaymentModel();
        $dataPayment = [
            'id_user_payment'     => $pendingId,
            'id_course_payment'   => $idCourse,
            'amount_payment'      => $post['amount_payment'] ?? 0,
            'status_payment'      => 'Pendente',
            'proof_file_payment'  => $filePath,
            'created_at'          => date('Y-m-d H:i:s'),
        ];

        $idPayment = $paymentModel->insert($dataPayment, true);
        if ($idPayment === false) {
            return redirect()->back()->with('error', implode(', ', $paymentModel->errors()));
        }

        // 5. Gerar referência
        $reference = 'PAY-' . date('Y') . '-' . str_pad($idPayment, 6, '0', STR_PAD_LEFT);
        $paymentModel->update($idPayment, ['reference_payment' => $reference]);

        // 6. Retornar com SweetAlert de sucesso
        return redirect()->to("/checkout/{$idCourse}")
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Pedido enviado!',
                'text'  => 'O comprovativo foi submetido e será analisado pelo instrutor. Após a confirmação, enviaremos um link para o seu email.'
            ]);
    }
}
