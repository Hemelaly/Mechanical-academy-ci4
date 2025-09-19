<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\EnrollmentModel;

class PaymentController extends Controller
{
    // private $baseUrl   = "https://openapi.m-pesa.com/sandbox/ipg/v2/vodacom";
    // private $apiKey    = "SUA_CONSUMER_KEY";   // do portal M-Pesa
    // private $publicKey = "SUA_PUBLIC_KEY";     // do portal M-Pesa
    // private $spCode    = "171717";             // shortcode do sandbox
    // private $msisdn    = "258841234567";       // número de teste (sandbox)

    public function createPayment($idCourse)
    {
        $paymentModel    = new \App\Models\PaymentModel();
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $studentModel    = new \App\Models\StudentModel();

        $user = service('auth')->user();
        if (!$user) {
            return redirect()->to('/login')->with('error', 'Você precisa estar logado.');
        }

        // ----- Verifica se o aluno já está inscrito no curso -----
        $existingEnrollment = $enrollmentModel
            ->where('id_student_enrollment', $user->id)
            ->where('id_course_enrollment', $idCourse)
            ->first();

        if ($existingEnrollment) {
            return redirect()->back()->with('message', 'Você já está inscrito neste curso.');
        }

        // ----- Upload do comprovante de pagamento -----
        $file = $this->request->getFile('proof_file_payment');
        $filePath = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/img/', $newName);
            $filePath = 'assets/img/' . $newName;
        }

        // ----- Criar inscrição primeiro -----
        $dataEnrollment = [
            'id_student_enrollment'  => $user->id,
            'id_course_enrollment'   => $idCourse,
            'status_enrollment'      => 'Pendente',
            'progress_enrollment'    => 0.00,
            'enrolled_at_enrollment' => date('Y-m-d H:i:s'),
        ];

        $idEnrollment = $enrollmentModel->insert($dataEnrollment, true);

        if ($idEnrollment === false) {
            $errors = $enrollmentModel->errors();
            return redirect()->back()->with('error', implode(', ', $errors));
        }

        // ----- Criar pagamento vinculado à matrícula -----
        $post = $this->request->getPost();


        $dataPayment = [
            'id_user_payment'       => $user->id,
            'id_course_payment'     => $idCourse,
            'id_enrollment_payment' => $idEnrollment,
            'amount_payment'        => $post['amount_payment'] ?? 0,
            'status_payment'        => 'Pendente',
            'proof_file_payment'    => $filePath,
            'created_at'            => date('Y-m-d H:i:s'),
        ];

        $idPayment = $paymentModel->insert($dataPayment, true);
        if ($idPayment === false) {
            $errors = $paymentModel->errors();
            return redirect()->back()->with('error', implode(', ', $errors));
        }

        // ----- Criar referência do pagamento -----
        $reference = 'PAY-' . date('Y') . '-' . str_pad($idPayment, 6, '0', STR_PAD_LEFT);
        $paymentModel->update($idPayment, ['reference_payment' => $reference]);

        // ----- Redireciona para a dashboard do aluno -----
        return redirect()->to('/student/dashboard/meus_cursos')
            ->with('success', 'Pedido enviado com sucesso! Aguarde a validação.');
    }
}
