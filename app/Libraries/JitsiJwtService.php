<?php

namespace App\Libraries;

use Config\Jitsi;
use RuntimeException;

class JitsiJwtService
{
    private Jitsi $config;

    public function __construct(?Jitsi $config = null)
    {
        $this->config = $config ?? config('Jitsi');
    }

    public function isEnabled(): bool
    {
        return $this->config->enabled;
    }

    public function getDomain(): string
    {
        return trim($this->config->domain);
    }

    public function getDefaultRecordingMode(): string
    {
        return trim($this->config->defaultRecordingMode) ?: 'file';
    }

    public function getExternalApiScriptUrl(): string
    {
        $domain = $this->getDomain();
        $appId = trim((string) $this->config->appId);

        if ($appId !== '' && stripos($domain, '8x8.vc') !== false) {
            return 'https://' . $domain . '/' . $appId . '/external_api.js';
        }

        return 'https://' . $domain . '/external_api.js';
    }

    public function buildRoomName(string $room): string
    {
        $room = trim($room);
        if ($room === '') {
            throw new RuntimeException('Room name vazio para Jitsi.');
        }

        if ($this->config->appId !== '') {
            return $this->config->appId . '/' . $room;
        }

        return $room;
    }

    public function buildToken(
        string $room,
        array $userContext,
        bool $isModerator = false,
        array $featureOverrides = []
    ): ?string {
        $privateKey = $this->resolvePrivateKey();
        $kid = trim($this->config->jwtKeyId);

        if ($privateKey === null || $kid === '') {
            return null;
        }

        $now = time();
        $exp = $now + max(300, (int) $this->config->jwtTtlSeconds);
        $roomName = $this->buildRoomName($room);

        $features = array_merge([
            'recording' => true,
            'livestreaming' => true,
            'screen-sharing' => true,
            'outbound-call' => false,
            'transcription' => false,
        ], $featureOverrides);

        $payload = [
            'aud' => $this->config->jwtAudience,
            'iss' => $this->config->jwtIssuer,
            'sub' => $this->config->jwtSubject,
            'room' => $this->config->jwtRoom !== '' ? $this->config->jwtRoom : $roomName,
            'iat' => $now,
            'nbf' => $now - 5,
            'exp' => $exp,
            'context' => [
                'user' => [
                    'id' => (string) ($userContext['id'] ?? ''),
                    'name' => (string) ($userContext['name'] ?? 'Participante'),
                    'email' => (string) ($userContext['email'] ?? ''),
                    'avatar' => (string) ($userContext['avatar'] ?? ''),
                    'moderator' => $isModerator,
                ],
                'features' => $features,
            ],
        ];

        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
            'kid' => $kid,
        ];

        return $this->encodeRs256($header, $payload, $privateKey);
    }

    private function resolvePrivateKey(): ?string
    {
        $inline = trim($this->config->jwtPrivateKey);
        if ($inline !== '') {
            return str_replace('\n', "\n", $inline);
        }

        $path = trim($this->config->jwtPrivateKeyPath);
        if ($path === '') {
            return null;
        }

        $candidatePaths = [$path];

        // When running on Linux VPS, allow relative paths from project roots.
        if (! preg_match('#^([a-zA-Z]:[\\\\/]|/)#', $path)) {
            $candidatePaths[] = rtrim(ROOTPATH, '/\\') . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
            $candidatePaths[] = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
            $candidatePaths[] = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
        }

        foreach (array_unique($candidatePaths) as $candidate) {
            if (! is_file($candidate)) {
                continue;
            }

            $contents = file_get_contents($candidate);
            if ($contents === false) {
                continue;
            }

            return $contents;
        }

        return null;
    }

    private function encodeRs256(array $header, array $payload, string $privateKey): string
    {
        $encodedHeader = $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $encodedPayload = $this->base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $data = $encodedHeader . '.' . $encodedPayload;

        $signature = '';
        $ok = openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        if (! $ok) {
            throw new RuntimeException('Falha ao assinar JWT do Jitsi.');
        }

        return $data . '.' . $this->base64UrlEncode($signature);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
