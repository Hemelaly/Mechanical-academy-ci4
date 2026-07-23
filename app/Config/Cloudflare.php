<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Cloudflare Turnstile — verificação de página inteira.
 *
 * Chaves: https://dash.cloudflare.com/ → Turnstile
 * O hostname do site (ex. academy.mechanical.co.mz) tem de estar no widget.
 */
class Cloudflare extends BaseConfig
{
    public bool $turnstileEnabled = false;

    public string $turnstileSiteKey = '';

    public string $turnstileSecretKey = '';

    /** managed | non-interactive | invisible (definido no dashboard CF) */
    public string $turnstileAppearance = 'managed';

    /** Segundos até pedir nova verificação (default 24h). */
    public int $turnstileTtlSeconds = 86400;

    public string $siteverifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct()
    {
        parent::__construct();

        $this->turnstileEnabled = $this->envBool('cloudflare.turnstile.enabled', $this->turnstileEnabled);
        $this->turnstileSiteKey = trim((string) env('cloudflare.turnstile.siteKey', $this->turnstileSiteKey));
        $this->turnstileSecretKey = trim((string) env('cloudflare.turnstile.secretKey', $this->turnstileSecretKey));
        $this->turnstileAppearance = trim((string) env('cloudflare.turnstile.appearance', $this->turnstileAppearance));
        $this->turnstileTtlSeconds = max(60, (int) env('cloudflare.turnstile.ttlSeconds', $this->turnstileTtlSeconds));

        if ($this->turnstileSiteKey === '' || $this->turnstileSecretKey === '') {
            $this->turnstileEnabled = false;
        }
    }

    public function isTurnstileReady(): bool
    {
        return $this->turnstileEnabled
            && $this->turnstileSiteKey !== ''
            && $this->turnstileSecretKey !== '';
    }

    private function envBool(string $key, bool $default): bool
    {
        $raw = env($key, $default);
        if (is_bool($raw)) {
            return $raw;
        }

        return in_array(strtolower(trim((string) $raw)), ['1', 'true', 'yes', 'on'], true);
    }
}
