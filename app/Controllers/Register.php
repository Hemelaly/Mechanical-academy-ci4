<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use CodeIgniter\Controller;
use CodeIgniter\Shield\Models\UserModel;
use emagombe\Mpesa;

class Register extends Controller
{
    public function createPendingUser($idCourse)
    {
        $db   = db_connect();
        $post = $this->request->getPost();

        // 0) Validação básica do form (sem mudar seus campos)
        helper(['form']);
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email'          => 'required|valid_email',
            'username'       => 'required|min_length[2]',
            'amount_payment' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'client_number'  => 'permit_empty|numeric|min_length[7]|max_length[15]',
        ]);
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $users      = model(UserModel::class);
        $isLoggedIn = auth()->loggedIn();

        // 1) Se o email já existe e NÃO está logado -> peça login
        $existingUser = $users->findByCredentials(['email' => $post['email'] ?? '']);
        if ($existingUser && !$isLoggedIn) {
            return redirect()->back()->with('swal', [
                'icon'  => 'warning',
                'title' => 'Já tem conta!',
                'text'  => 'Esse email já está registrado. Por favor, faça login para continuar.',
            ]);
        }

        // 2) Cria o pending_user
        $db->table('pending_users')->insert([
            'email'      => $post['email'],
            'username'   => $post['username'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $pendingId = $db->insertID();
        if (!$pendingId) {
            return redirect()->back()->with('error', 'Erro ao criar usuário pendente.');
        }

        // 3) Upload do comprovativo (opcional) — mantém seu fluxo atual
        $file     = $this->request->getFile('proof_file_payment');
        $filePath = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/img/', $newName);
            $filePath = 'assets/img/' . $newName;
        }

        // 4) Preparar dados para payment + (opcional) M-Pesa
        $paymentModel = new PaymentModel();
        $amount       = (float)($post['amount_payment'] ?? 0);
        $clientNumber = trim((string)($post['client_number'] ?? '')); // se vier, usa M-Pesa
        $useMpesa     = ($clientNumber !== '' && $amount > 0);

        // status padrão (sem M-Pesa continua igual)
        $statusPayment = 'Pendente';

        // 5) Se veio número M-Pesa, tentar cobrança C2B — sem criar novas colunas
        $mpesaResponseArr = null;
        if ($useMpesa) {
            $apiKey    = getenv('MPESA_API_KEY');
            $publicKey = getenv('MPESA_PUBLIC_KEY');
            $env       = getenv('MPESA_ENV') ?: 'development';

            if (!$apiKey || !$publicKey) {
                return redirect()->back()->withInput()->with(
                    'error',
                    'Configuração M-Pesa ausente: defina MPESA_API_KEY e MPESA_PUBLIC_KEY no .env.'
                );
            }

            // refs temporárias (não vão ao banco — só para o arquivo de log)
            $thirdPartyRef  = (string) random_int(10000, 99999);
            $transactionRef = (string) time();

            try {
                // Ajuste o namespace da sua SDK se for diferente
                $mpesa   = Mpesa::init($apiKey, $publicKey, $env);
                $payload = [
                    'value'                 => $amount,
                    'client_number'         => $clientNumber,
                    'agent_id'              => 171717,          // seu agent_id
                    'third_party_reference' => $thirdPartyRef,
                    'transaction_reference' => $transactionRef,
                ];

                $response         = $mpesa->c2b($payload);
                $mpesaResponseArr = json_decode(json_encode($response), true);

                // Heurística de sucesso (ajuste conforme sua SDK)
                $statusField = strtolower((string)($mpesaResponseArr['status'] ?? ''));
                $codeField   = strtolower((string)($mpesaResponseArr['responseCode'] ?? $mpesaResponseArr['code'] ?? ''));
                $okCodes     = ['0', '00', 'ins-0', 'success', 'ok', 'completed'];

                if (in_array($statusField, ['success', 'ok', 'completed'], true) || in_array($codeField, $okCodes, true)) {
                    $statusPayment = 'Aprovado';
                } else {
                    $statusPayment = 'Pendente'; // pode trocar para 'Falhou' se preferir
                }
            } catch (\Throwable $e) {
                $statusPayment    = 'Falhou';
                $mpesaResponseArr = ['exception' => true, 'message' => $e->getMessage()];
            }
        }

        // 6) Criar o registro em payments — APENAS com os campos que você já tem
        $dataPayment = [
            'id_user_payment'    => $pendingId,
            'id_course_payment'  => $idCourse,
            'amount_payment'     => $amount,
            'status_payment'     => $statusPayment,   // Aprovado/Pendente/Falhou
            'proof_file_payment' => $filePath,        // mantém seu upload (se tiver)
            'created_at'         => date('Y-m-d H:i:s'),
        ];
        $idPayment = $paymentModel->insert($dataPayment, true);
        if ($idPayment === false) {
            return redirect()->back()->with('error', implode(', ', $paymentModel->errors()));
        }

        // 7) Referência interna (mantido)
        $reference = 'PAY-' . date('Y') . '-' . str_pad($idPayment, 6, '0', STR_PAD_LEFT);
        $paymentModel->update($idPayment, ['reference_payment' => $reference]);

        // 8) Se fizemos M-Pesa, guardar o retorno em arquivo (fora do banco)
        if ($useMpesa) {
            $dir = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'mpesa';
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            // Salva como JSON com o ID do pagamento
            $logFile = $dir . DIRECTORY_SEPARATOR . 'payment_' . $idPayment . '.json';
            @file_put_contents($logFile, json_encode([
                'created_at'   => date('c'),
                'payment_id'   => $idPayment,
                'reference'    => $reference,
                'request'      => [
                    'amount'        => $amount,
                    'client_number' => $clientNumber,
                ],
                'response'     => $mpesaResponseArr,
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }

        // 9) Mensagem para o usuário
        if ($useMpesa) {
            if ($statusPayment === 'Aprovado') {
                return redirect()->to("/checkout/{$idCourse}")->with('swal', [
                    'icon'  => 'success',
                    'title' => 'Pagamento aprovado!',
                    'text'  => 'Recebemos a confirmação da M-Pesa. Sua inscrição será liberada em instantes.'
                ]);
            }
            if ($statusPayment === 'Falhou') {
                $err = $mpesaResponseArr['message'] ?? 'Não foi possível concluir a transação M-Pesa.';
                return redirect()->to("/checkout/{$idCourse}")->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Falha no pagamento',
                    'text'  => $err
                ]);
            }
            // Pendente
            return redirect()->to("/checkout/{$idCourse}")->with('swal', [
                'icon'  => 'info',
                'title' => 'Transação em processamento',
                'text'  => 'Seu pedido foi enviado à M-Pesa. Caso já tenha aprovado no celular, a confirmação pode levar alguns instantes.'
            ]);
        }

        // Fluxo sem M-Pesa (igual ao seu)
        return redirect()->to("/checkout/{$idCourse}")
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Pedido enviado!',
                'text'  => 'O comprovativo foi submetido e será analisado pelo instrutor. Após a confirmação, enviaremos um link para o seu email.'
            ]);
    }
}
