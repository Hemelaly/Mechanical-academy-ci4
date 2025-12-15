<?php

namespace App\Controllers;

use App\Models\UserModel;          // Shield
use App\Entities\User;             // Shield Entity (ajuste se necessário)
use App\Models\PaymentModel;
use App\Models\StudentModel;
use App\Models\EnrollmentModel;
use App\Models\PendingUserModel;   // para o fluxo antigo
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Entities\User as EntitiesUser;
use CodeIgniter\Shield\Models\UserModel as ModelsUserModel;
use emagombe\Mpesa;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class Register extends Controller
{
    public function createPendingUser($idCourse)
    {
        $db   = db_connect();
        $post = $this->request->getPost();

        // 0) Validação básica do form
        helper(['form', 'text']);
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email'          => 'required|valid_email',
            'username'       => 'required|min_length[2]',
            'amount_payment' => 'permit_empty|numeric|greater_than[0]', // > 0 quando informado
            'client_number'  => 'permit_empty|numeric|min_length[7]|max_length[15]',
        ]);
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // 1) Models
        $users        = model(ModelsUserModel::class);
        $studentModel = new StudentModel();
        $enrollModel  = new EnrollmentModel();
        $paymentModel = new PaymentModel();
        $pendingModel = new PendingUserModel();

        // 2) Inputs normalizados
        $email        = trim((string)($post['email'] ?? ''));
        $username     = trim((string)($post['username'] ?? ''));
        $amount       = (float)($post['amount_payment'] ?? 0);
        $clientNumber = trim((string)($post['client_number'] ?? ''));
        $useMpesa     = ($clientNumber !== '' && $amount > 0);

        // 3) Upload (opcional)
        $file     = $this->request->getFile('proof_file_payment');
        $filePath = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $destDir = FCPATH . 'assets/img/';
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0775, true);
            }
            $newName = $file->getRandomName();
            $file->move($destDir, $newName);
            $filePath = 'assets/img/' . $newName;
        }

        // 4) Cobrança M-Pesa (se aplicável)
        $mpesaSuccess    = false;
        $mpesaResponseArr = null;
        $statusField     = '';
        $codeField       = '';

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

            // Referências (para o gateway e auditoria)
            $thirdPartyRef  = (string) random_int(10000, 99999);
            $transactionRef = (string) time();

            try {
                $mpesa   = Mpesa::init($apiKey, $publicKey, $env);
                $payload = [
                    'value'                 => $amount,
                    'client_number'         => $clientNumber,
                    'agent_id'              => 171717, // TODO: seu agent_id
                    'third_party_reference' => $thirdPartyRef,
                    'transaction_reference' => $transactionRef,
                ];

                $response         = $mpesa->c2b($payload);
                $mpesaResponseArr = json_decode(json_encode($response), true);

                $mpesaSay = json_decode($mpesaResponseArr);

                if ($mpesaSay->output_ResponseDesc == 'Request processed successfully') {
                    $mpesaSuccess = true;
                }
            } catch (\Throwable $e) {
                $mpesaSuccess     = false;
                $mpesaResponseArr = ['exception' => true, 'message' => $e->getMessage()];
                log_message('error', 'M-Pesa exception: ' . $e->getMessage());
            }

            // Log do braseiro pra depuração
            log_message('info', 'M-Pesa raw: ' . print_r($mpesaResponseArr, true));
            log_message('info', "M-Pesa parsed: code={$codeField} status={$statusField} success=" . ($mpesaSuccess ? '1' : '0'));
        }

        // ========== FLUXO 1: M-PESA SUCESSO -> cria tudo direto ==========
        if ($useMpesa && $mpesaSuccess) {

            $db->transStart();

            // 1) Obter ou criar o User (Shield)
            $existingUser = $users->findByCredentials(['email' => $email]);
            if ($existingUser) {
                $userId   = (int)$existingUser->id;
                $userName = $existingUser->username ?? $username;
            } else {
                $userEntity            = new EntitiesUser(['username' => $username]);
                $userEntity->email     = $email;
                $tempPassword          = random_string('alnum', 12);
                $userEntity->password  = $tempPassword; // Shield deve hashear via mutator
                $users->save($userEntity);
                $userId = (int)$users->getInsertID();

                // Token de reset de senha
                $token   = bin2hex(random_bytes(16));
                $expires = date('Y-m-d H:i:s', strtotime('+1 day'));
                $db->table('password_resets')->insert([
                    'user_id'    => $userId,
                    'token'      => $token,
                    'expires_at' => $expires
                ]);

                // E-mail com link de reset
                try {
                    $link  = site_url("reset-password?token={$token}");
                    $emailSrv = \Config\Services::email();
                    $emailSrv->setTo($email);
                    $emailSrv->setSubject('Crie sua senha e acesse o curso');
                    $emailSrv->setMessage("
                    <div style='
                        font-family: Arial, Helvetica, sans-serif;
                        background-color: #f4f6f8;
                        padding: 30px;
                    '>
                        <div style='
                            max-width: 600px;
                            margin: auto;
                            background-color: #ffffff;
                            border-radius: 8px;
                            padding: 30px;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                        '>
                            <h2 style='color: #2c3e50; margin-top: 0;'>
                                Olá {$username} 👋
                            </h2>

                            <p style='color: #555; font-size: 15px; line-height: 1.6;'>
                                Temos boas notícias para si!
                            </p>

                            <p style='color: #555; font-size: 15px; line-height: 1.6;'>
                                O seu <strong>pagamento foi aprovado</strong> e a sua
                                <strong>matrícula será ativada</strong> automaticamente.
                            </p>

                            <p style='color: #555; font-size: 15px; line-height: 1.6;'>
                                Para começar, crie a sua senha clicando no botão abaixo:
                            </p>

                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='{$link}'
                                style='
                                    background-color: #1abc9c;
                                    color: #ffffff;
                                    text-decoration: none;
                                    padding: 14px 28px;
                                    border-radius: 6px;
                                    font-weight: bold;
                                    display: inline-block;
                                '>
                                    Criar minha senha
                                </a>
                            </div>

                            <p style='color: #777; font-size: 13px; line-height: 1.6;'>
                                Se o botão não funcionar, copie e cole o link abaixo no seu navegador:
                            </p>

                            <p style='word-break: break-all; font-size: 13px; color: #1abc9c;'>
                                {$link}
                            </p>

                            <hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>

                            <p style='color: #999; font-size: 12px;'>
                                Se não solicitou este acesso, por favor ignore este email.
                            </p>

                            <p style='color: #999; font-size: 12px;'>
                                Atenciosamente,<br>
                                <strong>Equipe da Plataforma</strong>
                            </p>
                        </div>
                    </div>
                    ");
                    $emailSrv->setMailType('html');
                    $emailSrv->send();
                } catch (\Throwable $e) {
                    log_message('warning', 'Falha ao enviar e-mail de reset: ' . $e->getMessage());
                }
            }

            // 2) Garantir Student
            $student = $studentModel->where('id_user_student', $userId)->first();
            if ($student) {
                $studentId = (int)$student->id_student;
            } else {
                $studentId = (int)$studentModel->insert([
                    'id_user_student' => $userId,
                    'name_student'    => $username,
                    'email_student'   => $email,
                ]);
            }

            // 3) Payment (Aprovado)
            $idPayment = $paymentModel->insert([
                'id_user_payment'    => $userId,
                'id_course_payment'  => $idCourse,
                'amount_payment'     => $amount,
                'status_payment'     => 'Aprovado',
                'proof_file_payment' => $filePath,
                'created_at'         => date('Y-m-d H:i:s'),
            ], true);
            if ($idPayment === false) {
                $db->transRollback();
                return redirect()->back()->with('error', implode(', ', $paymentModel->errors()));
            }
            $reference = 'PAY-' . date('Y') . '-' . str_pad($idPayment, 6, '0', STR_PAD_LEFT);
            $paymentModel->update($idPayment, ['reference_payment' => $reference]);

            // 4) Enrollment (Ativa)
            $enroll = $enrollModel->insert([
                'id_course_enrollment'   => $idCourse,
                'id_student_enrollment'  => $userId,
                'status_enrollment'      => 'Ativa',
                'progress_enrollment'    => 0.00,
                'enrolled_at_enrollment' => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();
            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Falha ao concluir matrícula após pagamento.');
            }

            // Log do retorno M-Pesa (opcional)
            $dir = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'mpesa';
            if (!is_dir($dir)) @mkdir($dir, 0775, true);
            @file_put_contents(
                $dir . DIRECTORY_SEPARATOR . 'payment_' . $idPayment . '.json',
                json_encode([
                    'created_at' => date('c'),
                    'payment_id' => $idPayment,
                    'reference'  => $reference,
                    'request'    => [
                        'amount'        => $amount,
                        'client_number' => $clientNumber,
                    ],
                    'response'   => $mpesaResponseArr,
                    'parsed'     => ['code' => $codeField, 'status' => $statusField],
                    'final'      => 'approved_and_enrolled',
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            );

            return redirect()->back()->with('swal', [
                'icon'  => 'success',
                'title' => 'Pagamento aprovado!',
                'text'  => 'Verifique a sua caixa de email para concluir a esta inscrição!'
            ]);
        }

        // ========== FLUXO 2: SEM M-PESA OU NÃO-SUCESSO -> fluxo pendente ==========
        $isLoggedIn   = auth()->loggedIn();
        $existingUser = $users->findByCredentials(['email' => $email]);

        if ($existingUser && !$isLoggedIn) {
            return redirect()->back()->with('swal', [
                'icon'  => 'warning',
                'title' => 'Já tem conta!',
                'text'  => 'Esse email já está registrado. Por favor, faça login para continuar.',
            ]);
        }

        // 1) pending_users
        $db->table('pending_users')->insert([
            'email'      => $email,
            'username'   => $username,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $pendingId = (int)$db->insertID();
        if (!$pendingId) {
            return redirect()->back()->with('error', 'Erro ao criar usuário pendente.');
        }

        // 2) Payment pendente
        $statusPayment = 'Pendente';

        $idPayment = $paymentModel->insert([
            'id_user_payment'    => $pendingId,  // guarda o ID do pending
            'id_course_payment'  => $idCourse,
            'amount_payment'     => $amount,
            'status_payment'     => $statusPayment,
            'proof_file_payment' => $filePath,
            'created_at'         => date('Y-m-d H:i:s'),
        ], true);
        if ($idPayment === false) {
            return redirect()->back()->with('error', implode(', ', $paymentModel->errors()));
        }

        $reference = 'PAY-' . date('Y') . '-' . str_pad($idPayment, 6, '0', STR_PAD_LEFT);
        $paymentModel->update($idPayment, ['reference_payment' => $reference]);

        // 3) Log do gateway (se houve tentativa M-Pesa)
        if ($useMpesa) {
            $dir = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'mpesa';
            if (!is_dir($dir)) @mkdir($dir, 0775, true);
            @file_put_contents(
                $dir . DIRECTORY_SEPARATOR . 'payment_' . $idPayment . '.json',
                json_encode([
                    'created_at' => date('c'),
                    'payment_id' => $idPayment,
                    'reference'  => $reference,
                    'request'    => [
                        'amount'        => $amount,
                        'client_number' => $clientNumber,
                    ],
                    'response'   => $mpesaResponseArr,
                    'parsed'     => ['code' => $codeField, 'status' => $statusField],
                    'final'      => 'pending_flow',
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            );
        }

        return redirect()->to("/checkout/{$idCourse}")
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Pedido enviado!',
                'text'  => 'O comprovativo foi submetido e será analisado pelo instrutor. Dentro de 48 horas, enviaremos um link para o seu email.'
            ]);
    }
}
