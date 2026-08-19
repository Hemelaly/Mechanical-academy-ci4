<?php

declare(strict_types=1);

$source = __DIR__ . '/../public/assets/img/favicon.png';
$public = __DIR__ . '/../public';

if (! is_file($source)) {
    fwrite(STDERR, "Source favicon not found: {$source}\n");
    exit(1);
}

$src = imagecreatefrompng($source);
if ($src === false) {
    fwrite(STDERR, "Could not read PNG\n");
    exit(1);
}

imagesavealpha($src, true);

function academy_resize_png($src, int $size)
{
    $dst = imagecreatetruecolor($size, $size);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefilledrectangle($dst, 0, 0, $size, $size, $transparent);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $size, $size, imagesx($src), imagesy($src));

    ob_start();
    imagepng($dst, null, 9);
    $png = (string) ob_get_clean();
    imagedestroy($dst);

    return $png;
}

$png16 = academy_resize_png($src, 16);
$png32 = academy_resize_png($src, 32);
$png180 = academy_resize_png($src, 180);
imagedestroy($src);

file_put_contents($public . '/favicon-16.png', $png16);
file_put_contents($public . '/favicon-32.png', $png32);
file_put_contents($public . '/apple-touch-icon.png', $png180);

$images = [$png16, $png32];
$count  = count($images);
$offset = 6 + (16 * $count);
$ico    = pack('vvv', 0, 1, $count);

foreach ($images as $png) {
    $info = getimagesizefromstring($png);
    $w    = (int) ($info[0] ?? 32);
    $h    = (int) ($info[1] ?? 32);
    $size = strlen($png);
    $ico .= pack(
        'CCCCvvVV',
        $w >= 256 ? 0 : $w,
        $h >= 256 ? 0 : $h,
        0,
        0,
        1,
        32,
        $size,
        $offset
    );
    $offset += $size;
}

foreach ($images as $png) {
    $ico .= $png;
}

file_put_contents($public . '/favicon.ico', $ico);
file_put_contents($public . '/favicon.png', $png32);

echo "Wrote favicon.ico, favicon.png, favicon-16.png, favicon-32.png, apple-touch-icon.png\n";
