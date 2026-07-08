<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CertificateModel;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use setasign\Fpdi\Fpdi;

class CertificatesController extends BaseController
{
    public function generate()
    {
        $payload = $this->request->getJSON(true) ?? [];

        $nameStudent = trim((string) ($payload['name_student_certificate'] ?? ''));
        $nameCourse = trim((string) ($payload['name_course_certificate'] ?? ''));
        $concludedAt = trim((string) ($payload['concluded_at_certificate'] ?? ''));
        $trainerName = trim((string) ($payload['trainer_name_certificate'] ?? ''));
        $directorName = trim((string) ($payload['director_name_certificate'] ?? ''));

        if ($nameStudent === '' || $nameCourse === '' || $concludedAt === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => 422,
                'message' => 'Nome do formando, curso e data de conclusão são obrigatórios.',
            ]);
        }

        if (!$this->isValidDate($concludedAt)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => 422,
                'message' => 'A data de conclusão deve estar no formato YYYY-MM-DD.',
            ]);
        }

        $templatePath = FCPATH . 'assets/certificado/certificado.pdf';

        if (!is_file($templatePath)) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 500,
                'message' => 'Template do certificado não encontrado em public/assets/certificado/certificado.pdf.',
            ]);
        }

        $certificateModel = new CertificateModel();
        $code = $this->generateUniqueCertificateCode($certificateModel);

        $frontendUrl = rtrim((string) env('app.frontendURL', site_url()), '/');
        $verificationUrl = $frontendUrl . '/certificados/verificar/' . $code;

        $uploadDir = WRITEPATH . 'uploads/certificates/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $fileName = $code . '.pdf';
        $outputPath = $uploadDir . $fileName;

        try {
            $this->createCertificatePdf(
                templatePath: $templatePath,
                outputPath: $outputPath,
                nameStudent: $nameStudent,
                nameCourse: $nameCourse,
                concludedAt: $concludedAt,
                trainerName: $trainerName,
                directorName: $directorName,
                code: $code,
                verificationUrl: $verificationUrl
            );

            $certificateModel->insert([
                'code_certificate' => $code,
                'name_student_certificate' => $nameStudent,
                'name_course_certificate' => $nameCourse,
                'concluded_at_certificate' => $concludedAt,
                'trainer_name_certificate' => $trainerName !== '' ? $trainerName : null,
                'director_name_certificate' => $directorName !== '' ? $directorName : null,
                'file_path_certificate' => 'certificates/' . $fileName,
                'status_certificate' => 'valid',
                'verification_url_certificate' => $verificationUrl,
            ]);
        } catch (\Throwable $exception) {
            log_message('error', 'Erro ao gerar certificado: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->response->setStatusCode(500)->setJSON([
                'status' => 500,
                'message' => 'Não foi possível gerar o certificado neste momento.',
            ]);
        }

        return $this->response->setJSON([
            'status' => 200,
            'message' => 'Certificado gerado com sucesso.',
            'data' => [
                'code_certificate' => $code,
                'verification_url_certificate' => $verificationUrl,
                'download_url_certificate' => site_url('api/certificates/download/' . $code),
            ],
        ]);
    }

    public function verify(?string $code = null)
    {
        $code = trim((string) $code);

        if ($code === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => 422,
                'valid' => false,
                'message' => 'Código do certificado é obrigatório.',
            ]);
        }

        $certificate = (new CertificateModel())
            ->where('code_certificate', $code)
            ->first();

        if (!$certificate) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 404,
                'valid' => false,
                'message' => 'Certificado não encontrado.',
            ]);
        }

        if ($certificate['status_certificate'] !== 'valid') {
            return $this->response->setJSON([
                'status' => 200,
                'valid' => false,
                'message' => 'Este certificado foi revogado.',
                'data' => $this->publicCertificatePayload($certificate),
            ]);
        }

        return $this->response->setJSON([
            'status' => 200,
            'valid' => true,
            'message' => 'Certificado válido.',
            'data' => $this->publicCertificatePayload($certificate),
        ]);
    }

    public function download(?string $code = null)
    {
        $code = trim((string) $code);

        if ($code === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => 422,
                'message' => 'Código do certificado é obrigatório.',
            ]);
        }

        $certificate = (new CertificateModel())
            ->where('code_certificate', $code)
            ->first();

        if (!$certificate) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 404,
                'message' => 'Certificado não encontrado.',
            ]);
        }

        $filePath = WRITEPATH . 'uploads/' . $certificate['file_path_certificate'];

        if (!is_file($filePath)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 404,
                'message' => 'Arquivo do certificado não encontrado.',
            ]);
        }

        return $this->response->download($filePath, null)->setFileName($code . '.pdf');
    }

    private function createCertificatePdf(
        string $templatePath,
        string $outputPath,
        string $nameStudent,
        string $nameCourse,
        string $concludedAt,
        string $trainerName,
        string $directorName,
        string $code,
        string $verificationUrl
    ): void {
        $certConfig = config('Certificate');
        $positions = (array) ($certConfig->templatePositions ?? []);

        $pdf = new Fpdi();
        $pdf->setSourceFile($templatePath);
        $templateId = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($templateId);
        $orientation = $size['width'] > $size['height'] ? 'L' : 'P';

        $pdf->AddPage($orientation, [$size['width'], $size['height']]);
        $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);
        $pdf->SetTextColor(26, 26, 26);

        $formattedDate = date('d/m/Y', strtotime($concludedAt));

        $write = function (string $key, string $text) use ($pdf, $positions): void {
            $pos = $positions[$key] ?? null;
            if (!is_array($pos) || trim($text) === '') {
                return;
            }

            $x = (float) ($pos['x'] ?? 0);
            $y = (float) ($pos['y'] ?? 0);
            $w = (float) ($pos['w'] ?? 0);
            $h = (float) ($pos['h'] ?? 0);
            $fontSize = (float) ($pos['size'] ?? 12);
            $align = strtoupper((string) ($pos['align'] ?? 'C'));
            $valign = strtoupper((string) ($pos['valign'] ?? 'B'));
            $bold = !empty($pos['bold']);
            $fontStyle = strtoupper(trim((string) ($pos['font_style'] ?? '')));
            $style = ($bold ? 'B' : '') . (str_contains($fontStyle, 'I') ? 'I' : '');

            $pdf->SetFont('Arial', $style, $fontSize);
            $pdf->SetXY($x, $y);
            $cellValign = in_array($valign, ['T', 'M', 'B'], true) ? $valign : 'B';
            $pdf->Cell($w, $h, utf8_decode($text), 0, 0, $align, false, '', 0, false, 'T', $cellValign);
        };

        $write('concluded_date', $formattedDate);
        $write('issued_date', $formattedDate);
        $write('student_name', $nameStudent);
        $write('course_name', $nameCourse);

        if ($trainerName !== '') {
            $write('instructor_name', $trainerName);
        }

        if (!empty($certConfig->showDirectorSignature) && $directorName !== '') {
            $write('director_name', $directorName);
        }

        if (!empty($certConfig->showVerificationCode)) {
            $write('verification_code', $code);
            $write('certificate_number', $code);
        }

        if (!empty($certConfig->showQrCode) && isset($positions['qr_code']) && is_array($positions['qr_code'])) {
            $qrTempPath = WRITEPATH . 'uploads/certificates/qr-' . $code . '.png';

            $qr = Builder::create()
                ->writer(new PngWriter())
                ->data($verificationUrl)
                ->size(260)
                ->margin(8)
                ->build();

            $qr->saveToFile($qrTempPath);

            $qrPos = $positions['qr_code'];
            $pdf->Image(
                $qrTempPath,
                (float) ($qrPos['x'] ?? 0),
                (float) ($qrPos['y'] ?? 0),
                (float) ($qrPos['w'] ?? 20),
                (float) ($qrPos['h'] ?? 20)
            );

            if (is_file($qrTempPath)) {
                unlink($qrTempPath);
            }
        }

        $pdf->Output('F', $outputPath);
    }

    private function generateUniqueCertificateCode(CertificateModel $certificateModel): string
    {
        do {
            $code = 'MTA-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));
        } while ($certificateModel->where('code_certificate', $code)->first());

        return $code;
    }

    private function isValidDate(string $date): bool
    {
        $value = \DateTime::createFromFormat('Y-m-d', $date);

        return $value && $value->format('Y-m-d') === $date;
    }

    private function publicCertificatePayload(array $certificate): array
    {
        return [
            'code_certificate' => $certificate['code_certificate'],
            'name_student_certificate' => $certificate['name_student_certificate'],
            'name_course_certificate' => $certificate['name_course_certificate'],
            'concluded_at_certificate' => $certificate['concluded_at_certificate'],
            'trainer_name_certificate' => $certificate['trainer_name_certificate'],
            'director_name_certificate' => $certificate['director_name_certificate'],
            'verification_url_certificate' => $certificate['verification_url_certificate'],
            'download_url_certificate' => site_url('api/certificates/download/' . $certificate['code_certificate']),
        ];
    }
}
