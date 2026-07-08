<?php
/**
 * Analisa o template e gera imagem PNG com grelha de calibração.
 */
require __DIR__ . '/../../vendor/autoload.php';

use setasign\Fpdi\TcpdfFpdi;

$template = __DIR__ . '/../../public/assets/certificado/certificado.pdf';
$outPdf = __DIR__ . '/cert_grid.pdf';
$outPng = __DIR__ . '/cert_grid.png';

$pdf = new TcpdfFpdi('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->SetMargins(0, 0, 0);
$pdf->setSourceFile($template);
$tplId = $pdf->importPage(1);
$size = $pdf->getTemplateSize($tplId);
$w = $size['width'];
$h = $size['height'];
$pdf->AddPage($size['orientation'], [$w, $h]);
$pdf->useTemplate($tplId);

// Grelha horizontal a cada 5mm
$pdf->SetDrawColor(255, 0, 0);
$pdf->SetTextColor(255, 0, 0);
$pdf->SetFont('helvetica', '', 4);
for ($y = 0; $y <= $h; $y += 5) {
    $pdf->SetLineWidth(0.05);
    $pdf->Line(0, $y, $w, $y);
    $pdf->SetXY(0.5, $y + 0.2);
    $pdf->Cell(8, 2, (string) $y, 0, 0, 'L');
}

// Grelha vertical a cada 10mm
for ($x = 0; $x <= $w; $x += 10) {
    $pdf->Line($x, 0, $x, $h);
    $pdf->SetXY($x + 0.2, 1);
    $pdf->Cell(8, 2, (string) $x, 0, 0, 'L');
}

// Marcar posições atuais
$ref = new ReflectionClass(\Config\Certificate::class);
$cfg = $ref->newInstanceWithoutConstructor();
$positions = (array) ($cfg->templatePositions ?? []);

$pdf->SetDrawColor(0, 0, 255);
$pdf->SetTextColor(0, 0, 255);
$pdf->SetFont('helvetica', 'B', 5);
foreach ($positions as $key => $pos) {
    if (!is_array($pos)) continue;
    $x = (float) ($pos['x'] ?? 0);
    $y = (float) ($pos['y'] ?? 0);
    $pw = (float) ($pos['w'] ?? 0);
    $ph = (float) ($pos['h'] ?? 0);
    $pdf->Rect($x, $y, $pw, $ph);
    $pdf->SetXY($x, max(0, $y - 2.5));
    $pdf->Cell($pw, 2, $key, 0, 0, 'L');
}

$pdf->Output($outPdf, 'F');

// Exportar como PNG via Imagick ou GD+ghostscript fallback
if (extension_loaded('imagick')) {
    $im = new Imagick();
    $im->setResolution(200, 200);
    $im->readImage($outPdf . '[0]');
    $im->setImageFormat('png');
    $im->writeImage($outPng);
    echo "PNG: $outPng\n";
} else {
    // TCPDF built-in image export
    echo "PDF grid: $outPdf (no imagick)\n";
    echo "Size: {$w} x {$h} mm\n";
}
