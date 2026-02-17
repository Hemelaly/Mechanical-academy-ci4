<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CertificateModel;
use App\Services\CertificateService;

class Certificates extends BaseController
{
    private CertificateService $certificateService;

    public function __construct()
    {
        $this->certificateService = new CertificateService();
    }

    /**
     * Emitir certificado (exemplo: depois do curso concluÃ­do)
     * Rota sugerida: POST /certificados/emitir/(:num)
     */
    public function pending()
    {
        return view('pages/student/certificates');
    }

    public function createPending()
    {
        $user = auth()->user();
        helper('url');

        $respond = function (array $data, int $code = 200) {
            return $this->response
                ->setStatusCode($code)
                ->setHeader('X-CSRF-Hash', csrf_hash())
                ->setJSON($data);
        };

        if (! $user) {
            return $respond(['ok' => false, 'message' => 'Usuário não autenticado.'], 401);
        }

        $payload = $this->request->getJSON(true) ?? [];
        $enrollmentId = (int) ($payload['enrollment_id'] ?? $this->request->getPost('enrollment_id'));

        if (! $enrollmentId) {
            return $respond(['ok' => false, 'message' => 'Matrícula inválida.'], 400);
        }

        $result = $this->certificateService->ensureForEnrollment($enrollmentId, (int) $user->id);
        if (! $result['ok']) {
            return $respond([
                'ok'      => false,
                'message' => $result['message'] ?? 'Não foi possível emitir o certificado.',
                'code'    => $result['code'] ?? null,
            ], $result['status'] ?? 400);
        }

        return $respond([
            'ok'             => true,
            'created'        => $result['created'] ?? false,
            'certificate_id' => $result['certificate_id'] ?? null,
            'available_at'   => $result['available_at'] ?? null,
            'completed_at'   => $result['completed_at'] ?? null,
            'pdf_ready'      => $result['pdf_ready'] ?? false,
        ]);
    }

    public function upload()
    {
        $certificateModel = new CertificateModel();
        $db = \Config\Database::connect();

        $enrollmentId = (int) $this->request->getPost('enrollment_id');
        $file = $this->request->getFile('certificate_pdf');

        $fail = function (string $msg) {
            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(400)
                    ->setHeader('X-CSRF-Hash', csrf_hash())
                    ->setJSON(['ok' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        };

        if (!$enrollmentId) return $fail('Enrollment invÃ¡lido.');
        if (!$file || !$file->isValid() || $file->hasMoved()) return $fail('Arquivo invÃ¡lido.');

        $mime = $file->getMimeType();
        $clientMime = $file->getClientMimeType();
        $ext = strtolower($file->getClientExtension() ?? '');
        $allowedMimes = ['application/pdf', 'application/x-pdf', 'application/octet-stream'];

        if (
            !in_array($mime, $allowedMimes, true) &&
            !in_array($clientMime, $allowedMimes, true) &&
            $ext !== 'pdf'
        ) {
            return $fail('Envie apenas PDF.');
        }

        // âœ… Valida ownership do instrutor: enrollment -> course -> instructor
        $row = $db->table('enrollments')
            ->select('enrollments.id_enrollment, enrollments.id_student_enrollment, enrollments.id_course_enrollment, courses.id_instructor_course')
            ->join('courses', 'courses.id_course = enrollments.id_course_enrollment', 'inner')
            ->where('enrollments.id_enrollment', $enrollmentId)
            ->get()
            ->getRowArray();

        if (!$row) return $fail('Enrollment nÃ£o encontrado.');

        if ((int)$row['id_instructor_course'] !== (int)auth()->id()) {
            return $fail('Sem permissÃ£o para submeter certificado deste aluno.');
        }

        // Certificado precisa existir (criado quando aluno conclui)
        $cert = $certificateModel
            ->where('id_user_certificate', $row['id_student_enrollment'])
            ->where('id_course_certificate', $row['id_course_enrollment'])
            ->first();
        if (!$cert) return $fail('Certificado nÃ£o existe (aluno ainda nÃ£o concluiu).');

        // âœ… Salva em writable/uploads/certificates/
        $newName = 'cert_' . $enrollmentId . '_' . time() . '.pdf';
        $path = $file->store('certificates', $newName); // retorna: "certificates/cert_...pdf"

        // âœ… Atualiza pelo enrollment_id (nÃ£o depende de "id")
        $updateData = [
            'pdf_path_certificate'    => $path,
            'uploaded_by_certificate' => auth()->id(),
            'updated_at'              => date('Y-m-d H:i:s'),
        ];

        $number = trim((string) $this->request->getPost('number_certificate'));
        if ($number !== '') {
            $updateData['number_certificate'] = $number;
        }

        $issuedAt = trim((string) $this->request->getPost('issued_at_certificate'));
        if ($issuedAt !== '') {
            $updateData['issued_at_certificate'] = $issuedAt;
        }

        $certificateModel->update((int) $cert['id_certificate'], $updateData);

        if ($this->request->isAJAX()) {
            return $this->response
                ->setHeader('X-CSRF-Hash', csrf_hash())
                ->setJSON([
                    'ok'            => true,
                    'message'       => 'Certificado submetido com sucesso.',
                    'enrollment_id' => $enrollmentId,
                    'status'        => 'available',
                    'file_path'     => $path,
                    'uploaded_at'   => date('Y-m-d H:i:s'),
                ]);
        }

        return redirect()->back()->with('success', 'Certificado submetido com sucesso.');
    }


    public function download($enrollmentId)
    {
        $certificateModel = new CertificateModel();
        $db = \Config\Database::connect();

        $enrollmentId = (int)$enrollmentId;

        // âœ… Valida ownership do aluno: enrollment deve ser do usuÃ¡rio logado
        $row = $db->table('enrollments')
            ->select('enrollments.id_enrollment, enrollments.id_student_enrollment, enrollments.id_course_enrollment, courses.title_course')
            ->join('courses', 'courses.id_course = enrollments.id_course_enrollment', 'inner')
            ->where('enrollments.id_enrollment', $enrollmentId)
            ->get()
            ->getRowArray();

        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ((int)$row['id_student_enrollment'] !== (int)auth()->id()) {
            return redirect()->back()->with('error', 'Sem permissÃ£o para baixar este certificado.');
        }

        $cert = $certificateModel
            ->where('id_user_certificate', $row['id_student_enrollment'])
            ->where('id_course_certificate', $row['id_course_enrollment'])
            ->first();
        if (!$cert) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // âœ… Regra de disponibilidade (se usar available_at)
        $now = time();
        $availableAt = strtotime($cert['avaiable_at_certificate'] ?? '1970-01-01');

        if (empty($cert['pdf_path_certificate']) || $now < $availableAt) {
            return redirect()->back()->with('error', 'Seu certificado ainda nÃ£o estÃ¡ disponÃ­vel.');
        }

        // âœ… Caminho real do store(): writable/uploads/ + file_path
        $fullPath = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $cert['pdf_path_certificate'];

        if (!is_file($fullPath)) {
            return redirect()->back()->with('error', 'Arquivo do certificado nÃ£o encontrado.');
        }

        $courseTitle = trim((string) ($row['title_course'] ?? ''));
        $baseName = $courseTitle !== '' ? $courseTitle : 'certificado';
        $safe = preg_replace('/[^a-z0-9]+/i', '-', strtolower($baseName));
        $safe = trim($safe, '-');
        if ($safe === '') {
            $safe = 'certificado';
        }

        return $this->response
            ->download($fullPath, null)
            ->setFileName('certificado-' . $safe . '.pdf');
    }

    public function deleteCertificate()
    {
        $certificateModel = new CertificateModel();
        $db = \Config\Database::connect();

        $payload = $this->request->getJSON(true) ?? [];
        $id = (int)($payload['id_certificate'] ?? $this->request->getPost('id_certificate'));

        $fail = function (string $msg) {
            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(400)
                    ->setHeader('X-CSRF-Hash', csrf_hash())
                    ->setJSON(['ok' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        };

        if (!$id) {
            return $fail('Certificado invÃ¡lido.');
        }

        $row = $db->table('certificates cert')
            ->select('cert.id_certificate, cert.pdf_path_certificate, courses.id_instructor_course')
            ->join('courses', 'courses.id_course = cert.id_course_certificate', 'inner')
            ->where('cert.id_certificate', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return $fail('Certificado nÃ£o encontrado.');
        }

        if ((int)$row['id_instructor_course'] !== (int)auth()->id()) {
            return $fail('Sem permissÃ£o para excluir este certificado.');
        }

        if (!empty($row['pdf_path_certificate'])) {
            $fullPath = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $row['pdf_path_certificate'];
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
        }

        $certificateModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response
                ->setHeader('X-CSRF-Hash', csrf_hash())
                ->setJSON(['ok' => true, 'id_certificate' => $id]);
        }

        return redirect()->back()->with('success', 'Certificado excluÃ­do com sucesso.');
    }
}
