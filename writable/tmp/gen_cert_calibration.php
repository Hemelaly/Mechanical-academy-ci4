<?php
require __DIR__ . '/../../vendor/autoload.php';

use setasign\Fpdi\TcpdfFpdi;

$template = __DIR__ . '/../../public/assets/certificado/Certtificado.pdf';
$out = __DIR__ . '/cert_calibration.pdf';

$ref = new ReflectionClass(\Config\Certificate::class);
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

foreach ($positions as $key => $pos) {
    if (!is_array($pos)) {
        continue;
    }

    $x = (float) ($pos['x'] ?? 0);
    $y = (float) ($pos['y'] ?? 0);
    $w = (float) ($pos['w'] ?? 0);
    $h = (float) ($pos['h'] ?? 0);

    $pdf->SetDrawColor(220, 38, 38);
    $pdf->SetLineWidth(0.2);
    $pdf->Rect($x, $y, $w, $h);

    $pdf->SetFont('helvetica', '', 5);
    $pdf->SetTextColor(220, 38, 38);
    $pdf->SetXY($x, max(0, $y - 3));
    $pdf->Cell($w, 3, $key, 0, 0, 'L');
}

$pdf->Output($out, 'F');
echo $out . PHP_EOL;
