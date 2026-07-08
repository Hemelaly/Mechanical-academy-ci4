<?php
$imgPath = __DIR__ . '/../../public/assets/certificado/certificado-bg.png';
$img = imagecreatefrompng($imgPath);
$w = imagesx($img);
$h = imagesy($img);
$pageW = 210.07916666667;
$pageH = 147.90208192222;
$imgH = imagesy($img);

function rowStats($img, int $y, int $imgW, float $pageW, float $pageH, int $imgH): array
{
    $darkXs = [];
    for ($x = 0; $x < $imgW; $x++) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        if (($r + $g + $b) / 3 < 100) {
            $darkXs[] = $x;
        }
    }
    if (count($darkXs) < 30) {
        return [];
    }

    $segments = 1;
    for ($i = 1; $i < count($darkXs); $i++) {
        if ($darkXs[$i] - $darkXs[$i - 1] > 8) {
            $segments++;
        }
    }

    $xStart = $darkXs[0];
    $xEnd = $darkXs[count($darkXs) - 1];
    $widthPx = $xEnd - $xStart;
    $centerX = ($xStart + $xEnd) / 2;

    return [
        'y_px' => $y,
        'y_mm' => round($y * $pageH / $imgH, 2),
        'x_start_mm' => round($xStart * $pageW / $imgW, 2),
        'x_end_mm' => round($xEnd * $pageW / $imgW, 2),
        'width_mm' => round($widthPx * $pageW / $imgW, 2),
        'center_x_mm' => round($centerX * $pageW / $imgW, 2),
        'segments' => $segments,
        'dark_count' => count($darkXs),
    ];
}

$candidates = [];
for ($row = 0; $row < $imgH; $row++) {
    $stats = rowStats($img, $row, $w, $pageW, $pageH, $imgH);
    if ($stats === []) {
        continue;
    }
    // Dotted lines have many small segments
    if ($stats['segments'] < 12) {
        continue;
    }
    // Avoid very wide decorative lines
    if ($stats['width_mm'] > 150) {
        continue;
    }
    $candidates[] = $stats;
}

// Merge rows within 3px
$merged = [];
foreach ($candidates as $c) {
    $found = false;
    foreach ($merged as &$m) {
        if (abs($m['y_px'] - $c['y_px']) <= 3) {
            $m['hits'] = ($m['hits'] ?? 1) + 1;
            $found = true;
            break;
        }
    }
    unset($m);
    if (!$found) {
        $c['hits'] = 1;
        $merged[] = $c;
    }
}

usort($merged, fn($a, $b) => $a['y_mm'] <=> $b['y_mm']);

echo "Candidate dotted lines (form fields):\n";
foreach ($merged as $m) {
    if (($m['hits'] ?? 1) < 2) {
        continue;
    }
    printf(
        "  y=%.1fmm | x=%.1f-%.1f | w=%.1f | center=%.1f | segments=%d\n",
        $m['y_mm'],
        $m['x_start_mm'],
        $m['x_end_mm'],
        $m['width_mm'],
        $m['center_x_mm'],
        $m['segments']
    );
}

// Suggest text positions (baseline ~1.5mm above line)
echo "\nSuggested text positions (baseline on line):\n";
$fields = [
    'date' => fn($m) => $m['width_mm'] < 55 && $m['y_mm'] < 40,
    'student' => fn($m) => $m['width_mm'] > 100 && $m['y_mm'] > 55 && $m['y_mm'] < 80,
    'course' => fn($m) => $m['width_mm'] > 100 && $m['y_mm'] > 80 && $m['y_mm'] < 105,
    'instructor' => fn($m) => $m['width_mm'] < 70 && $m['y_mm'] > 105,
];

foreach ($fields as $name => $filter) {
    foreach ($merged as $m) {
        if (($m['hits'] ?? 1) < 2) continue;
        if ($filter($m)) {
            $textY = round($m['y_mm'] - 4.5, 1); // baseline above line
            $textX = round($m['x_start_mm'], 1);
            $textW = round($m['width_mm'], 1);
            echo "  {$name}: x={$textX}, y={$textY}, w={$textW}\n";
            break;
        }
    }
}
