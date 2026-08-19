<?php

declare(strict_types=1);

/**
 * Upsert key=value pairs into a dotenv file.
 * Usage: php scripts/upsert_env.php /path/.env BASE64_JSON
 *
 * BASE64_JSON example: {"google.clientId":"...","google.clientSecret":"..."}
 */
$file = $argv[1] ?? '';
$encoded = $argv[2] ?? '';

if ($file === '' || $encoded === '') {
    fwrite(STDERR, "usage: php scripts/upsert_env.php .env BASE64_JSON\n");
    exit(1);
}

$pairs = json_decode((string) base64_decode($encoded, true), true);
if (! is_array($pairs) || $pairs === []) {
    fwrite(STDERR, "invalid payload\n");
    exit(1);
}

$env = is_file($file) ? (string) file_get_contents($file) : '';

foreach ($pairs as $key => $value) {
    $key = trim((string) $key);
    $value = trim((string) $value);
    if ($key === '' || $value === '') {
        continue;
    }

    $line = $key . ' = ' . $value;
    $pattern = '/^' . preg_quote($key, '/') . '\s*=.*$/m';

    if (preg_match($pattern, $env) === 1) {
        $env = (string) preg_replace($pattern, $line, $env, 1);
    } else {
        $env = rtrim($env) . "\n\n" . $line . "\n";
    }
}

$dir = dirname($file);
if (! is_dir($dir)) {
    mkdir($dir, 0775, true);
}

file_put_contents($file, $env);
