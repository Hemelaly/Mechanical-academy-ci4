<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\PaymentModel;
use App\Services\CheckoutEnrollmentService;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use emagombe\Mpesa;

class MpesaController extends Controller
{
    private CheckoutEnrollmentService $checkoutService;

    public function __construct()
    {
        $this->checkoutService = new CheckoutEnrollmentService();
    }

    public function send(): ResponseInterface
    {
        helper(['form']);

        $authenticatedUser = auth()->user();
        $rules = [
            'id_course'     => 'required|integer',
            'client_number' => 'required|min_length[9]|max_length[15]',
        ];

        if (! $authenticatedUser) {
            $rules['email'] = 'required|valid_email';
            $rules['username'] = 'required|min_length[3]|max_length[100]';
        }

        if (! $this->validate($rules)) {
            return $this->jsonResponse(422, [
                'ok'   => false,
                'swal' => [
                    'icon'  => 'error',
                    'title' => 'Dados invalidos',
                    'text'  => implode(' | ', $this->validator->getErrors()),
                ],
            ]);
        }

        $courseId = (int) $this->request->getPost('id_course');
        $course = (new CourseModel())
            ->where('status_course', 'Ativo')
            ->find($courseId);

        if (! $course) {
            return $this->jsonResponse(404, [
                'ok'   => false,
                'swal' => [
                    'icon'  => 'error',
                    'title' => 'Curso indisponivel',
                    'text'  => 'Nao foi possivel localizar este curso para pagamento.',
                ],
            ]);
        }

        if ($authenticatedUser && strtolower(trim((string) ($authenticatedUser->role ?? ''))) !== 'student') {
            return $this->jsonResponse(403, [
                'ok'   => false,
                'swal' => [
                    'icon'  => 'warning',
                    'title' => 'Acesso bloqueado',
                    'text'  => 'Apenas alunos podem concluir esta compra.',
                ],
            ]);
        }

        $email = trim(strtolower((string) ($authenticatedUser->email ?? $this->request->getPost('email'))));
        $fullName = trim((string) ($authenticatedUser->username ?? $this->request->getPost('username')));
        $existingUser = $authenticatedUser ?: $this->checkoutService->findUserByEmail($email);

        if (
            ! $authenticatedUser
            && $existingUser
            && ! in_array(strtolower(trim((string) ($existingUser->role ?? 'student'))), ['', 'student'], true)
        ) {
            return $this->jsonResponse(409, [
                'ok'   => false,
                'swal' => [
                    'icon'  => 'warning',
                    'title' => 'Conta incompativel',
                    'text'  => 'Este email pertence a uma conta que nao pode comprar cursos como estudante.',
                ],
            ]);
        }

        $targetUserId = (int) ($existingUser->id ?? 0);
        if ($targetUserId > 0 && $this->checkoutService->hasActiveEnrollment($targetUserId, $courseId)) {
            return $this->jsonResponse(409, [
                'ok'           => false,
                'redirect_url' => $authenticatedUser
                    ? $this->checkoutService->buildCourseAccessPath($courseId)
                    : site_url('login'),
                'swal'         => [
                    'icon'  => 'info',
                    'title' => 'Inscricao existente',
                    'text'  => $authenticatedUser
                        ? 'Voce ja esta inscrito neste curso.'
                        : 'Este email ja esta inscrito neste curso. Entre na conta para acessar.',
                ],
            ]);
        }

        $paymentModel = new PaymentModel();

        if ($targetUserId > 0) {
            $existingApproved = $paymentModel
                ->where('id_user_payment', $targetUserId)
                ->where('id_course_payment', $courseId)
                ->where('status_payment', 'Aprovado')
                ->orderBy('id_payment', 'DESC')
                ->first();

            if ($existingApproved) {
                try {
                    $result = $this->checkoutService->finalizeApprovedPayment(
                        (int) $existingApproved->id_payment,
                        $courseId,
                        (string) $course->title_course,
                        $email,
                        $fullName,
                        $authenticatedUser
                    );

                    return $this->jsonResponse(200, [
                        'ok'           => true,
                        'status'       => 'approved',
                        'redirect_url' => $result['redirect_url'],
                        'swal'         => $this->successSwal((string) $course->title_course),
                    ]);
                } catch (\Throwable $e) {
                    log_message('error', 'Falha ao reaproveitar pagamento aprovado no checkout: ' . $e->getMessage());

                    return $this->jsonResponse(500, [
                        'ok'   => false,
                        'swal' => [
                            'icon'  => 'warning',
                            'title' => 'Pagamento encontrado',
                            'text'  => 'Ha um pagamento aprovado para este curso, mas nao foi possivel liberar o acesso agora.',
                        ],
                    ]);
                }
            }
        }

        $clientNumber = $this->normalizeClientNumber((string) $this->request->getPost('client_number'));
        if ($clientNumber === null) {
            return $this->jsonResponse(422, [
                'ok'   => false,
                'swal' => [
                    'icon'  => 'error',
                    'title' => 'Numero invalido',
                    'text'  => 'Informe um numero M-Pesa valido para concluir a cobranca.',
                ],
            ]);
        }

        try {
            $preparedUser = $this->checkoutService->prepareCheckoutUser(
                $email,
                $fullName,
                $authenticatedUser
            );
            $targetUserId = (int) $preparedUser->id;
        } catch (\Throwable $e) {
            log_message('error', 'Falha ao preparar usuario do checkout: ' . $e->getMessage());

            return $this->jsonResponse(409, [
                'ok'   => false,
                'swal' => [
                    'icon'  => 'warning',
                    'title' => 'Conta nao preparada',
                    'text'  => 'Nao foi possivel preparar a conta para concluir esta compra. Confira o email informado.',
                ],
            ]);
        }

        $amount = (float) $course->price_course;
        $now = date('Y-m-d H:i:s');
        $reference = $this->buildReference($courseId, $targetUserId);

        $paymentId = $paymentModel->insert([
            'id_user_payment'       => $targetUserId,
            'id_course_payment'     => $courseId,
            'id_enrollment_payment' => 0,
            'amount_payment'        => $amount,
            'status_payment'        => 'Pendente',
            'reference_payment'     => $reference,
            'approved_by_payment'   => 0,
            'created_at'            => $now,
            'updated_at'            => $now,
        ], true);

        if ($paymentId === false) {
            return $this->jsonResponse(500, [
                'ok'   => false,
                'swal' => [
                    'icon'  => 'error',
                    'title' => 'Erro interno',
                    'text'  => implode(', ', $paymentModel->errors() ?: ['Nao foi possivel preparar o pagamento.']),
                ],
            ]);
        }

        try {
            $apiKey = getenv('MPESA_API_KEY');
            $publicKey = getenv('MPESA_PUBLIC_KEY');
            $agentId = (int) getenv('MPESA_AGENT_ID');
            $environment = strtolower(trim((string) (getenv('MPESA_ENV') ?: 'production')));

            if (! $apiKey || ! $publicKey || $agentId <= 0) {
                throw new \RuntimeException('Credenciais do M-Pesa nao configuradas.');
            }

            $mpesa = Mpesa::init($apiKey, $publicKey, $environment);
            $response = $mpesa->c2b([
                'value'                 => $amount,
                'client_number'         => $clientNumber,
                'agent_id'              => $agentId,
                'third_party_reference' => $reference,
                'transaction_reference' => 'TXN' . time() . $paymentId,
            ]);

            $mpesaData = $this->parseMpesaResponse($response);

            log_message('info', 'Checkout M-Pesa: ' . json_encode([
                'payment_id' => $paymentId,
                'reference'  => $reference,
                'response'   => $mpesaData,
            ], JSON_UNESCAPED_UNICODE));

            if ($this->hasGatewayError($mpesaData)) {
                $paymentModel->update($paymentId, [
                    'status_payment'        => 'Rejeitado',
                    'id_enrollment_payment' => 0,
                    'approved_by_payment'   => 0,
                    'updated_at'            => date('Y-m-d H:i:s'),
                ]);

                log_message('error', 'Checkout M-Pesa retornou erro tecnico: ' . json_encode([
                    'payment_id' => $paymentId,
                    'reference'  => $reference,
                    'response'   => $mpesaData,
                ], JSON_UNESCAPED_UNICODE));

                return $this->jsonResponse(502, [
                    'ok'     => false,
                    'status' => 'gateway_error',
                    'mpesa'  => $mpesaData,
                    'swal'   => $this->buildFailureSwal($mpesaData),
                ]);
            }

            if (! $this->isAcceptedMpesaResponse($mpesaData)) {
                $paymentModel->update($paymentId, [
                    'status_payment'        => 'Rejeitado',
                    'id_enrollment_payment' => 0,
                    'approved_by_payment'   => 0,
                    'updated_at'            => date('Y-m-d H:i:s'),
                ]);

                return $this->jsonResponse(409, [
                    'ok'     => false,
                    'status' => 'rejected',
                    'mpesa'  => $mpesaData,
                    'swal'   => $this->buildFailureSwal($mpesaData),
                ]);
            }
        } catch (\Throwable $e) {
            $paymentModel->update($paymentId, [
                'status_payment' => 'Rejeitado',
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            log_message('error', 'Erro ao iniciar pagamento M-Pesa: ' . $e->getMessage());

            return $this->jsonResponse(500, [
                'ok'   => false,
                'swal' => [
                    'icon'  => 'error',
                    'title' => 'Falha no pagamento',
                    'text'  => 'Nao foi possivel comunicar com o M-Pesa agora. Tente novamente.',
                ],
            ]);
        }

        return $this->jsonResponse(202, [
            'ok'               => true,
            'status'           => 'pending_confirmation',
            'payment_id'       => (int) $paymentId,
            'reference'        => $reference,
            'query_reference'  => $this->extractQueryReference($mpesaData),
            'status_check_url' => site_url('mpesa/status'),
            'swal'             => $this->pendingConfirmationSwal(),
        ]);
    }

    public function status(): ResponseInterface
    {
        helper(['form']);

        $authenticatedUser = auth()->user();
        $rules = [
            'payment_id'      => 'required|integer',
            'reference'       => 'required',
            'query_reference' => 'required',
        ];

        if (! $authenticatedUser) {
            $rules['email'] = 'required|valid_email';
            $rules['username'] = 'required|min_length[3]|max_length[100]';
        }

        if (! $this->validate($rules)) {
            return $this->jsonResponse(422, [
                'ok'   => false,
                'swal' => [
                    'icon'  => 'error',
                    'title' => 'Dados invalidos',
                    'text'  => implode(' | ', $this->validator->getErrors()),
                ],
            ]);
        }

        $paymentId = (int) $this->request->getPost('payment_id');
        $reference = trim((string) $this->request->getPost('reference'));
        $queryReference = trim((string) $this->request->getPost('query_reference'));
        $email = trim(strtolower((string) ($authenticatedUser->email ?? $this->request->getPost('email'))));
        $fullName = trim((string) ($authenticatedUser->username ?? $this->request->getPost('username')));

        $paymentModel = new PaymentModel();
        $payment = $paymentModel
            ->where('id_payment', $paymentId)
            ->where('reference_payment', $reference)
            ->first();

        if (! $payment) {
            return $this->jsonResponse(404, [
                'ok'   => false,
                'swal' => [
                    'icon'  => 'error',
                    'title' => 'Pagamento nao encontrado',
                    'text'  => 'Nao foi possivel localizar este pagamento para confirmar o estado atual.',
                ],
            ]);
        }

        $paymentStatus = strtolower(trim((string) ($payment->status_payment ?? '')));
        if ($paymentStatus === 'aprovado') {
            return $this->finalizeConfirmedPayment($payment, $email, $fullName, $authenticatedUser);
        }

        if ($paymentStatus === 'rejeitado') {
            return $this->jsonResponse(409, [
                'ok'     => false,
                'status' => 'rejected',
                'swal'   => [
                    'icon'  => 'error',
                    'title' => 'Pagamento rejeitado',
                    'text'  => 'O pagamento foi rejeitado pelo M-Pesa.',
                ],
            ]);
        }

        try {
            $apiKey = getenv('MPESA_API_KEY');
            $publicKey = getenv('MPESA_PUBLIC_KEY');
            $agentId = (int) getenv('MPESA_AGENT_ID');
            $environment = strtolower(trim((string) (getenv('MPESA_ENV') ?: 'production')));

            if (! $apiKey || ! $publicKey || $agentId <= 0) {
                throw new \RuntimeException('Credenciais do M-Pesa nao configuradas.');
            }

            $mpesa = Mpesa::init($apiKey, $publicKey, $environment);
            $response = $mpesa->status([
                'transaction_id'        => $queryReference,
                'third_party_reference' => $reference,
                'agent_id'              => $agentId,
            ]);

            $mpesaData = $this->parseMpesaResponse($response);

            log_message('info', 'Checkout M-Pesa status: ' . json_encode([
                'payment_id'       => $paymentId,
                'reference'        => $reference,
                'query_reference'  => $queryReference,
                'response'         => $mpesaData,
            ], JSON_UNESCAPED_UNICODE));

            if ($this->hasGatewayError($mpesaData)) {
                return $this->jsonResponse(502, [
                    'ok'     => false,
                    'status' => 'gateway_error',
                    'mpesa'  => $mpesaData,
                    'swal'   => $this->buildFailureSwal($mpesaData),
                ]);
            }

            if ($this->isCompletedMpesaStatusResponse($mpesaData)) {
                return $this->finalizeConfirmedPayment($payment, $email, $fullName, $authenticatedUser);
            }

            if ($this->isRejectedMpesaStatusResponse($mpesaData)) {
                $paymentModel->update($paymentId, [
                    'status_payment'        => 'Rejeitado',
                    'id_enrollment_payment' => 0,
                    'approved_by_payment'   => 0,
                    'updated_at'            => date('Y-m-d H:i:s'),
                ]);

                return $this->jsonResponse(409, [
                    'ok'     => false,
                    'status' => 'rejected',
                    'mpesa'  => $mpesaData,
                    'swal'   => $this->buildFailureSwal($mpesaData),
                ]);
            }

            return $this->jsonResponse(202, [
                'ok'               => true,
                'status'           => 'pending_confirmation',
                'payment_id'       => $paymentId,
                'reference'        => $reference,
                'query_reference'  => $queryReference,
                'status_check_url' => site_url('mpesa/status'),
                'mpesa'            => $mpesaData,
                'swal'             => $this->pendingConfirmationSwal(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Erro ao consultar estado do M-Pesa: ' . $e->getMessage());

            return $this->jsonResponse(500, [
                'ok'   => false,
                'swal' => [
                    'icon'  => 'error',
                    'title' => 'Falha na confirmacao',
                    'text'  => 'Nao foi possivel confirmar o estado do pagamento agora. Tente novamente em instantes.',
                ],
            ]);
        }
    }

    private function jsonResponse(int $statusCode, array $payload): ResponseInterface
    {
        $payload['csrf'] = csrf_hash();

        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($payload);
    }

    private function normalizeClientNumber(string $clientNumber): ?string
    {
        $digits = preg_replace('/\D+/', '', $clientNumber) ?? '';
        if ($digits === '') {
            return null;
        }

        if (strpos($digits, '258') !== 0) {
            $digits = '258' . ltrim($digits, '0');
        }

        if (strlen($digits) < 12 || strlen($digits) > 15) {
            return null;
        }

        return $digits;
    }

    private function buildReference(int $courseId, int $userId): string
    {
        return 'CRS' . $courseId . 'U' . max(0, $userId) . 'T' . time() . random_int(10, 99);
    }

    private function parseMpesaResponse($response): array
    {
        if (is_array($response)) {
            return $response;
        }

        if (is_object($response)) {
            return json_decode(json_encode($response), true) ?: [];
        }

        if (is_string($response)) {
            $decoded = json_decode($response, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            return ['raw' => $response];
        }

        return [];
    }

    private function finalizeConfirmedPayment(object $payment, string $email, string $fullName, ?object $authenticatedUser = null): ResponseInterface
    {
        $course = (new CourseModel())->find((int) $payment->id_course_payment);
        if (! $course) {
            return $this->jsonResponse(404, [
                'ok'   => false,
                'swal' => [
                    'icon'  => 'error',
                    'title' => 'Curso indisponivel',
                    'text'  => 'O pagamento foi localizado, mas o curso associado nao esta disponivel.',
                ],
            ]);
        }

        try {
            $result = $this->checkoutService->finalizeApprovedPayment(
                (int) $payment->id_payment,
                (int) $payment->id_course_payment,
                (string) $course->title_course,
                $email,
                $fullName,
                $authenticatedUser
            );
        } catch (\Throwable $e) {
            (new PaymentModel())->update((int) $payment->id_payment, [
                'status_payment'        => 'Aprovado',
                'id_enrollment_payment' => 0,
                'approved_by_payment'   => 0,
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);

            log_message('critical', 'Pagamento aprovado sem finalizar inscricao: ' . $e->getMessage());

            return $this->jsonResponse(500, [
                'ok'   => false,
                'swal' => [
                    'icon'  => 'warning',
                    'title' => 'Pagamento confirmado',
                    'text'  => 'O pagamento foi confirmado, mas a inscricao nao terminou automaticamente. Nossa equipa vai validar o acesso.',
                ],
            ]);
        }

        return $this->jsonResponse(200, [
            'ok'           => true,
            'status'       => 'approved',
            'redirect_url' => $result['redirect_url'],
            'swal'         => $this->successSwal((string) $course->title_course),
        ]);
    }

    private function isAcceptedMpesaResponse(array $mpesaData): bool
    {
        return strtoupper((string) ($mpesaData['output_ResponseCode'] ?? '')) === 'INS-0';
    }

    private function isCompletedMpesaStatusResponse(array $mpesaData): bool
    {
        $transactionStatus = strtolower(trim((string) ($mpesaData['output_ResponseTransactionStatus'] ?? '')));

        return in_array($transactionStatus, ['completed', 'success', 'successful', 'approved'], true);
    }

    private function isRejectedMpesaStatusResponse(array $mpesaData): bool
    {
        $transactionStatus = strtolower(trim((string) ($mpesaData['output_ResponseTransactionStatus'] ?? '')));
        if (in_array($transactionStatus, ['failed', 'cancelled', 'canceled', 'rejected', 'declined', 'timeout', 'timed out'], true)) {
            return true;
        }

        $code = strtoupper(trim((string) ($mpesaData['output_ResponseCode'] ?? '')));
        if ($code !== '' && $code !== 'INS-0') {
            return true;
        }

        $description = strtolower(trim((string) ($mpesaData['output_ResponseDesc'] ?? '')));

        return str_contains($description, 'insufficient')
            || str_contains($description, 'saldo insuficiente')
            || str_contains($description, 'cancel')
            || str_contains($description, 'declin')
            || str_contains($description, 'failed');
    }

    private function hasGatewayError(array $mpesaData): bool
    {
        return trim((string) ($mpesaData['output_error'] ?? '')) !== '';
    }

    private function extractQueryReference(array $mpesaData): ?string
    {
        $queryReference = trim((string) ($mpesaData['output_ConversationID'] ?? ''));
        if ($queryReference !== '') {
            return $queryReference;
        }

        $queryReference = trim((string) ($mpesaData['output_TransactionID'] ?? ''));

        return $queryReference !== '' ? $queryReference : null;
    }

    private function buildFailureSwal(array $mpesaData): array
    {
        $gatewayError = trim((string) ($mpesaData['output_error'] ?? ''));
        $gatewayErrorLower = strtolower($gatewayError);

        if ($gatewayError !== '') {
            if (str_contains($gatewayErrorLower, 'bad api key')) {
                return [
                    'icon'  => 'error',
                    'title' => 'Configuracao invalida do M-Pesa',
                    'text'  => 'O gateway recusou a API Key do ambiente atual. Verifique MPESA_API_KEY e MPESA_ENV.',
                ];
            }

            if (str_contains($gatewayErrorLower, 'bad public key')) {
                return [
                    'icon'  => 'error',
                    'title' => 'Chave publica invalida',
                    'text'  => 'O gateway recusou a chave publica configurada para o M-Pesa. Verifique MPESA_PUBLIC_KEY e MPESA_ENV.',
                ];
            }

            return [
                'icon'  => 'error',
                'title' => 'Falha no gateway M-Pesa',
                'text'  => $gatewayError,
            ];
        }

        $code = strtoupper(trim((string) ($mpesaData['output_ResponseCode'] ?? '')));
        $description = trim((string) ($mpesaData['output_ResponseDesc'] ?? 'Pagamento rejeitado pelo M-Pesa.'));
        $descriptionLower = strtolower($description);

        if (str_contains($descriptionLower, 'insufficient') || str_contains($descriptionLower, 'saldo insuficiente')) {
            return [
                'icon'  => 'warning',
                'title' => 'Saldo insuficiente',
                'text'  => 'O pagamento foi negado por saldo insuficiente na conta M-Pesa.',
            ];
        }

        if ($code === 'INS-2051' || str_contains($descriptionLower, 'msisdn invalid')) {
            return [
                'icon'  => 'error',
                'title' => 'Numero invalido',
                'text'  => 'O numero informado nao e valido para M-Pesa. Confira e tente novamente.',
            ];
        }

        if (str_contains($descriptionLower, 'cancel') || str_contains($descriptionLower, 'declin') || str_contains($descriptionLower, 'failed')) {
            return [
                'icon'  => 'error',
                'title' => 'Pagamento negado',
                'text'  => $description,
            ];
        }

        return [
            'icon'  => 'error',
            'title' => 'Pagamento rejeitado',
            'text'  => $description !== '' ? $description : 'Nao foi possivel concluir o pagamento.',
        ];
    }

    private function pendingConfirmationSwal(): array
    {
        return [
            'icon'  => 'info',
            'title' => 'Confirme o PIN',
            'text'  => 'O pedido foi enviado ao M-Pesa. Assim que confirmar o PIN no celular, vamos concluir a inscricao.',
        ];
    }

    private function successSwal(string $courseTitle): array
    {
        return [
            'icon'  => 'success',
            'title' => 'Pagamento confirmado',
            'text'  => 'Voce foi inscrito no curso ' . $courseTitle . '.',
        ];
    }
}
