<?php

namespace App\Libraries;

class AuthRedirect
{
    public static function remember(?string $url): void
    {
        $safe = self::sanitize($url);
        if ($safe === null) {
            return;
        }

        session()->setTempdata('beforeLoginUrl', $safe, 900);
    }

    public static function fromRequest(): void
    {
        $request = service('request');
        self::remember((string) $request->getGet('redirect'));
    }

    public static function sanitize(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        $base = rtrim((string) site_url('/'), '/');
        if ($base !== '' && str_starts_with($url, $base)) {
            return $url;
        }

        return null;
    }
}
