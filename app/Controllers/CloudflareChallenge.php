<?php

namespace App\Controllers;

use App\Libraries\CloudflareTurnstile;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Cloudflare;

class CloudflareChallenge extends BaseController
{
    public function index()
    {
        $config = config(Cloudflare::class);
        $turnstile = new CloudflareTurnstile($config);

        if (! $config->isTurnstileReady() || $turnstile->isVerified()) {
            return redirect()->to($this->safeRedirect());
        }

        return view('cloudflare_challenge', [
            'siteKey'    => $config->turnstileSiteKey,
            'appearance' => $config->turnstileAppearance,
            'error'      => session()->getFlashdata('cf_error'),
        ]);
    }

    public function verify(): ResponseInterface
    {
        $config = config(Cloudflare::class);
        $turnstile = new CloudflareTurnstile($config);

        if (! $config->isTurnstileReady()) {
            return redirect()->to(site_url('/'));
        }

        if ($turnstile->isVerified()) {
            return redirect()->to($this->safeRedirect());
        }

        $token = (string) ($this->request->getPost('cf-turnstile-response')
            ?? $this->request->getPost('cf_turnstile_response')
            ?? '');

        $result = $turnstile->verifyToken($token, $this->request->getIPAddress());

        if (! $result['ok']) {
            if ($this->request->isAJAX() || $this->wantsJson()) {
                return $this->response->setStatusCode(422)->setJSON([
                    'ok'      => false,
                    'message' => $result['message'],
                    'codes'   => $result['codes'] ?? [],
                ]);
            }

            return redirect()->to(site_url('cf-challenge'))
                ->with('cf_error', $result['message']);
        }

        $turnstile->markVerified();
        $target = $this->safeRedirect();
        session()->remove('cf_turnstile_redirect');

        if ($this->request->isAJAX() || $this->wantsJson()) {
            return $this->response->setJSON([
                'ok'       => true,
                'redirect' => $target,
            ]);
        }

        return redirect()->to($target);
    }

    private function safeRedirect(): string
    {
        $fallback = site_url('/');
        $stored = (string) (session()->get('cf_turnstile_redirect') ?? '');
        if ($stored === '') {
            return $fallback;
        }

        $base = rtrim((string) config('App')->baseURL, '/');
        if ($base !== '' && str_starts_with($stored, $base)) {
            return $stored;
        }

        // path relativo
        if (str_starts_with($stored, '/') && ! str_starts_with($stored, '//')) {
            return site_url(ltrim($stored, '/'));
        }

        return $fallback;
    }

    private function wantsJson(): bool
    {
        $accept = strtolower((string) $this->request->getHeaderLine('Accept'));

        return str_contains($accept, 'application/json');
    }
}
