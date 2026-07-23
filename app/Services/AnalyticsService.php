<?php

namespace App\Services;

use App\Models\AnalyticsEventModel;

class AnalyticsService
{
    private AnalyticsEventModel $model;

    public function __construct(?AnalyticsEventModel $model = null)
    {
        $this->model = $model ?? new AnalyticsEventModel();
    }

    public function track(array $payload): bool
    {
        $eventType = $this->normalizeEventType((string) ($payload['event_type'] ?? ''));
        $path = $this->normalizePath((string) ($payload['path'] ?? '/'));

        if ($eventType === '' || $this->shouldIgnorePath($path)) {
            return false;
        }

        $meta = $payload['meta'] ?? [];
        if (! is_array($meta)) {
            $meta = [];
        }

        $row = [
            'event_type'  => $eventType,
            'path'        => mb_substr($path, 0, 255),
            'route_label' => $this->truncate((string) ($payload['route_label'] ?? $this->labelForPath($path)), 120),
            'referrer'    => $this->truncate((string) ($payload['referrer'] ?? ''), 255) ?: null,
            'element'     => $this->truncate((string) ($payload['element'] ?? ''), 180) ?: null,
            'persona'     => $this->normalizePersona((string) ($payload['persona'] ?? 'guest')),
            'user_id'     => isset($payload['user_id']) && (int) $payload['user_id'] > 0 ? (int) $payload['user_id'] : null,
            'visitor_id'  => $this->truncate((string) ($payload['visitor_id'] ?? ''), 64) ?: null,
            'session_id'  => $this->truncate((string) ($payload['session_id'] ?? ''), 128) ?: null,
            'ip_address'  => $this->truncate((string) ($payload['ip_address'] ?? ''), 45) ?: null,
            'user_agent'  => $this->truncate((string) ($payload['user_agent'] ?? ''), 255) ?: null,
            'device'      => $this->normalizeDevice((string) ($payload['device'] ?? $this->detectDevice((string) ($payload['user_agent'] ?? '')))),
            'meta_json'   => $meta !== [] ? json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        try {
            return (bool) $this->model->insert($row, false);
        } catch (\Throwable $e) {
            log_message('debug', 'Analytics track failed: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * @param list<array<string, mixed>> $events
     */
    public function trackBatch(array $events): int
    {
        $ok = 0;
        foreach ($events as $event) {
            if (! is_array($event)) {
                continue;
            }
            if ($this->track($event)) {
                $ok++;
            }
        }

        return $ok;
    }

    public function shouldIgnorePath(string $path): bool
    {
        $path = strtolower($this->normalizePath($path));
        $blocked = [
            '/analytics/collect',
            '/materials/',
            '/student/lessons/download/',
            '/certificados/download/',
            '/assets/',
            '/favicon',
            '/writable/',
            '/debugbar',
            '/.well-known',
            '/mpesa/callback',
        ];

        foreach ($blocked as $prefix) {
            if ($path === rtrim($prefix, '/') || str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public function labelForPath(string $path): string
    {
        $path = $this->normalizePath($path);
        $map = [
            '/' => 'Home pública',
            '/login' => 'Login',
            '/admin/dashboard' => 'Admin · Início',
            '/admin/dashboard/cursos' => 'Admin · Cursos',
            '/admin/dashboard/estudantes' => 'Admin · Estudantes',
            '/admin/dashboard/matriculas' => 'Admin · Matrículas',
            '/admin/dashboard/instrutores' => 'Admin · Instrutores',
            '/admin/dashboard/financas' => 'Admin · Finanças',
            '/admin/dashboard/analytics' => 'Admin · Analytics',
            '/admin/dashboard/notificacoes' => 'Admin · Notificações',
            '/instructor/dashboard' => 'Instrutor · Início',
            '/instructor/dashboard/meus_cursos' => 'Instrutor · Cursos',
            '/instructor/dashboard/meus_estudantes' => 'Instrutor · Estudantes',
            '/instructor/dashboard/financas' => 'Instrutor · Finanças',
            '/student/dashboard' => 'Aluno · Início',
            '/student/dashboard/inscricoes' => 'Aluno · Inscrições',
            '/student/dashboard/cursos' => 'Aluno · Cursos',
            '/checkout' => 'Checkout',
        ];

        if (isset($map[$path])) {
            return $map[$path];
        }

        if (preg_match('#^/courses/\d+#', $path)) {
            return 'Página do curso';
        }
        if (preg_match('#^/student/dashboard/ver_aulas/\d+#', $path)) {
            return 'Aluno · Aulas';
        }
        if (preg_match('#^/checkout/\d+#', $path)) {
            return 'Checkout';
        }
        if (str_starts_with($path, '/admin/')) {
            return 'Admin · ' . $path;
        }
        if (str_starts_with($path, '/instructor/')) {
            return 'Instrutor · ' . $path;
        }
        if (str_starts_with($path, '/student/')) {
            return 'Aluno · ' . $path;
        }

        return $path;
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboard(int $days = 30): array
    {
        $days = max(1, min(90, $days));
        $since = date('Y-m-d 00:00:00', strtotime('-' . ($days - 1) . ' days'));
        $db = db_connect();

        if (! $db->tableExists('analytics_events')) {
            return $this->emptyDashboard($days);
        }

        $base = static fn () => $db->table('analytics_events')->where('created_at >=', $since);

        $pageviews = (int) $base()->where('event_type', 'pageview')->countAllResults();
        $clicks = (int) $base()->where('event_type', 'click')->countAllResults();
        $uniqueVisitors = (int) ($db->query(
            'SELECT COUNT(DISTINCT COALESCE(NULLIF(visitor_id, ""), NULLIF(session_id, ""), CONCAT("u", COALESCE(user_id, 0), "-", ip_address))) AS c
             FROM analytics_events WHERE created_at >= ?',
            [$since]
        )->getRow()->c ?? 0);
        $uniqueSessions = (int) ($db->query(
            'SELECT COUNT(DISTINCT COALESCE(NULLIF(session_id, ""), visitor_id)) AS c
             FROM analytics_events WHERE created_at >= ? AND event_type = "pageview"',
            [$since]
        )->getRow()->c ?? 0);

        $byPersona = $db->query(
            'SELECT persona, COUNT(*) AS total
             FROM analytics_events
             WHERE created_at >= ?
             GROUP BY persona
             ORDER BY total DESC',
            [$since]
        )->getResultArray();

        $byDevice = $db->query(
            'SELECT COALESCE(NULLIF(device, ""), "unknown") AS device, COUNT(*) AS total
             FROM analytics_events
             WHERE created_at >= ? AND event_type = "pageview"
             GROUP BY device
             ORDER BY total DESC',
            [$since]
        )->getResultArray();

        $daily = $db->query(
            'SELECT DATE(created_at) AS day,
                    SUM(CASE WHEN event_type = "pageview" THEN 1 ELSE 0 END) AS pageviews,
                    SUM(CASE WHEN event_type = "click" THEN 1 ELSE 0 END) AS clicks
             FROM analytics_events
             WHERE created_at >= ?
             GROUP BY DATE(created_at)
             ORDER BY day ASC',
            [$since]
        )->getResultArray();

        $dailyMap = [];
        foreach ($daily as $row) {
            $dailyMap[(string) $row['day']] = [
                'pageviews' => (int) $row['pageviews'],
                'clicks'    => (int) $row['clicks'],
            ];
        }

        $labels = [];
        $pageviewSeries = [];
        $clickSeries = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime('-' . $i . ' days'));
            $labels[] = date('d/m', strtotime($day));
            $pageviewSeries[] = (int) ($dailyMap[$day]['pageviews'] ?? 0);
            $clickSeries[] = (int) ($dailyMap[$day]['clicks'] ?? 0);
        }

        return [
            'days' => $days,
            'since' => $since,
            'kpis' => [
                'pageviews' => $pageviews,
                'clicks' => $clicks,
                'visitors' => $uniqueVisitors,
                'sessions' => $uniqueSessions,
                'avg_clicks_per_session' => $uniqueSessions > 0 ? round($clicks / $uniqueSessions, 2) : 0,
            ],
            'chart' => [
                'labels' => $labels,
                'pageviews' => $pageviewSeries,
                'clicks' => $clickSeries,
            ],
            'by_persona' => $byPersona,
            'by_device' => $byDevice,
        ];
    }

    /**
     * Listas paginadas do dashboard de analytics.
     *
     * @return array{items: list<array<string, mixed>>, pagination: array{page: int, per_page: int, total: int, total_pages: int}}
     */
    public function paginateList(string $section, int $days = 30, int $page = 1, int $perPage = 5): array
    {
        $days = max(1, min(90, $days));
        $page = max(1, $page);
        $perPage = max(5, min(50, $perPage));
        $offset = ($page - 1) * $perPage;
        $since = date('Y-m-d 00:00:00', strtotime('-' . ($days - 1) . ' days'));
        $db = db_connect();

        $empty = static function (int $page, int $perPage): array {
            return [
                'items' => [],
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => 0,
                    'total_pages' => 1,
                ],
            ];
        };

        if (! $db->tableExists('analytics_events')) {
            return $empty($page, $perPage);
        }

        $section = strtolower(trim($section));
        $total = 0;
        $items = [];

        if ($section === 'routes') {
            $total = (int) ($db->query(
                'SELECT COUNT(*) AS c FROM (
                    SELECT path, COALESCE(NULLIF(route_label, ""), path) AS label
                    FROM analytics_events
                    WHERE created_at >= ? AND event_type = "pageview"
                    GROUP BY path, label
                 ) AS t',
                [$since]
            )->getRow()->c ?? 0);
            $items = $db->query(
                'SELECT path, COALESCE(NULLIF(route_label, ""), path) AS label, COUNT(*) AS total
                 FROM analytics_events
                 WHERE created_at >= ? AND event_type = "pageview"
                 GROUP BY path, label
                 ORDER BY total DESC
                 LIMIT ' . $perPage . ' OFFSET ' . $offset,
                [$since]
            )->getResultArray();
        } elseif ($section === 'clicks') {
            $total = (int) ($db->query(
                'SELECT COUNT(*) AS c FROM (
                    SELECT COALESCE(NULLIF(element, ""), "(sem rótulo)") AS label, path
                    FROM analytics_events
                    WHERE created_at >= ? AND event_type = "click"
                    GROUP BY label, path
                 ) AS t',
                [$since]
            )->getRow()->c ?? 0);
            $items = $db->query(
                'SELECT COALESCE(NULLIF(element, ""), "(sem rótulo)") AS label, path, COUNT(*) AS total
                 FROM analytics_events
                 WHERE created_at >= ? AND event_type = "click"
                 GROUP BY label, path
                 ORDER BY total DESC
                 LIMIT ' . $perPage . ' OFFSET ' . $offset,
                [$since]
            )->getResultArray();
        } elseif ($section === 'entries') {
            $total = (int) ($db->query(
                'SELECT COUNT(*) AS c FROM (
                    SELECT path, COALESCE(NULLIF(route_label, ""), path) AS label
                    FROM analytics_events
                    WHERE created_at >= ? AND event_type = "pageview"
                      AND (referrer IS NULL OR referrer = "" OR referrer NOT LIKE CONCAT("%", path, "%"))
                    GROUP BY path, label
                 ) AS t',
                [$since]
            )->getRow()->c ?? 0);
            $items = $db->query(
                'SELECT path, COALESCE(NULLIF(route_label, ""), path) AS label, COUNT(*) AS total
                 FROM analytics_events
                 WHERE created_at >= ? AND event_type = "pageview"
                   AND (referrer IS NULL OR referrer = "" OR referrer NOT LIKE CONCAT("%", path, "%"))
                 GROUP BY path, label
                 ORDER BY total DESC
                 LIMIT ' . $perPage . ' OFFSET ' . $offset,
                [$since]
            )->getResultArray();
        } elseif ($section === 'recent') {
            $total = (int) $db->table('analytics_events')
                ->where('created_at >=', $since)
                ->countAllResults();
            $items = $db->table('analytics_events')
                ->where('created_at >=', $since)
                ->orderBy('created_at', 'DESC')
                ->orderBy('id_analytics', 'DESC')
                ->limit($perPage, $offset)
                ->get()
                ->getResultArray();
        } else {
            return $empty($page, $perPage);
        }

        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        return [
            'items' => $items,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyDashboard(int $days): array
    {
        $labels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $labels[] = date('d/m', strtotime('-' . $i . ' days'));
        }

        return [
            'days' => $days,
            'since' => date('Y-m-d 00:00:00', strtotime('-' . ($days - 1) . ' days')),
            'kpis' => [
                'pageviews' => 0,
                'clicks' => 0,
                'visitors' => 0,
                'sessions' => 0,
                'avg_clicks_per_session' => 0,
            ],
            'chart' => [
                'labels' => $labels,
                'pageviews' => array_fill(0, $days, 0),
                'clicks' => array_fill(0, $days, 0),
            ],
            'by_persona' => [],
            'by_device' => [],
        ];
    }

    public function normalizePath(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '/';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $path = is_string($parsed) ? $parsed : '/';
        }

        $path = '/' . ltrim($path, '/');
        $path = preg_replace('#/+#', '/', $path) ?: '/';
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        // strip query
        $qPos = strpos($path, '?');
        if ($qPos !== false) {
            $path = substr($path, 0, $qPos);
        }

        return $path === '' ? '/' : $path;
    }

    public function normalizeEventType(string $type): string
    {
        $type = strtolower(trim($type));
        $allowed = ['pageview', 'click', 'navigation', 'cta', 'search'];

        return in_array($type, $allowed, true) ? $type : '';
    }

    public function normalizePersona(string $persona): string
    {
        $persona = strtolower(trim($persona));
        $allowed = ['guest', 'student', 'instructor', 'admin'];

        return in_array($persona, $allowed, true) ? $persona : 'guest';
    }

    public function normalizeDevice(string $device): string
    {
        $device = strtolower(trim($device));
        if (in_array($device, ['desktop', 'mobile', 'tablet'], true)) {
            return $device;
        }

        return 'unknown';
    }

    public function detectDevice(string $ua): string
    {
        $ua = strtolower($ua);
        if ($ua === '') {
            return 'unknown';
        }
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'tablet';
        }
        if (str_contains($ua, 'mobi') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
            return 'mobile';
        }

        return 'desktop';
    }

    public function resolvePersonaFromAuth(): string
    {
        try {
            $user = service('auth')->user();
            if (! $user) {
                return 'guest';
            }
            $role = strtolower(trim((string) ($user->role ?? '')));

            return $this->normalizePersona($role !== '' ? $role : 'guest');
        } catch (\Throwable $e) {
            return 'guest';
        }
    }

    private function truncate(string $value, int $max): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? $value);

        return mb_substr($value, 0, $max);
    }
}
