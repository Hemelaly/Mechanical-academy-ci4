<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\PaymentModel;
use App\Services\CheckoutEnrollmentService;
use CodeIgniter\Controller;

class MpesaWebhookController extends Controller
{
    public function receive()
    {
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        log_message('info', 'Webhook M-Pesa recebido: ' . $rawBody);

        if (! is_array($data)) {
            log_message('error', 'Webhook invalido: payload nao e JSON valido.');

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Payload invalido',
            ]);
        }

        $responseCode = $data['output_ResponseCode'] ?? null;
        $referencePayment = $data['input_ThirdPartyReference'] ?? null;

        if (! $referencePayment) {
            log_message('error', 'Webhook sem input_ThirdPartyReference.');

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Referencia ausente',
            ]);
        }

        $paymentModel = new PaymentModel();
        $checkoutService = new CheckoutEnrollmentService();

        $payment = $paymentModel
            ->where('reference_payment', $referencePayment)
            ->first();

        if (! $payment) {
            log_message('error', 'Pagamento nao encontrado para referencia: ' . $referencePayment);

            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Pagamento nao encontrado',
            ]);
        }

        $currentStatus = strtolower((string) ($payment->status_payment ?? ''));
        if ($currentStatus === 'aprovado' || $currentStatus === 'rejeitado') {
            return $this->response->setStatusCode(200)->setJSON([
                'status'  => 'ok',
                'message' => 'Pagamento ja finalizado anteriormente.',
            ]);
        }

        $paymentId = (int) $payment->id_payment;
        $userId = (int) $payment->id_user_payment;
        $courseId = (int) $payment->id_course_payment;

        if ((string) $responseCode === 'INS-0' && $userId <= 0) {
            log_message('warning', 'Webhook M-Pesa recebeu aprovacao para pagamento sem usuario resolvido: ' . $referencePayment);

            return $this->response->setStatusCode(200)->setJSON([
                'status'  => 'ok',
                'message' => 'Pagamento aprovado, aguardando resolucao interna do usuario.',
            ]);
        }

        if ((string) $responseCode === 'INS-0') {
            $course = (new CourseModel())->find($courseId);
            $identity = db_connect()
                ->table('auth_identities')
                ->select('secret')
                ->where('user_id', $userId)
                ->where('type', 'email_password')
                ->orderBy('id', 'DESC')
                ->get()
                ->getRow();

            $student = db_connect()
                ->table('students')
                ->select('name_student, email_student')
                ->where('id_user_student', $userId)
                ->get()
                ->getRow();

            $email = trim(strtolower((string) ($identity->secret ?? $student->email_student ?? '')));
            $fullName = trim((string) ($student->name_student ?? 'Aluno'));

            if (! $course || $email === '') {
                log_message('warning', 'Webhook M-Pesa sem dados suficientes para finalizar checkout.', [
                    'payment_id' => $paymentId,
                    'course_id'  => $courseId,
                    'user_id'    => $userId,
                ]);

                return $this->response->setStatusCode(200)->setJSON([
                    'status'  => 'ok',
                    'message' => 'Pagamento aprovado, aguardando finalizacao interna.',
                ]);
            }

            try {
                $checkoutService->finalizeApprovedPayment(
                    $paymentId,
                    $courseId,
                    (string) $course->title_course,
                    $email,
                    $fullName,
                    null
                );
            } catch (\Throwable $e) {
                log_message('critical', 'Webhook M-Pesa aprovou pagamento, mas a inscricao falhou: ' . $e->getMessage());

                return $this->response->setStatusCode(200)->setJSON([
                    'status'  => 'ok',
                    'message' => 'Pagamento aprovado, mas a finalizacao interna falhou.',
                ]);
            }

            return $this->response->setStatusCode(200)->setJSON([
                'status'  => 'ok',
                'message' => 'Pagamento aprovado e matricula criada.',
            ]);
        }

        $paymentModel->update($paymentId, [
            'status_payment' => 'Rejeitado',
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 'ok',
            'message' => 'Pagamento rejeitado/sem sucesso.',
        ]);
    }
}
