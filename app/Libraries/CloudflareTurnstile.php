<?php

namespace App\Libraries;

use Config\Cloudflare;
use Config\Services;

class CloudflareTurnstile
{
    public const SESSION_KEY = 'cf_turnstile_verified_at';

    public function __construct(private ?Cloudflare $config = null)
    {
        $this->config = $config ?? config(Cloudflare::class);
    }

    public function isReady(): bool
    {
        return $this->config->isTurnstileReady();
    }

    public function isVerified(): bool
    {
        if (! $this->isReady()) {
            return true;
        }

        $verifiedAt = (int) (session()->get(self::SESSION_KEY) ?? 0);
        if ($verifiedAt <= 0) {
            return false;
        }

        $ttl = max(60, (int) $this->config->turnstileTtlSeconds);

        return (time() - $verifiedAt) < $ttl;
    }

    public function markVerified(): void
    {
        session()->set(self::SESSION_KEY, time());
    }

    public function clear(): void
    {
        session()->remove(self::SESSION_KEY);
    }

    /**
     * @return array{ok: bool, message: string, codes?: list<string>}
     */
    public function verifyToken(string $token, ?string $remoteIp = null): array
    {
        $token = trim($token);
        if ($token === '') {
            return ['ok' => false, 'message' => 'Token Turnstile em falta.'];
        }

        if (! $this->isReady()) {
            return ['ok' => false, 'message' => 'Turnstile não está configurado.'];
        }

        $client = Services::curlrequest([
            'timeout'     => 10,
            'http_errors' => false,
        ]);

        try {
            $response = $client->post($this->config->siteverifyUrl, [
                'form_params' => array_filter([
                    'secret'   => $this->config->turnstileSecretKey,
                    'response' => $token,
                    'remoteip' => $remoteIp ?: null,
                ]),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Turnstile verify failed: {msg}', ['msg' => $e->getMessage()]);

            return ['ok' => false, 'message' => 'Não foi possível contactar a Cloudflare.'];
        }

        $body = json_decode((string) $response->getBody(), true);
        if (! is_array($body)) {
            return ['ok' => false, 'message' => 'Resposta inválida da Cloudflare.'];
        }

        if (! empty($body['success'])) {
            return ['ok' => true, 'message' => 'Verificado.'];
        }

        $codes = is_array($body['error-codes'] ?? null) ? $body['error-codes'] : [];

        return [
            'ok'      => false,
            'message' => 'Verificação Cloudflare falhou. Tente novamente.',
            'codes'   => $codes,
        ];
    }
}
