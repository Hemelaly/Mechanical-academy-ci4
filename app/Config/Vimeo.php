<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Vimeo API (OAuth) — usado para puxar metadados de vídeos/pastas
 * e acelerar a montagem do currículo do curso.
 *
 * Dashboard: https://developer.vimeo.com/apps
 * Auth: https://developer.vimeo.com/api/authentication
 *
 * Scopes recomendados no access token:
 *   public, private, video_files
 * (edit/upload só se fores gravar/alterar no Vimeo)
 */
class Vimeo extends BaseConfig
{
    public bool $enabled = false;

    /** Client Identifier da app OAuth */
    public string $clientId = '';

    /** Client Secrets da app OAuth */
    public string $clientSecret = '';

    /**
     * Access token autenticado (Bearer).
     * Gera em My Apps → Authentication → Generate Access Token.
     */
    public string $accessToken = '';

    /**
     * Redirect URI registado na app (para fluxo OAuth futuro / refresh).
     * Ex.: https://academy.mechanical.co.mz/oauth/vimeo/callback
     */
    public string $redirectUri = '';

    public string $apiBaseUrl = 'https://api.vimeo.com';

    /** Header Accept: application/vnd.vimeo.*+json;version=3.4 */
    public string $apiVersion = '3.4';

    /** User id Vimeo (opcional). Vazio = usa /me */
    public string $userId = '';

    public int $timeoutSeconds = 30;

    public function __construct()
    {
        parent::__construct();

        $this->enabled = $this->envBool('vimeo.enabled', $this->enabled);
        $this->clientId = trim((string) env('vimeo.clientId', $this->clientId));
        $this->clientSecret = trim((string) env('vimeo.clientSecret', $this->clientSecret));
        $this->accessToken = trim((string) env('vimeo.accessToken', $this->accessToken));
        $this->redirectUri = trim((string) env('vimeo.redirectUri', $this->redirectUri));
        $this->apiBaseUrl = rtrim(trim((string) env('vimeo.apiBaseUrl', $this->apiBaseUrl)), '/');
        $this->apiVersion = trim((string) env('vimeo.apiVersion', $this->apiVersion));
        $this->userId = trim((string) env('vimeo.userId', $this->userId));
        $this->timeoutSeconds = max(5, (int) env('vimeo.timeoutSeconds', $this->timeoutSeconds));

        if ($this->accessToken === '') {
            $this->enabled = false;
        }
    }

    public function isReady(): bool
    {
        return $this->enabled && $this->accessToken !== '';
    }

    public function hasOAuthApp(): bool
    {
        return $this->clientId !== '' && $this->clientSecret !== '';
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
