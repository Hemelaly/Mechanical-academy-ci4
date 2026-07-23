<?php

namespace App\Filters;

use App\Libraries\CloudflareTurnstile;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Cloudflare;

/**
 * Exige verificação Cloudflare Turnstile (página inteira) antes de aceder ao site.
 */
class CloudflareTurnstileFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $config = config(Cloudflare::class);
        if (! $config->isTurnstileReady()) {
            return null;
        }

        $turnstile = new CloudflareTurnstile($config);
        if ($turnstile->isVerified()) {
            return null;
        }

        $path = trim((string) $request->getUri()->getPath(), '/');

        // Normalizar se CI estiver num subdirectório
        if (str_starts_with($path, 'index.php/')) {
            $path = substr($path, strlen('index.php/'));
        }

        // Já na página de challenge / verify
        if ($path === 'cf-challenge' || str_starts_with($path, 'cf-challenge/')) {
            return null;
        }

        // Guardar destino para redirect após verificação
        $method = strtoupper((string) $request->getMethod());
        if ($method === 'GET' && ! $request->isAJAX()) {
            $uri = (string) $request->getUri();
            if ($uri !== '' && ! str_contains($uri, 'cf-challenge')) {
                session()->set('cf_turnstile_redirect', $uri);
            }
        }

        if ($request->isAJAX() || $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
            return service('response')
                ->setStatusCode(403)
                ->setJSON([
                    'ok'       => false,
                    'message'  => 'Verificação Cloudflare necessária.',
                    'redirect' => site_url('cf-challenge'),
                ]);
        }

        return redirect()->to(site_url('cf-challenge'));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
