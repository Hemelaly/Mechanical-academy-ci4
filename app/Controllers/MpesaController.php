<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use emagombe\Mpesa;
use App\Models\PaymentModel;
use App\Models\EnrollmentModel;

class MpesaController extends Controller
{
    public function send()
    {
        helper(['form']);

        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_course'       => 'required|integer',
            'amount_payment'  => 'required|numeric',
            'client_number'   => 'required|min_length[9]|max_length[15]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('swal', [
                'icon'  => 'error',
                'title' => 'Dados inválidos',
                'text'  => implode(' | ', $validation->getErrors()),
            ]);
        }

        $user = auth()->user();

        if (!$user) {
            return redirect()->back()->with('swal', [
                'icon'  => 'warning',
                'title' => 'Sessão expirada',
                'text'  => 'Faça login antes de efetuar o pagamento.',
            ]);
        }

        $courseId     = (int) $this->request->getPost('id_course');
        $amount       = (float) $this->request->getPost('amount_payment');
        $clientNumber = trim((string) $this->request->getPost('client_number'));

        // Normaliza o número
        $clientNumber = preg_replace('/\D+/', '', $clientNumber);

        if (strpos($clientNumber, '258') !== 0) {
            $clientNumber = '258' . ltrim($clientNumber, '0');
        }

        $paymentModel = new PaymentModel();

        // Referência rastreável e curta para caber no teu campo reference_payment
        $reference = 'CRS' . $courseId . 'U' . $user->id . 'T' . time();

        // Evita pagamento duplicado já aprovado para o mesmo curso/utilizador
        $existingApproved = $paymentModel
            ->where('id_user_payment', $user->id)
            ->where('id_course_payment', $courseId)
            ->where('status_payment', 'Aprovado')
            ->first();

        if ($existingApproved) {
            return redirect()->back()->with('swal', [
                'icon'  => 'info',
                'title' => 'Pagamento já confirmado',
                'text'  => 'Já existe um pagamento aprovado para este curso.',
            ]);
        }

        // Cria registo local
        $paymentData = [
            'id_user_payment'       => $user->id,
            'id_course_payment'     => $courseId,
            'amount_payment'        => $amount,
            'status_payment'        => 'Pendente',
            'reference_payment'     => $reference,
            'id_enrollment_payment' => null,
            'proof_file_payment'    => null,
            'approved_by_payment'   => null,
            'created_at'            => date('Y-m-d H:i:s'),
            'updated_at'            => date('Y-m-d H:i:s'),
        ];

        $paymentId = $paymentModel->insert($paymentData, true);

        if (!$paymentId) {
            return redirect()->back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Erro interno',
                'text'  => 'Não foi possível criar o registo do pagamento.',
            ]);
        }

        $payload = [
            'value'                 => $amount,
            'client_number'         => $clientNumber,
            'agent_id'              => (int) getenv('MPESA_AGENT_ID'),
            'third_party_reference' => $reference,
            'transaction_reference' => 'TXN' . time(),
        ];

        try {
            $apiKey    = getenv('MPESA_API_KEY');
            $publicKey = getenv('MPESA_PUBLIC_KEY');
            $env       = getenv('MPESA_ENV') ?: 'development';

            if (empty($apiKey) || empty($publicKey)) {
                throw new \RuntimeException('Credenciais do M-Pesa não configuradas.');
            }

            $mpesa = Mpesa::init($apiKey, $publicKey, $env);

            $response = $mpesa->c2b($payload);

            log_message('info', 'Resposta inicial M-Pesa: ' . json_encode($response, JSON_UNESCAPED_UNICODE));

            // Mantém Pendente até o callback confirmar
            $paymentModel->update($paymentId, [
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()->with('swal', [
                'icon'  => 'success',
                'title' => 'Pedido enviado',
                'text'  => 'Confirme o pagamento no seu telefone. A inscrição será ativada após confirmação.',
            ]);
        } catch (\Throwable $e) {
            $paymentModel->update($paymentId, [
                'status_payment' => 'Rejeitado',
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            log_message('error', 'Erro M-Pesa send(): ' . $e->getMessage());

            return redirect()->back()->withInput()->with('swal', [
                'icon'  => 'error',
                'title' => 'Falha no pagamento',
                'text'  => 'Não foi possível iniciar o pagamento M-Pesa.',
            ]);
        }
    }
}