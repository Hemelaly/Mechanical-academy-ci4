<?php
require __DIR__ . '/../../vendor/autoload.php';

use Config\Certificate;
use setasign\Fpdi\TcpdfFpdi;

$template = __DIR__ . '/../../public/assets/certificado/certificado.pdf';
$out = __DIR__ . '/cert_preview_final.pdf';

$ref = new ReflectionClass(Certificate::class);
$cfg = $ref->newInstanceWithoutConstructor();
$positions = (array) ($cfg->templatePositions ?? []);

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
$pdf->SetTextColor(26, 26, 26);

$sample = [
    'student_name' => 'João da Silva',
    'course_name' => 'Excel Moderno do Iniciante ao Intermédio',
    'instructor_name' => 'Gilberto Manhiça',
    'issued_date' => '21/05/2026',
    'concluded_date' => '21/05/2026',
];

foreach ($positions as $key => $pos) {
    if (!is_array($pos) || ($pos['type'] ?? '') === 'qr') {
        continue;
    }

    $x = (float) ($pos['x'] ?? 0);
    $y = (float) ($pos['y'] ?? 0);
    $w = (float) ($pos['w'] ?? 0);
    $h = (float) ($pos['h'] ?? 0);
    $fontSize = (float) ($pos['size'] ?? 12);
    $align = (string) ($pos['align'] ?? 'L');
    $bold = !empty($pos['bold']);
    $fontStyle = strtoupper(trim((string) ($pos['font_style'] ?? '')));
    $style = ($bold ? 'B' : '') . (str_contains($fontStyle, 'I') ? 'I' : '');

    $pdf->SetFont('times', $style !== '' ? $style : '', $fontSize);
    $pdf->SetXY($x, $y);
    $text = $sample[$key] ?? '';
    if ($text === '') {
        continue;
    }
    $pdf->MultiCell($w, $h, $text, 0, $align, false, 1, '', '', true, 0, false, true, $h, 'M');
}

$pdf->Output($out, 'F');
echo $out . PHP_EOL;
