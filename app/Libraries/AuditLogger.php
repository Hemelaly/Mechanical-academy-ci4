<?php

namespace App\Libraries;

use App\Models\AuditLogModel;
use Throwable;

class AuditLogger
{
    private AuditLogModel $model;

    public function __construct(?AuditLogModel $model = null)
    {
        $this->model = $model ?? new AuditLogModel();
    }

    public function write(string $event, string $level = 'info', ?string $message = null, array $context = []): void
    {
        $payload = $this->buildPayload($event, $level, $message, $context);

        try {
            $this->model->insert($payload, false);
        } catch (Throwable $exception) {
            log_message('error', '[audit_logger] DB insert failed for event={event}: {error}', [
                'event' => $event,
                'error' => $exception->getMessage(),
            ]);
        }

        log_message($payload['level_audit_log'], '[audit:{event}] {message} {context}', [
            'event' => $payload['event_audit_log'],
            'message' => $payload['message_audit_log'] ?? '',
            'context' => $payload['context_audit_log'] ?? '{}',
        ]);
    }

    private function buildPayload(string $event, string $level, ?string $message, array $context): array
    {
        $request = service('request');
        $auth = service('auth');
        $user = null;

        try {
            $user = $auth ? $auth->user() : null;
        } catch (Throwable $exception) {
            $user = null;
        }

        $method = null;
        $uri = null;
        $ip = null;
        $agent = null;

        if (is_object($request) && method_exists($request, 'getMethod')) {
            $method = strtoupper((string) $request->getMethod());
        }

        if (is_object($request) && method_exists($request, 'getPath')) {
            $uri = (string) $request->getPath();
        }

        if (is_object($request) && method_exists($request, 'getIPAddress')) {
            $ip = (string) $request->getIPAddress();
        }

        if (is_object($request) && method_exists($request, 'getUserAgent')) {
            $userAgent = $request->getUserAgent();
            if ($userAgent && method_exists($userAgent, 'getAgentString')) {
                $agent = (string) $userAgent->getAgentString();
            }
        }

        return [
            'event_audit_log'      => $event,
            'level_audit_log'      => $this->normalizeLevel($level),
            'message_audit_log'    => $this->truncate($message, 255),
            'actor_user_id'        => isset($user->id) ? (int) $user->id : null,
            'method_audit_log'     => $this->truncate($method, 10),
            'uri_audit_log'        => $this->truncate($uri, 255),
            'ip_address_audit_log' => $this->truncate($ip, 45),
            'user_agent_audit_log' => $this->truncate($agent, 255),
            'context_audit_log'    => $this->encodeContext($context),
            'created_at'           => date('Y-m-d H:i:s'),
        ];
    }

    private function normalizeLevel(string $level): string
    {
        $allowed = [
            'emergency',
            'alert',
            'critical',
            'error',
            'warning',
            'notice',
            'info',
            'debug',
        ];

        $normalized = strtolower(trim($level));

        return in_array($normalized, $allowed, true) ? $normalized : 'info';
    }

    private function encodeContext(array $context): string
    {
        $sanitized = $this->sanitizeValue($context);
        $json = json_encode($sanitized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            return '{}';
        }

        return $this->truncate($json, 65000) ?? '{}';
    }

    private function sanitizeValue($value)
    {
        if (is_null($value) || is_scalar($value)) {
            return $value;
        }

        if (is_array($value)) {
            $output = [];
            foreach ($value as $key => $item) {
                $output[$key] = $this->sanitizeValue($item);
            }

            return $output;
        }

        if ($value instanceof Throwable) {
            return [
                'type' => get_class($value),
                'message' => $value->getMessage(),
                'code' => $value->getCode(),
            ];
        }

        if (is_object($value)) {
            return method_exists($value, '__toString')
                ? (string) $value
                : '[object:' . get_class($value) . ']';
        }

        return '[' . gettype($value) . ']';
    }

    private function truncate(?string $value, int $limit): ?string
    {
        if ($value === null) {
            return null;
        }

        if (strlen($value) <= $limit) {
            return $value;
        }

        return substr($value, 0, $limit);
    }
}
