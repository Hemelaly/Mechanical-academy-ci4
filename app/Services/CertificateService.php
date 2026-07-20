<?php

namespace App\Services;

use App\Models\CertificateModel;
use App\Services\CourseCommerceService;
use CodeIgniter\Database\BaseConnection;
use Dompdf\Dompdf;
use Dompdf\Options;
use setasign\Fpdi\TcpdfFpdi;

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

        try {
            if ($existing) {
                $certificateId = (int) $existing['id_certificate'];
                $this->certificateModel->update($certificateId, $this->filterCertificateFields([
                    'avaiable_at_certificate' => $availableAt,
                    'updated_at'              => date('Y-m-d H:i:s'),
                ]));
            } else {
                $uuid = bin2hex(random_bytes(16));
                $hash = hash('sha256', $uuid . '|' . $row['id_student_enrollment'] . '|' . $row['id_course_enrollment']);
                $now = date('Y-m-d H:i:s');

                // Em produção a coluna number_certificate ainda pode ser NOT NULL
                // (migration não aplicada). Gera o número já no insert.
                $courseTitle = $this->db->table('courses')
                    ->select('title_course')
                    ->where('id_course', (int) $row['id_course_enrollment'])
                    ->get()
                    ->getRowArray();
                $courseNameForNumber = trim((string) ($courseTitle['title_course'] ?? '')) ?: 'Curso';
                $yearForNumber = (int) date('Y', strtotime($completedAt) ?: time());
                $numberOnCreate = $this->generateCertificateNumber($courseNameForNumber, $yearForNumber);

                $certificateId = (int) $this->certificateModel->insert($this->filterCertificateFields([
                    'id_user_certificate'     => (int) $row['id_student_enrollment'],
                    'id_course_certificate'   => (int) $row['id_course_enrollment'],
                    'uuid_certificate'        => $uuid,
                    'number_certificate'      => $numberOnCreate,
                    'hash_certificate'        => $hash,
                    'avaiable_at_certificate' => $availableAt,
                    'created_at'              => $now,
                    'updated_at'              => $now,
                ]));

                $created = true;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Falha ao criar/atualizar registo de certificado: ' . $e->getMessage());

            return [
                'ok'             => false,
                'created'        => false,
                'certificate_id' => null,
                'completed_at'   => $completedAt ? date('c', strtotime($completedAt)) : null,
                'available_at'   => date('c', strtotime($availableAt)),
                'pdf_ready'      => false,
                'message'        => 'Não foi possível preparar o certificado.',
                'code'           => 'certificate_db',
                'status'         => 500,
            ];
        }

        $certificateRow = $this->certificateModel->find($certificateId);
        if ($certificateRow) {
            try {
                $this->ensureCertificatePdf($certificateRow, $row, $enrollmentId, $completedAt, $actorId);
            } catch (\Throwable $e) {
                log_message('error', 'Falha ao gerar PDF do certificado: ' . $e->getMessage());
            }
            $certificateRow = $this->certificateModel->find($certificateId) ?: $certificateRow;
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
            'message'        => $pdfReady ? null : 'Certificado registado; PDF ainda não disponível.',
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

        $certConfig = config('Certificate');
        $forceRegenerate = (defined('ENVIRONMENT') && ENVIRONMENT === 'development') || !empty($certConfig->forceRegenerate);
        $templateRel = (string) ($certConfig->templatePdfPath ?? 'assets/certificado/certificado.pdf');
        $templatePath = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($templateRel, '/\\'));
        if (!$forceRegenerate && $fullPath && is_file($fullPath)) {
            // If the PDF already exists, only regenerate when the template was updated.
            if (!is_file($templatePath) || filemtime($templatePath) <= filemtime($fullPath)) {
                return;
            }
        }

        $studentRow = $this->db->table('users u')
            ->select('COALESCE(s.name_student, u.username) AS name', false)
            ->join('students s', 's.id_user_student = u.id', 'left')
            ->where('u.id', $enrollmentRow['id_student_enrollment'])
            ->get()
            ->getRowArray();
        $studentName = trim((string) ($studentRow['name'] ?? '')) ?: 'Aluno';

        $courseRow = $this->db->table('courses')
            ->select('id_course, title_course, id_instructor_course')
            ->where('id_course', $enrollmentRow['id_course_enrollment'])
            ->get()
            ->getRowArray();
        $courseId = (int) ($courseRow['id_course'] ?? 0);
        $courseName = trim((string) ($courseRow['title_course'] ?? '')) ?: 'Curso';
        $instructorUserId = (int) ($courseRow['id_instructor_course'] ?? 0);

        $instructorName = 'Instrutor';
        if ($instructorUserId > 0) {
            $inst = $this->db->table('users')->select('username')->where('id', $instructorUserId)->get()->getRowArray();
            $instructorName = trim((string) ($inst['username'] ?? '')) ?: $instructorName;
        }

        $durationRow = $this->db->table('lessons l')
            ->selectSum('l.duration_lesson', 'total_minutes')
            ->join('modules m', 'm.id_module = l.id_module_lesson', 'inner')
            ->where('m.id_course_module', $courseId)
            ->get()
            ->getRowArray();
        $totalMinutes = (int) ($durationRow['total_minutes'] ?? 0);

        $commerce = new CourseCommerceService();
        $courseFull = $this->db->table('courses')->where('id_course', $courseId)->get()->getRowArray() ?: [];
        $workloadHours = max(1, (int) ceil($commerce->resolveHoursValue((object) $courseFull, $totalMinutes)));

        $timestamp = strtotime($completedAt);
        if ($timestamp === false) {
            $timestamp = time();
        }
        $issuedDate = date('d/m/Y', $timestamp);
        $issuedAtDb = date('Y-m-d H:i:s', $timestamp);

        $number = $certificate['number_certificate'] ?? null;
        $uuid = $certificate['uuid_certificate'] ?? bin2hex(random_bytes(8));
        $year = (int) date('Y', $timestamp);
        $isValidFormat = is_string($number) && preg_match('~^[A-Z0-9]+[-/]\\d{4}[-/][A-Z0-9]+[-/]\\d{3}$~', $number);
        if (empty($number) || !$isValidFormat) {
            $number = $this->generateCertificateNumber($courseName, $year);
        }

        $verifyId = (int) $certificate['id_certificate'];
        $academyUrl = rtrim((string) ($certConfig->academyUrl ?? ''), '/');
        $verifyBase = $academyUrl !== '' ? $academyUrl : rtrim(site_url(), '/');
        $verifyUrl = $verifyBase . '/certificados/verificar/' . rawurlencode($number);

        $directorName = $certConfig->directorName ?? 'Director Geral';
        $directorTitle = $certConfig->directorTitle ?? 'Director Geral';
        $directorSignaturePath = $certConfig->directorSignaturePath ?? '';
        $instructorTitle = $certConfig->instructorTitle ?? 'Formador';
        $instructorSignaturePath = $certConfig->instructorSignaturePath ?? '';
        $studentNameFontCssUrl = $certConfig->studentNameFontCssUrl ?? '';

        $destDir = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'certificates';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }

        $fileName = 'cert_' . $enrollmentId . '_' . time() . '.pdf';
        $outputPath = $destDir . DIRECTORY_SEPARATOR . $fileName;

        $generated = false;

        if (is_file($templatePath)) {
            $generated = $this->renderFromTemplatePdf(
                $templatePath,
                $outputPath,
                [
                    'studentName' => $studentName,
                    'courseName' => $courseName,
                    'workloadHours' => $workloadHours,
                    'issuedDate' => $issuedDate,
                    'certificateNumber' => $number,
                    'verificationUrl' => $verifyUrl,
                    'instructorName' => $instructorName,
                    'directorName' => $directorName,
                ],
                $certConfig
            );
        }

        if (!$generated) {
            try {
                // Fallback: gera via HTML/Dompdf (mantém compatibilidade)
                $html = view('certificates/pdf', [
                    'studentName'              => $studentName,
                    'courseName'               => $courseName,
                    'workloadHours'            => $workloadHours,
                    'issuedDate'               => $issuedDate,
                    'certificateNumber'        => $number,
                    'uuid'                     => $uuid,
                    'verifyUrl'                => $verifyUrl,
                    'directorName'             => $directorName,
                    'directorTitle'            => $directorTitle,
                    'directorSignaturePath'    => $directorSignaturePath,
                    'instructorName'           => $instructorName,
                    'instructorTitle'          => $instructorTitle,
                    'instructorSignaturePath'  => $instructorSignaturePath,
                    'studentNameFontCssUrl'    => $studentNameFontCssUrl,
                ]);

                $options = new Options();
                $options->set('isRemoteEnabled', true);
                $options->set('isHtml5ParserEnabled', true);
                $dompdf = new Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper([0, 0, 595.28, 419.53], 'landscape');
                $dompdf->render();
                file_put_contents($outputPath, $dompdf->output());
                $generated = is_file($outputPath) && filesize($outputPath) > 0;
            } catch (\Throwable $e) {
                log_message('error', 'Fallback Dompdf do certificado falhou: ' . $e->getMessage());
                $generated = false;
            }
        }

        if (!$generated) {
            log_message('error', 'Certificado sem PDF gerado. Verifique o template em: ' . $templatePath);
            return;
        }

        $actorId = $actorId ?? (function_exists('auth') ? auth()->id() : null);

        $this->certificateModel->update($verifyId, $this->filterCertificateFields([
            'pdf_path_certificate'     => 'certificates/' . $fileName,
            'number_certificate'       => $number,
            'issued_at_certificate'    => $issuedAtDb,
            'status_certificate'       => 'available',
            'available_at_certificate' => $issuedAtDb,
            'avaiable_at_certificate'  => $issuedAtDb,
            'uploaded_by_certificate'  => $actorId,
            'updated_at'               => date('Y-m-d H:i:s'),
        ]));
    }

    /**
     * Remove campos inexistentes na tabela certificates para evitar 500 em produção
     * quando alguma migration ainda não foi aplicada.
     *
     * @param array<string, mixed> $fields
     * @return array<string, mixed>
     */
    private function filterCertificateFields(array $fields): array
    {
        static $columns = null;

        try {
            if ($columns === null) {
                $columns = array_flip($this->db->getFieldNames('certificates') ?: []);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Não foi possível ler colunas de certificates: ' . $e->getMessage());
            return $fields;
        }

        if ($columns === []) {
            return $fields;
        }

        return array_filter(
            $fields,
            static fn ($key) => isset($columns[$key]),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Gera PDF usando um template PDF base (FPDI + TCPDF) e escreve em disco.
     */
    private function renderFromTemplatePdf(string $templatePath, string $outputPath, array $data, object $certConfig): bool
    {
        try {
            $pdf = new TcpdfFpdi('L', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('Mechanical Academy');
            $pdf->SetAuthor('Mechanical Academy');
            $pdf->SetTitle('Certificado');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetAutoPageBreak(false, 0);
            $pdf->setImageScale(1);

            $pageCount = $pdf->setSourceFile($templatePath);
            if ($pageCount < 1) {
                return false;
            }

            $tplId = $pdf->importPage(1);
            $tplSize = $pdf->getTemplateSize($tplId);
            $pdf->AddPage($tplSize['orientation'], [$tplSize['width'], $tplSize['height']]);
            $pdf->useTemplate($tplId);

            $positions = (array) ($certConfig->templatePositions ?? []);

            // Para evitar o erro TCPDF "Could not include font definition file",
            // fontes decorativas/manuscritas são renderizadas como PNG transparente via GD
            // e aplicadas por cima do PDF. Assim não dependemos do cache de fontes do TCPDF.
            $studentFontPath = $this->resolveCertificateAssetPath((string) ($certConfig->studentNameTtfPath ?? ''));
            $courseFontPath = $this->resolveCertificateAssetPath((string) ($certConfig->courseNameTtfPath ?? ''));
            $signatureFontPath = $this->resolveCertificateAssetPath((string) ($certConfig->signatureTtfPath ?? ''));

            $write = function (string $key, string $text, bool $handwriting = false) use ($pdf, $positions, $studentFontPath, $courseFontPath, $signatureFontPath): void {
                $pos = $positions[$key] ?? null;
                if (!is_array($pos)) return;

                $x = (float) ($pos['x'] ?? 0);
                $y = (float) ($pos['y'] ?? 0);
                $w = (float) ($pos['w'] ?? 0);
                $h = (float) ($pos['h'] ?? 0);
                $size = (float) ($pos['size'] ?? 12);
                $minSize = (float) ($pos['min_size'] ?? max(8, $size - 6));
                $align = (string) ($pos['align'] ?? 'L');
                $valign = strtoupper((string) ($pos['valign'] ?? 'B'));
                $bold = !empty($pos['bold']);
                $fontStyle = strtoupper(trim((string) ($pos['font_style'] ?? '')));
                $fontRole = strtolower(trim((string) ($pos['font_role'] ?? '')));

                $decorativeFontPath = null;
                if ($fontRole === 'student') {
                    $decorativeFontPath = $studentFontPath;
                } elseif ($fontRole === 'course') {
                    // O título do curso fica mais legível com a fonte nativa configurada.
                    // Só usa TTF decorativa se certificate.courseNameTtfPath for definido.
                    $decorativeFontPath = $courseFontPath;
                } elseif ($fontRole === 'signature') {
                    $decorativeFontPath = $signatureFontPath;
                } elseif ($handwriting) {
                    $decorativeFontPath = $studentFontPath;
                }

                if ($decorativeFontPath && $this->writeTextAsImage($pdf, $text, $decorativeFontPath, $x, $y, $w, $h, $size, $minSize, $align, $valign)) {
                    return;
                }

                $configuredFont = strtolower(trim((string) ($pos['font'] ?? 'helvetica')));
                $coreFonts = ['helvetica', 'times', 'courier', 'symbol', 'zapfdingbats'];
                $font = in_array($configuredFont, $coreFonts, true) ? $configuredFont : 'helvetica';
                $style = '';
                if ($bold) {
                    $style .= 'B';
                }
                if (str_contains($fontStyle, 'I')) {
                    $style .= 'I';
                }

                $pdf->SetTextColor(15, 23, 42);
                // Auto-fit long text into the available width when needed.
                $fitSize = $size;
                if ($w > 0 && $text !== '') {
                    while ($fitSize > $minSize) {
                        $pdf->SetFont($font, $style, $fitSize);
                        if ($pdf->GetStringWidth($text) <= ($w - 1)) {
                            break;
                        }
                        $fitSize -= 0.5;
                    }
                }
                $pdf->SetFont($font, $style, $fitSize);
                $pdf->SetXY($x, $y);
                $cellValign = in_array($valign, ['T', 'M', 'B'], true) ? $valign : 'B';
                $pdf->MultiCell($w, $h, $text, 0, $align, false, 1, '', '', true, 0, false, true, $h, $cellValign);
            };

            $studentName = trim((string) ($data['studentName'] ?? ''));
            $courseName = trim((string) ($data['courseName'] ?? ''));
            $hours = (int) ($data['workloadHours'] ?? 0);
            $issuedDate = trim((string) ($data['issuedDate'] ?? ''));
            $number = trim((string) ($data['certificateNumber'] ?? ''));
            $instructorName = trim((string) ($data['instructorName'] ?? ''));
            $directorName = trim((string) ($data['directorName'] ?? ''));
            $verificationUrl = trim((string) ($data['verificationUrl'] ?? ''));

            // Nome do estudante, curso e assinaturas usam fonte TTF decorativa quando o ficheiro existe.
            // Se a fonte não existir, o sistema cai para texto seguro em helvetica sem quebrar o certificado.
            if ($studentName !== '') $write('student_name', $studentName, true);
            if ($courseName !== '') $write('course_name', $courseName, false);
            if ($instructorName !== '') $write('instructor_name', $instructorName, false);
            if (!empty($certConfig->showDirectorSignature) && $directorName !== '') $write('director_name', $directorName, false);
            if ($issuedDate !== '') {
                if (preg_match('~^(\\d{2})/(\\d{2})/(\\d{4})$~', $issuedDate, $m)) {
                    $day = $m[1];
                    $month = $m[2];
                    $year = $m[3];
                    if (isset($positions['issued_day'], $positions['issued_month'], $positions['issued_year'])) {
                        $write('issued_day', $day, false);
                        $write('issued_month', $month, false);
                        $write('issued_year', substr($year, -1), false);
                    } else {
                        $write('issued_date', $issuedDate, false);
                        $write('concluded_date', $issuedDate, false);
                    }
                } else {
                    $write('issued_date', $issuedDate, false);
                    $write('concluded_date', $issuedDate, false);
                }
            }

            if (!empty($certConfig->showVerificationCode) && $number !== '') {
                if (preg_match('~^([A-Z0-9]+)[-/]([0-9]{4})[-/]([A-Z0-9]+)[-/]([0-9]{3})$~', $number, $m)) {
                    $issuer = $m[1];
                    $year = $m[2];
                    $code = $m[3];
                    $seq = $m[4];
                    if (isset($positions['cert_issuer'], $positions['cert_year'], $positions['cert_course_code'], $positions['cert_seq'])) {
                        $write('cert_issuer', $issuer, false);
                        // Template usually has "20__" so we only fill the last 2 digits (e.g. "26").
                        $write('cert_year', substr($year, -2), false);
                        $write('cert_course_code', $code, false);
                        $write('cert_seq', $seq, false);
                    } else {
                        $write('verification_code', $number, false);
                    }
                } else {
                    $write('verification_code', $number, false);
                }
            }

            if (!empty($certConfig->showQrCode) && $verificationUrl !== '' && isset($positions['qr_code']) && is_array($positions['qr_code'])) {
                $qr = $positions['qr_code'];
                $pdf->write2DBarcode(
                    $verificationUrl,
                    'QRCODE,H',
                    (float) ($qr['x'] ?? 134),
                    (float) ($qr['y'] ?? 167),
                    (float) ($qr['w'] ?? 30),
                    (float) ($qr['h'] ?? 30),
                    [
                        'border' => 0,
                        'padding' => 0,
                        'fgcolor' => [15, 23, 42],
                        'bgcolor' => false,
                    ],
                    'N'
                );
            }

            $pdf->Output($outputPath, 'F');
            return is_file($outputPath) && filesize($outputPath) > 0;
        } catch (\Throwable $e) {
            log_message('error', 'Falha ao gerar certificado via template PDF: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Writes decorative text as a transparent image over the PDF.
     * This avoids TCPDF custom-font cache errors and keeps handwriting fonts reliable.
     */
    private function writeTextAsImage(
        TcpdfFpdi $pdf,
        string $text,
        string $fontPath,
        float $x,
        float $y,
        float $w,
        float $h,
        float $size,
        float $minSize,
        string $align = 'C',
        string $valign = 'B'
    ): bool {
        $text = trim($text);
        if ($text === '' || $fontPath === '' || !is_file($fontPath)) {
            return false;
        }

        if (!function_exists('imagecreatetruecolor') || !function_exists('imagettfbbox') || !function_exists('imagettftext')) {
            return false;
        }

        $dpi = 300;
        $pxPerMm = $dpi / 25.4;
        $lineYmm = $y + $h;

        $widthPx = max(10, (int) round($w * $pxPerMm));
        $fontSizePx = max(8, (float) ($size * $dpi / 72));
        $minFontSizePx = max(6, (float) ($minSize * $dpi / 72));

        $fitFontSize = $fontSizePx;
        $box = imagettfbbox($fitFontSize, 0, $fontPath, $text);
        while ($fitFontSize > $minFontSizePx && is_array($box)) {
            $textWidth = abs((int) $box[2] - (int) $box[0]);
            if ($textWidth <= ($widthPx - 12)) {
                break;
            }
            $fitFontSize -= 1;
            $box = imagettfbbox($fitFontSize, 0, $fontPath, $text);
        }

        if (!is_array($box)) {
            return false;
        }

        $xs = [(int) $box[0], (int) $box[2], (int) $box[4], (int) $box[6]];
        $ys = [(int) $box[1], (int) $box[3], (int) $box[5], (int) $box[7]];
        $minX = min($xs);
        $maxX = max($xs);
        $minY = min($ys);
        $maxY = max($ys);
        $textWidth = $maxX - $minX;
        $textHeight = $maxY - $minY;

        $bottomPadPx = 8;
        $topPadPx = 10;
        $heightPx = max((int) round($h * $pxPerMm), (int) round($textHeight + $bottomPadPx + $topPadPx));
        $drawH = $heightPx / $pxPerMm;
        $drawY = $lineYmm - $drawH;

        $image = imagecreatetruecolor($widthPx, $heightPx);
        if (!$image) {
            return false;
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefilledrectangle($image, 0, 0, $widthPx, $heightPx, $transparent);
        imagealphablending($image, true);

        $color = imagecolorallocate($image, 15, 23, 42);
        $align = strtoupper($align);
        $valign = strtoupper($valign);
        if ($align === 'R') {
            $textX = (int) round($widthPx - $textWidth - 6 - $minX);
        } elseif ($align === 'C') {
            $textX = (int) round(($widthPx - $textWidth) / 2 - $minX);
        } else {
            $textX = (int) round(6 - $minX);
        }

        if ($valign === 'T') {
            $textY = (int) round(6 - $minY);
        } elseif ($valign === 'M') {
            $textY = (int) round(($heightPx - $textHeight) / 2 - $minY);
        } else {
            // Assenta o texto sobre a linha pontilhada, com espaço extra para ascendentes.
            $textY = (int) round($heightPx - $bottomPadPx - $maxY);
        }

        imagettftext($image, $fitFontSize, 0, $textX, $textY, $color, $fontPath, $text);

        $cacheDir = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'cache';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0775, true);
        }

        $tmp = tempnam($cacheDir, 'cert_text_');
        if ($tmp === false) {
            imagedestroy($image);
            return false;
        }
        $tmpPng = $tmp . '.png';
        $saved = imagepng($image, $tmpPng);
        imagedestroy($image);
        @unlink($tmp);

        if (!$saved || !is_file($tmpPng)) {
            return false;
        }

        $pdf->Image($tmpPng, $x, $drawY, $w, $drawH, 'PNG', '', '', false, $dpi, '', false, false, 0, false, false, false);
        @unlink($tmpPng);

        return true;
    }

    /**
     * Resolve a certificate config path into an absolute readable file path.
     * Accepts either an absolute path or a path relative to FCPATH (public/).
     */
    private function resolveCertificateAssetPath(string $path): ?string
    {
        $path = trim($path);
        if ($path === '') {
            return null;
        }

        // Absolute path (Windows drive, UNC, or Unix root)
        $isAbsolute =
            preg_match('~^[A-Za-z]:[\\\\/]~', $path) === 1 ||
            str_starts_with($path, '\\\\') ||
            str_starts_with($path, '/');

        $candidate = $isAbsolute
            ? $path
            : rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($path, '/\\'));

        return is_file($candidate) ? $candidate : null;
    }

    private function generateCertificateNumber(string $courseName, int $year): string
    {
        $issuer = (string) (config('Certificate')->issuerCode ?? 'MT');
        $issuer = strtoupper(preg_replace('/[^A-Z0-9]/', '', $issuer) ?: 'MT');

        $code = $this->courseCodeFromName($courseName);
        $seq = $this->nextSequenceForYear($year);

        return sprintf('%s-%d-%s-%03d', $issuer, $year, $code, $seq);
    }

    private function courseCodeFromName(string $courseName): string
    {
        $courseName = trim($courseName);
        $first = preg_split('/\\s+/', $courseName)[0] ?? 'CURSO';
        $first = preg_replace('/[^A-Za-z0-9]/', '', (string) $first);
        $first = strtoupper($first);
        if ($first === '') {
            $first = 'CURSO';
        }
        return substr($first, 0, 10);
    }

    private function nextSequenceForYear(int $year): int
    {
        $issuer = (string) (config('Certificate')->issuerCode ?? 'MT');
        $issuer = strtoupper(preg_replace('/[^A-Z0-9]/', '', $issuer) ?: 'MT');
        $prefixDash = $issuer . '-' . $year . '-';
        $prefixSlash = $issuer . '/' . $year . '/';

        $rows = $this->db->table('certificates')
            ->select('number_certificate')
            ->groupStart()
                ->like('number_certificate', $prefixDash, 'after')
                ->orLike('number_certificate', $prefixSlash, 'after')
            ->groupEnd()
            ->where('number_certificate IS NOT NULL', null, false)
            ->get()
            ->getResultArray();

        $max = 0;
        $pattern = '~^' . preg_quote($issuer, '~') . '[-/]' . preg_quote((string) $year, '~') . '[-/][A-Z0-9]+[-/](\\d{3})$~';
        foreach ($rows as $row) {
            $num = (string) ($row['number_certificate'] ?? '');
            if ($num === '') {
                continue;
            }
            if (preg_match($pattern, $num, $m)) {
                $val = (int) $m[1];
                if ($val > $max) {
                    $max = $val;
                }
            }
        }

        return $max + 1;
    }
}
