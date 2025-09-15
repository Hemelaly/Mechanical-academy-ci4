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

        $post = $this->request->getPost();

        $file = $this->request->getFile('proof_file_payment');
        $filePath = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/img/', $newName);
            $filePath = 'assets/img/' . $newName;
        }

        $user = service('auth')->user();

        // ----- Criar Inscrição primeiro -----
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
            dd($errors);
        }

        // ----- Criar Pagamento vinculado à inscrição -----
        $dataPayment = [
            'id_user_payment'       => $user->id,
            'id_course_payment'     => $idCourse,
            'id_enrollment_payment' => $idEnrollment,
            'amount_payment'        => $post['amount_payment'],
            'status_payment'        => 'Pendente',
            'proof_file_payment'    => $filePath,
        ];

        $idPayment = $paymentModel->insert($dataPayment, true);

        if ($idPayment === false) {
            $errors = $paymentModel->errors();
            dd($errors);
        }

        $reference = 'PAY-' . date('Y') . '-' . str_pad($idPayment, 6, '0', STR_PAD_LEFT);
        $paymentModel->update($idPayment, ['reference_payment' => $reference]);

        // ---- Redireciona para a dashboard do aluno ----
        return redirect()->to('/student/dashboard/meus_cursos')->with('success', 'Pedido enviado com sucesso! Aguarde a validação.');
    }


    /**
     * Inicia o pagamento da inscrição
     */
    // public function pay($courseId)
    // {
    //     $phone  = $this->request->getPost('phone');
    //     $amount = $this->request->getPost('amount');

    //     $payload = [
    //         "input_Amount"                  => $amount,
    //         "input_Country"                 => "MOZ",
    //         "input_Currency"                => "MZN",
    //         "input_CustomerMSISDN"          => "258" . $phone,  // prefixo +258
    //         "input_ServiceProviderCode"     => $this->spCode,
    //         "input_ThirdPartyConversationID" => uniqid(),
    //         "input_TransactionReference"    => "TX" . time() . "-COURSE" . $courseId,
    //         "input_PurchasedItemsDesc"      => "Inscrição Curso ID $courseId"
    //     ];

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $this->baseUrl . "/c2bPayment/singleStage/");
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         "Content-Type: application/json",
    //         "Origin: *",
    //         "Authorization: Bearer " . $this->getAccessToken()
    //     ]);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //     $response = curl_exec($ch);
    //     curl_close($ch);

    //     return $this->response->setJSON(json_decode($response, true));
    // }

    /**
     * Callback do M-Pesa após pagamento
     */
    // public function callback()
    // {
    //     $data = $this->request->getJSON(true);

    //     if (!empty($data['output_ResponseCode']) && $data['output_ResponseCode'] === "INS-0") {
    //         // Pagamento bem sucedido → inscrever aluno
    //         $courseId = $this->extractCourseId($data['output_TransactionReference']);
    //         $userId   = session()->get('id'); // ou mapeie de outra forma

    //         $enrollmentModel = new EnrollmentModel();
    //         $enrollmentModel->insert([
    //             'id_student_enrollment' => $userId,
    //             'id_course_enrollment'  => $courseId,
    //             'status_enrollment'     => 'active',
    //             'progress_enrollment'   => 0.00,
    //             'enrolled_at_enrollment' => date('Y-m-d H:i:s')
    //         ]);
    //     }

    //     return $this->response->setJSON(["status" => "callback_received"]);
    // }

    /**
     * Obtém o access token usando Consumer Key + Secret
     */
    // private function getAccessToken()
    // {
    //     $credentials = base64_encode($this->apiKey . ":" . "SUA_CONSUMER_SECRET");

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $this->baseUrl . "/tokens");
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         "Authorization: Basic " . $credentials
    //     ]);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //     $response = curl_exec($ch);
    //     curl_close($ch);

    //     $result = json_decode($response, true);
    //     return $result['access_token'] ?? null;
    // }

    /**
     * Extrai o ID do curso do TransactionReference
     */
    // private function extractCourseId($transactionRef)
    // {
    //     // Exemplo: "TX1736758290-COURSE5"
    //     $parts = explode("-", $transactionRef);
    //     return isset($parts[1]) ? str_replace("COURSE", "", $parts[1]) : null;
    // }
}
