<?php
$imgPath = __DIR__ . '/../../public/assets/certificado/certificado-bg.png';
$img = imagecreatefrompng($imgPath);
$imgW = imagesx($img);
$imgH = imagesy($img);
$pageW = 210.07916666667;
$pageH = 147.90208192222;

function scanLine($img, int $y, int $imgW, float $pageW, float $pageH, int $imgH): ?array
{
    $darkXs = [];
    for ($x = 0; $x < $imgW; $x++) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        if (($r + $g + $b) / 3 < 95) {
            $darkXs[] = $x;
        }
    }
    if (count($darkXs) < 25) return null;

    $segments = 1;
    for ($i = 1; $i < count($darkXs); $i++) {
        if ($darkXs[$i] - $darkXs[$i - 1] > 10) $segments++;
    }
    if ($segments < 10) return null;

    $x0 = $darkXs[0];
    $x1 = $darkXs[count($darkXs) - 1];
    return [
        'y_mm' => round($y * $pageH / $imgH, 2),
        'x0_mm' => round($x0 * $pageW / $imgW, 2),
        'x1_mm' => round($x1 * $pageW / $imgW, 2),
        'w_mm' => round(($x1 - $x0) * $pageW / $imgW, 2),
        'segments' => $segments,
    ];
}

$results = [];
$hits = [];
for ($y = 0; $y < $imgH; $y++) {
    $r = scanLine($img, $y, $imgW, $pageW, $pageH, $imgH);
    if (!$r) continue;
    $key = (string) round($r['y_mm'], 0);
    $hits[$key] = ($hits[$key] ?? 0) + 1;
    if (($hits[$key] ?? 0) === 1) $results[] = $r;
}

usort($results, fn($a, $b) => $a['y_mm'] <=> $b['y_mm']);

echo "All dotted lines:\n";
foreach ($results as $r) {
    printf("  y=%5.1f | x=%5.1f-%5.1f | w=%5.1f | seg=%d\n", $r['y_mm'], $r['x0_mm'], $r['x1_mm'], $r['w_mm'], $r['segments']);
}
