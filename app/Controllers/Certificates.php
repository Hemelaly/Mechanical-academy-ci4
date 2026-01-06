<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CertificateModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Certificates extends BaseController
{
    /**
     * Emitir certificado (exemplo: depois do curso concluído)
     * Rota sugerida: POST /certificados/emitir/(:num)
     */
    public function pending() {
        return view('pages/student/certificates');
    }

    public function upload()
    {
        $certificateModel = new CertificateModel();
        $enrollmentId = (int)$this->request->getPost('enrollment_id');
        $file = $this->request->getFile('certificate_pdf');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Arquivo inválido.');
        }

        if ($file->getClientMimeType() !== 'application/pdf') {
            return redirect()->back()->with('error', 'Envie apenas PDF.');
        }

        // TODO: validar ownership do instrutor sobre o curso desse enrollment

        $newName = 'cert_' . $enrollmentId . '_' . time() . '.pdf';
        $path = $file->store('uploads/certificates', $newName); // salva em writable/

        $cert = $certificateModel->where('enrollment_id', $enrollmentId)->first();
        if (!$cert) {
            return redirect()->back()->with('error', 'Certificado não existe (aluno ainda não concluiu).');
        }

        $certificateModel->update($cert['id'], [
            'status'      => 'available',
            'file_path'   => $path,
            'uploaded_at' => date('Y-m-d H:i:s'),
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Certificado submetido com sucesso.');
    }


    /**
     * Download do PDF do certificado
     * Rota sugerida: GET /certificados/download/(:segment)
     */
    public function download($enrollmentId)
    {
        // validar ownership do aluno aqui

        $certificateModel = new CertificateModel();

        $cert = $certificateModel->where('enrollment_id', (int)$enrollmentId)->first();
        if (!$cert) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $now = time();
        $availableAt = strtotime($cert['available_at'] ?? '1970-01-01');

        if ($cert['status'] !== 'available' || empty($cert['file_path']) || $now < $availableAt) {
            return redirect()->back()->with('error', 'Seu certificado ainda não está disponível.');
        }

        $fullPath = WRITEPATH . $cert['file_path'];
        if (!is_file($fullPath)) {
            return redirect()->back()->with('error', 'Arquivo do certificado não encontrado.');
        }

        return $this->response->download($fullPath, null)->setFileName('certificado.pdf');
    }


    /**
     * Verificação pública
     * Rota sugerida: GET /certificados/verificar/(:segment)
     */
    public function verificar(string $uuid_certificate)
    {
        $certModel = new CertificateModel();
        $cert = $certModel->where('uuid_certificate', $uuid_certificate)->first();

        if (!$cert) {
            return $this->response->setStatusCode(404)->setBody('Certificado inválido.');
        }

        // (Opcional) Recalcular hash para confirmar integridade
        $expectedHash = hash(
            'sha256',
            $cert['uuid_certificate'] . '|' . $cert['id_user_cerificate'] . '|' . $cert['id_course_cerificate'] . '|' . env('app.key')
        );
        $isValid = hash_equals($expectedHash, $cert['hash_certificate']);

        // Cria uma view: app/Views/certificates/verify.php
        return view('certificates/verify', [
            'cert' => $cert,
            'isValid' => $isValid,
        ]);
    }

    /**
     * Gerador UUID v4 (sem dependências)
     */
    private function uuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // version 4
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // variant

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
