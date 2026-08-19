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

        $this->clientId     = trim((string) env('google.clientId', $this->clientId));
        $this->clientSecret = trim((string) env('google.clientSecret', $this->clientSecret));
        $this->redirectUri  = trim((string) env('google.redirectUri', $this->redirectUri));
    }

    public function isConfigured(): bool
    {
        return $this->clientId !== '' && $this->clientSecret !== '';
    }

    public function callbackUrl(): string
    {
        if ($this->redirectUri !== '') {
            return $this->redirectUri;
        }

        return site_url('auth/google/callback');
    }
}
