<?php

namespace App\Filters;

use App\Services\AnalyticsService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Regista pageviews HTML (GET) para analytics.
 */
class AnalyticsTrackFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        try {
            if (strtoupper((string) $request->getMethod()) !== 'GET') {
                return;
            }

            $status = (int) $response->getStatusCode();
            if ($status >= 400) {
                return;
            }

            $contentType = strtolower((string) $response->getHeaderLine('Content-Type'));
            if ($contentType !== '' && ! str_contains($contentType, 'text/html')) {
                return;
            }

            if ($request->isAJAX() || $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                return;
            }

            $accept = strtolower((string) $request->getHeaderLine('Accept'));
            if ($accept !== '' && str_contains($accept, 'application/json') && ! str_contains($accept, 'text/html')) {
                return;
            }

            $path = '/' . ltrim((string) $request->getUri()->getPath(), '/');
            $analytics = new AnalyticsService();
            if ($analytics->shouldIgnorePath($path)) {
                return;
            }

            $ua = (string) $request->getUserAgent();
            $userId = null;
            try {
                $user = service('auth')->user();
                $userId = $user ? (int) $user->id : null;
            } catch (\Throwable $e) {
                $userId = null;
            }

            $visitorId = (string) ($request->getCookie('ma_vid') ?? '');
            if ($visitorId === '') {
                $visitorId = bin2hex(random_bytes(16));
                $response->setCookie('ma_vid', $visitorId, 60 * 60 * 24 * 365, '', '/', '', false, false);
            }

            $analytics->track([
                'event_type' => 'pageview',
                'path'       => $path,
                'referrer'   => (string) ($request->getServer('HTTP_REFERER') ?? ''),
                'persona'    => $analytics->resolvePersonaFromAuth(),
                'user_id'    => $userId,
                'visitor_id' => $visitorId,
                'session_id' => session_id() ?: null,
                'ip_address' => $request->getIPAddress(),
                'user_agent' => $ua,
                'device'     => $analytics->detectDevice($ua),
            ]);
        } catch (\Throwable $e) {
            // Nunca quebrar a resposta por causa de analytics.
            log_message('debug', 'Analytics filter error: ' . $e->getMessage());
        }
    }
}
