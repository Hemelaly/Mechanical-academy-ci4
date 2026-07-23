<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Cloudflare Turnstile — verificação de página inteira.
 *
 * Chaves: https://dash.cloudflare.com/ → Turnstile
 */
class Cloudflare extends BaseConfig
{
    /**
     * Se false ou chaves vazias, o filtro não bloqueia.
     */
    public bool $turnstileEnabled = false;

    public string $turnstileSiteKey = '';

    public string $turnstileSecretKey = '';

    /**
     * managed | non-interactive | invisible
     */
    public string $turnstileAppearance = 'managed';

    /**
     * Segundos até pedir nova verificação (default 24h).
     */
    public int $turnstileTtlSeconds = 86400;

    public string $siteverifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct()
    {
        parent::__construct();

        $this->turnstileEnabled = filter_var(
            env('cloudflare.turnstile.enabled', $this->turnstileEnabled),
            FILTER_VALIDATE_BOOLEAN
        );
        $this->turnstileSiteKey = trim((string) env('cloudflare.turnstile.siteKey', $this->turnstileSiteKey));
        $this->turnstileSecretKey = trim((string) env('cloudflare.turnstile.secretKey', $this->turnstileSecretKey));
        $this->turnstileAppearance = trim((string) env('cloudflare.turnstile.appearance', $this->turnstileAppearance));
        $this->turnstileTtlSeconds = (int) env('cloudflare.turnstile.ttlSeconds', $this->turnstileTtlSeconds);

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
}
