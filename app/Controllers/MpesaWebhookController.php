<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\EnrollmentModel;

class MpesaWebhookController extends Controller
{
    public function receive()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        // Log para debug
        log_message('info', 'Webhook M-Pesa recebido: ' . print_r($data, true));

        // Checa se é sucesso
        if(isset($data['output_ResponseCode']) && $data['output_ResponseCode'] === 'INS-0') {
            $courseId = $data['input_ThirdPartyReference'] ?? null;
            $phone    = $data['input_CustomerMSISDN'] ?? null;

            // Aqui podes buscar o student pelo phone e registrar matrícula
            $enrollmentModel = new EnrollmentModel();

            if($courseId && $phone) {
                $studentId = 1; // EX: pegar pelo phone do usuário
                $exists = $enrollmentModel->where('id_student_enrollment', $studentId)
                                          ->where('id_course_enrollment', $courseId)
                                          ->first();

                if(!$exists){
                    $enrollmentModel->insert([
                        'id_student_enrollment'  => $studentId,
                        'id_course_enrollment'   => $courseId,
                        'status_enrollment'      => 'ativa',
                        'progress_enrollment'    => 0,
                        'enrolled_at_enrollment' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        return $this->response->setStatusCode(200)->setBody('OK');
    }
}
