<?php
require __DIR__ . '/../../vendor/autoload.php';

$pdf = new setasign\Fpdi\TcpdfFpdi('L', 'mm', 'A4', true, 'UTF-8', false);
$path = __DIR__ . '/../../public/assets/certificado/Certtificado.pdf';
$pageCount = $pdf->setSourceFile($path);
$tplId = $pdf->importPage(1);
$size = $pdf->getTemplateSize($tplId);
echo "pages={$pageCount}\n";
print_r($size);
