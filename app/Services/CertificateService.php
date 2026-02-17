<?php

namespace App\Services;

use App\Models\CertificateModel;
use CodeIgniter\Database\BaseConnection;
use Dompdf\Dompdf;

class CertificateService
{
    public function __construct(
        private ?BaseConnection $db = null,
        private ?CertificateModel $certificateModel = null
    ) {
        $this->db ??= db_connect();
        $this->certificateModel ??= new CertificateModel();
        helper(['url', 'auth']);
    }

    /**
     * Ensures the enrollment has a certificate record and PDF ready.
     *
     * @return array{ok:bool,created:bool|null,certificate_id:int|null,completed_at:?string,available_at:?string,pdf_ready:bool,string?:string}
     */
    public function ensureForEnrollment(int $enrollmentId, int $studentId, ?int $actorId = null): array
    {
        $row = $this->db->table('enrollments')
            ->select('id_enrollment, id_student_enrollment, id_course_enrollment, progress_enrollment, completed_enrollment')
            ->where('id_enrollment', $enrollmentId)
            ->get()
            ->getRowArray();

        if (! $row || (int) $row['id_student_enrollment'] !== $studentId) {
            return [
                'ok'      => false,
                'message' => 'Sem permissão para emitir este certificado.',
                'code'    => 'forbidden',
                'status'  => 403,
            ];
        }

        if ((int) ($row['progress_enrollment'] ?? 0) < 100) {
            return [
                'ok'      => false,
                'message' => 'Curso ainda não concluído.',
                'code'    => 'incomplete',
                'status'  => 400,
            ];
        }

        $completedAt = $row['completed_enrollment'] ?? null;
        if (empty($completedAt)) {
            $completedAt = date('Y-m-d H:i:s');
            $this->db->table('enrollments')
                ->where('id_enrollment', $enrollmentId)
                ->update(['completed_enrollment' => $completedAt]);
        }

        $availableAt = date('Y-m-d H:i:s');

        $existing = $this->certificateModel
            ->where('id_user_certificate', $row['id_student_enrollment'])
            ->where('id_course_certificate', $row['id_course_enrollment'])
            ->first();

        $certificateId = 0;
        $created = false;

        if ($existing) {
            $certificateId = (int) $existing['id_certificate'];
            $this->certificateModel->update($certificateId, [
                'avaiable_at_certificate' => $availableAt,
                'updated_at'              => date('Y-m-d H:i:s'),
            ]);
        } else {
            $uuid = bin2hex(random_bytes(16));
            $hash = hash('sha256', $uuid . '|' . $row['id_student_enrollment'] . '|' . $row['id_course_enrollment']);
            $now = date('Y-m-d H:i:s');

            $certificateId = (int) $this->certificateModel->insert([
                'id_user_certificate'    => (int) $row['id_student_enrollment'],
                'id_course_certificate'  => (int) $row['id_course_enrollment'],
                'uuid_certificate'       => $uuid,
                'hash_certificate'       => $hash,
                'avaiable_at_certificate'=> $availableAt,
                'created_at'             => $now,
                'updated_at'             => $now,
            ]);

            $created = true;
        }

        $certificateRow = $this->certificateModel->find($certificateId);
        if ($certificateRow) {
            $this->ensureCertificatePdf($certificateRow, $row, $enrollmentId, $completedAt, $actorId);
            $certificateRow = $this->certificateModel->find($certificateId);
        }

        $availableAtResponse = $certificateRow['avaiable_at_certificate'] ?? $availableAt;
        $availableAtIso = $availableAtResponse ? date('c', strtotime($availableAtResponse)) : null;
        $availableAtTs = $availableAtResponse ? strtotime($availableAtResponse) : null;
        $pdfReady = !empty($certificateRow['pdf_path_certificate']) && $availableAtTs && time() >= $availableAtTs;

        return [
            'ok'             => true,
            'created'        => $created,
            'certificate_id' => $certificateId,
            'completed_at'   => $completedAt ? date('c', strtotime($completedAt)) : null,
            'available_at'   => $availableAtIso,
            'pdf_ready'      => $pdfReady,
        ];
    }

    private function ensureCertificatePdf(array $certificate, array $enrollmentRow, int $enrollmentId, string $completedAt, ?int $actorId = null): void
    {
        if (empty($certificate['id_certificate'])) {
            return;
        }

        $pdfPath = $certificate['pdf_path_certificate'] ?? null;
        $fullPath = null;
        if ($pdfPath) {
            $fullPath = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $pdfPath;
        }

        if ($fullPath && is_file($fullPath)) {
            return;
        }

        $studentRow = $this->db->table('users')
            ->select('username')
            ->where('id', $enrollmentRow['id_student_enrollment'])
            ->get()
            ->getRowArray();
        $studentName = $studentRow['username'] ?? 'Aluno';

        $courseRow = $this->db->table('courses')
            ->select('title_course')
            ->where('id_course', $enrollmentRow['id_course_enrollment'])
            ->get()
            ->getRowArray();
        $courseName = $courseRow['title_course'] ?? 'Curso';

        $timestamp = strtotime($completedAt);
        if ($timestamp === false) {
            $timestamp = time();
        }
        $issuedDate = date('d/m/Y', $timestamp);
        $issuedAtDb = date('Y-m-d H:i:s', $timestamp);

        $number = $certificate['number_certificate'] ?? null;
        $uuid = $certificate['uuid_certificate'] ?? bin2hex(random_bytes(8));
        if (empty($number)) {
            $number = strtoupper(substr($uuid, 0, 8));
        }

        $verifyId = (int) $certificate['id_certificate'];
        $verifyUrl = site_url('certificados/verificar/' . $verifyId);

        $html = view('certificates/pdf', [
            'studentName'       => $studentName,
            'courseName'        => $courseName,
            'issuedDate'        => $issuedDate,
            'certificateNumber' => $number,
            'uuid'              => $uuid,
            'verifyUrl'         => $verifyUrl,
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();

        $destDir = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'certificates';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }

        $fileName = 'cert_' . $enrollmentId . '_' . time() . '.pdf';
        $outputPath = $destDir . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($outputPath, $dompdf->output());

        $actorId = $actorId ?? (function_exists('auth') ? auth()->id() : null);

        $this->certificateModel->update($verifyId, [
            'pdf_path_certificate'    => 'certificates/' . $fileName,
            'number_certificate'      => $number,
            'issued_at_certificate'   => $issuedAtDb,
            'uploaded_by_certificate' => $actorId,
            'updated_at'              => date('Y-m-d H:i:s'),
        ]);
    }
}
