<?php
define('WRITEPATH', __DIR__ . '/../../writable/');
require __DIR__ . '/../../vendor/autoload.php';

use App\Services\CertificateService;
use Config\Certificate;
use ReflectionClass;
use setasign\Fpdi\TcpdfFpdi;

$template = __DIR__ . '/../../public/assets/certificado/certificado.pdf';
$out = __DIR__ . '/cert_preview_aligned.pdf';

$ref = new ReflectionClass(Certificate::class);
$cfg = $ref->newInstanceWithoutConstructor();
$positions = (array) ($cfg->templatePositions ?? []);

$studentFont = __DIR__ . '/../../public/assets/fonts/GreatVibes-Regular.ttf';
$signatureFont = __DIR__ . '/../../public/assets/fonts/MsMadi-Regular.ttf';

$pdf = new TcpdfFpdi('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->SetMargins(0, 0, 0);
$pdf->setSourceFile($template);
$tplId = $pdf->importPage(1);
$size = $pdf->getTemplateSize($tplId);
$pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
$pdf->useTemplate($tplId);

$svc = new ReflectionClass(CertificateService::class);
$service = $svc->newInstanceWithoutConstructor();
$writeImage = $svc->getMethod('writeTextAsImage');
$writeImage->setAccessible(true);

$sample = [
    'student_name' => 'Hemel Aly',
    'course_name' => 'Excel Moderno do Iniciante ao Intermédio',
    'instructor_name' => 'Gilberto Manhiça',
    'issued_date' => '16/06/2026',
    'concluded_date' => '16/06/2026',
];

foreach ($positions as $key => $pos) {
    if (!is_array($pos) || ($pos['type'] ?? '') === 'qr') {
        continue;
    }

    $text = $sample[$key] ?? '';
    if ($text === '') {
        continue;
    }

    $x = (float) ($pos['x'] ?? 0);
    $y = (float) ($pos['y'] ?? 0);
    $w = (float) ($pos['w'] ?? 0);
    $h = (float) ($pos['h'] ?? 0);
    $fontSize = (float) ($pos['size'] ?? 12);
    $minSize = (float) ($pos['min_size'] ?? max(8, $fontSize - 6));
    $align = (string) ($pos['align'] ?? 'C');
    $valign = strtoupper((string) ($pos['valign'] ?? 'B'));
    $fontRole = strtolower(trim((string) ($pos['font_role'] ?? '')));

    $fontPath = match ($fontRole) {
        'student', 'course' => $studentFont,
        'signature' => $signatureFont,
        default => '',
    };

    if ($fontPath !== '' && is_file($fontPath)) {
        $writeImage->invoke($service, $pdf, $text, $fontPath, $x, $y, $w, $h, $fontSize, $minSize, $align, $valign);
        continue;
    }

    $pdf->SetFont('helvetica', '', $fontSize);
    $pdf->SetTextColor(26, 26, 26);
    $pdf->SetXY($x, $y);
    $pdf->MultiCell($w, $h, $text, 0, $align, false, 1, '', '', true, 0, false, true, $h, $valign);
}

$pdf->Output($out, 'F');
echo $out . PHP_EOL;
