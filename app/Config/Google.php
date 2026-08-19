<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Google OAuth (login / cadastro).
 *
 * Google Cloud Console → APIs & Services → Credentials → OAuth 2.0 Client
 * Authorized redirect URI: {baseURL}/auth/google/callback
 */
class Google extends BaseConfig
{
    public string $clientId = '';

    public string $clientSecret = '';

    /**
     * Vazio = site_url('auth/google/callback')
     */
    public string $redirectUri = '';

    public string $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth';

    public string $tokenUrl = 'https://oauth2.googleapis.com/token';

    public string $userInfoUrl = 'https://www.googleapis.com/oauth2/v3/userinfo';

    public function __construct()
    {
        parent::__construct();

        $clientId     = $this->firstEnv(['google.clientId', 'GOOGLE_CLIENT_ID'], $this->clientId);
        $clientSecret = $this->firstEnv(['google.clientSecret', 'GOOGLE_CLIENT_SECRET'], $this->clientSecret);
        $redirectUri  = $this->firstEnv(['google.redirectUri', 'GOOGLE_REDIRECT_URI'], $this->redirectUri);

        if ($clientId !== '') {
            $this->clientId = $clientId;
        }
        if ($clientSecret !== '') {
            $this->clientSecret = $clientSecret;
        }
        if ($redirectUri !== '') {
            $this->redirectUri = $redirectUri;
        }
    }

    public function isConfigured(): bool
    {
        return $this->clientId !== '' && $this->clientSecret !== '';
    }

    /**
     * O botão pode aparecer só com o Client ID; o secret é obrigatório no callback.
     */
    public function hasClientId(): bool
    {
        return $this->clientId !== '';
    }

    /**
     * @param list<string> $keys
     */
    private function firstEnv(array $keys, string $fallback = ''): string
    {
        foreach ($keys as $key) {
            $raw = env($key);
            if ($raw === null || $raw === false) {
                continue;
            }

            $value = trim((string) $raw, " \t\n\r\0\x0B'\"");
            if ($value !== '') {
                return $value;
            }
        }

        return trim($fallback);
    }

    public function callbackUrl(): string
    {
        if ($this->redirectUri !== '') {
            return $this->redirectUri;
        }

        return site_url('auth/google/callback');
    }
}
