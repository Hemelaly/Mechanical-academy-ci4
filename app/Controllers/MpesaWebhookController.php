<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\PaymentModel;
use App\Models\EnrollmentModel;

class MpesaWebhookController extends Controller
{
    public function receive()
    {
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        log_message('info', 'Webhook M-Pesa recebido: ' . $rawBody);

        if (!is_array($data)) {
            log_message('error', 'Webhook inválido: payload não é JSON válido.');
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Payload inválido',
            ]);
        }

        $responseCode        = $data['output_ResponseCode'] ?? null;
        $referencePayment    = $data['input_ThirdPartyReference'] ?? null;

        if (!$referencePayment) {
            log_message('error', 'Webhook sem input_ThirdPartyReference.');
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Referência ausente',
            ]);
        }

        $paymentModel = new PaymentModel();
        $enrollmentModel = new EnrollmentModel();

        $payment = $paymentModel
            ->where('reference_payment', $referencePayment)
            ->first();

        if (!$payment) {
            log_message('error', 'Pagamento não encontrado para referência: ' . $referencePayment);
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Pagamento não encontrado',
            ]);
        }

        // Como o teu model retorna object
        $paymentId   = $payment->id_payment;
        $userId      = $payment->id_user_payment;
        $courseId    = $payment->id_course_payment;

        if ($responseCode === 'INS-0') {
            // Verifica se já existe matrícula
            $existingEnrollment = $enrollmentModel
                ->where('id_student_enrollment', $userId)
                ->where('id_course_enrollment', $courseId)
                ->first();

            if ($existingEnrollment) {
                $enrollmentId = is_object($existingEnrollment)
                    ? $existingEnrollment->id_enrollment
                    : $existingEnrollment['id_enrollment'];
            } else {
                $enrollmentModel->insert([
                    'id_student_enrollment'  => $userId,
                    'id_course_enrollment'   => $courseId,
                    'status_enrollment'      => 'ativa',
                    'progress_enrollment'    => 0,
                    'enrolled_at_enrollment' => date('Y-m-d H:i:s'),
                ]);

                $enrollmentId = $enrollmentModel->getInsertID();
            }

            $paymentModel->update($paymentId, [
                'status_payment'        => 'Aprovado',
                'id_enrollment_payment' => $enrollmentId,
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setStatusCode(200)->setJSON([
                'status'  => 'ok',
                'message' => 'Pagamento aprovado e matrícula criada.',
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