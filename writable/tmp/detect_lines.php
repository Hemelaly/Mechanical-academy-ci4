<?php
$imgPath = __DIR__ . '/../../public/assets/certificado/certificado-bg.png';
$img = imagecreatefrompng($imgPath);
$w = imagesx($img);
$h = imagesy($img);

$pageW = 210.07916666667;
$pageH = 147.90208192222;

function isDark($img, int $x, int $y): bool
{
    $rgb = imagecolorat($img, $x, $y);
    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;
    return ($r + $g + $b) / 3 < 120;
}

// Scan horizontal bands for dotted lines (many dark pixels in a row)
$lines = [];
for ($y = 0; $y < $h; $y++) {
    $darkCount = 0;
    $segments = 0;
    $inDark = false;
    for ($x = 0; $x < $w; $x++) {
        $dark = isDark($img, $x, $y);
        if ($dark) {
            $darkCount++;
            if (!$inDark) {
                $segments++;
                $inDark = true;
            }
        } else {
            $inDark = false;
        }
    }
    // Dotted line: many segments, moderate dark pixel ratio
    if ($segments >= 15 && $darkCount > 80 && $darkCount < $w * 0.6) {
        $lines[] = ['y' => $y, 'segments' => $segments, 'dark' => $darkCount];
    }
}

// Group nearby y values
$grouped = [];
foreach ($lines as $line) {
    $found = false;
    foreach ($grouped as &$g) {
        if (abs($g['y'] - $line['y']) <= 2) {
            $g['count']++;
            $g['y'] = (int) round(($g['y'] + $line['y']) / 2);
            $found = true;
            break;
        }
    }
    unset($g);
    if (!$found) {
        $grouped[] = ['y' => $line['y'], 'count' => 1];
    }
}

usort($grouped, fn($a, $b) => $a['y'] <=> $b['y']);

echo "Image: {$w}x{$h}px\n";
echo "Page: {$pageW}x{$pageH}mm\n\n";
echo "Dotted lines detected:\n";
foreach ($grouped as $g) {
    if ($g['count'] < 3) continue;
    $ymm = round($g['y'] * $pageH / $h, 1);
    // Find x extent of line on this row
    $xStart = 0; $xEnd = 0;
    for ($x = 0; $x < $w; $x++) {
        if (isDark($img, $x, $g['y'])) {
            if ($xStart === 0) $xStart = $x;
            $xEnd = $x;
        }
    }
    $xmm = round($xStart * $pageW / $w, 1);
    $xmmEnd = round($xEnd * $pageW / $w, 1);
    $widthMm = round(($xEnd - $xStart) * $pageW / $w, 1);
    echo "  y={$ymm}mm (px {$g['y']}), x={$xmm}-{$xmmEnd}mm, width={$widthMm}mm\n";
}
