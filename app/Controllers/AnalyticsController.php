<?php

namespace App\Controllers;

use App\Services\AnalyticsService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Endpoint público leve para eventos client-side (cliques, etc.).
 */
class AnalyticsController extends BaseController
{
    public function collect(): ResponseInterface
    {
        $analytics = new AnalyticsService();
        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost();
        }
        if (! is_array($payload)) {
            $payload = [];
        }

        $events = [];
        if (isset($payload['events']) && is_array($payload['events'])) {
            $events = $payload['events'];
        } elseif ($payload !== []) {
            $events = [$payload];
        }

        $visitorId = trim((string) ($this->request->getCookie('ma_vid') ?? ''));
        if ($visitorId === '') {
            $visitorId = bin2hex(random_bytes(16));
            $this->response->setCookie('ma_vid', $visitorId, 60 * 60 * 24 * 365, '', '/', '', false, false);
        }

        $userId = null;
        try {
            $user = service('auth')->user();
            $userId = $user ? (int) $user->id : null;
        } catch (\Throwable $e) {
            $userId = null;
        }

        $ua = (string) $this->request->getUserAgent();
        $persona = $analytics->resolvePersonaFromAuth();
        $ip = $this->request->getIPAddress();
        $sessionId = session_id() ?: null;
        $enriched = [];

        foreach ($events as $event) {
            if (! is_array($event)) {
                continue;
            }
            $enriched[] = array_merge($event, [
                'persona'    => $event['persona'] ?? $persona,
                'user_id'    => $userId,
                'visitor_id' => $visitorId,
                'session_id' => $sessionId,
                'ip_address' => $ip,
                'user_agent' => $ua,
                'device'     => $event['device'] ?? $analytics->detectDevice($ua),
                'path'       => $event['path'] ?? (string) $this->request->getUri()->getPath(),
                'referrer'   => $event['referrer'] ?? (string) ($this->request->getServer('HTTP_REFERER') ?? ''),
            ]);
        }

        $analytics->trackBatch(array_slice($enriched, 0, 40));

        return $this->response
            ->setStatusCode(204)
            ->setHeader('Content-Type', 'application/json; charset=UTF-8')
            ->setHeader('Cache-Control', 'no-store')
            ->setBody('');
    }
}
