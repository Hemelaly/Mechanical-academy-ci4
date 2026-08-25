<?php

namespace App\Services;

/**
 * Um dispositivo "primário" por conta para assistir vídeos.
 *
 * - Login/logout no mesmo dispositivo: sempre permitido (cookie persistente).
 * - 2.º/3.º dispositivo: podem entrar na conta, mas não assistem vídeos
 *   até o dispositivo primário fazer logout (ou a sessão expirar).
 */
class SingleDeviceSessionService
{
    public const SESSION_KEY = 'active_device_token';

    public const DEVICE_COOKIE = 'ma_watch_device';

    public function lockTtlSeconds(): int
    {
        $expiration = (int) (config('Session')->expiration ?? 7200);

        return max(300, $expiration);
    }

    public function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Lê a identidade local sem criar uma nova (não mintar).
     */
    public function peekDeviceId(): string
    {
        $fromCookie = trim((string) (service('request')->getCookie(self::DEVICE_COOKIE) ?? ''));
        if ($fromCookie !== '' && preg_match('/^[a-f0-9]{64}$/', $fromCookie)) {
            return $fromCookie;
        }

        $fromSession = trim((string) (session()->get(self::SESSION_KEY) ?? ''));
        if ($fromSession !== '' && preg_match('/^[a-f0-9]{64}$/', $fromSession)) {
            return $fromSession;
        }

        return '';
    }

    /**
     * Identidade estável deste browser (sobrevive a logout/login).
     */
    public function deviceId(): string
    {
        $existing = $this->peekDeviceId();
        if ($existing !== '') {
            if (session()->get(self::SESSION_KEY) !== $existing) {
                session()->set(self::SESSION_KEY, $existing);
            }
            // Garante cookie persistente se só existia na sessão.
            $fromCookie = trim((string) (service('request')->getCookie(self::DEVICE_COOKIE) ?? ''));
            if ($fromCookie !== $existing) {
                $this->writeDeviceCookie($existing);
            }

            return $existing;
        }

        $token = $this->generateToken();
        session()->set(self::SESSION_KEY, $token);
        $this->writeDeviceCookie($token);

        return $token;
    }

    /**
     * Regista este browser. Nunca bloqueia o login.
     * Torna-se primário se não houver outro activo, ou se já era o primário.
     */
    public function registerOnLogin(int $userId): string
    {
        $token = $this->deviceId();

        if ($userId <= 0 || ! $this->hasColumns()) {
            return $token;
        }

        $row = $this->readUser($userId);
        $stored = trim((string) ($row['active_device_token'] ?? ''));
        $storedAt = trim((string) ($row['active_device_at'] ?? ''));

        if ($stored === '' || ! $this->isLockActive($storedAt) || hash_equals($stored, $token)) {
            $this->setPrimary($userId, $token);
        } else {
            // Mantém identidade local estável mesmo sem ser o primário.
            session()->set(self::SESSION_KEY, $token);
            $this->writeDeviceCookie($token);
        }

        return $token;
    }

    /**
     * Pode este dispositivo assistir vídeos?
     * Se não houver primário activo, este dispositivo passa a ser o primário.
     */
    public function canWatchVideos(int $userId): bool
    {
        if ($userId <= 0 || ! $this->hasColumns()) {
            return true;
        }

        $local = $this->deviceId();

        $row = $this->readUser($userId);
        $stored = trim((string) ($row['active_device_token'] ?? ''));
        $storedAt = trim((string) ($row['active_device_at'] ?? ''));

        if ($stored === '' || ! $this->isLockActive($storedAt)) {
            $this->setPrimary($userId, $local);

            return true;
        }

        if (hash_equals($stored, $local)) {
            $this->touchPrimary($userId);

            return true;
        }

        return false;
    }

    /**
     * Logout: se este dispositivo era o primário (ou não há identidade local),
     * limpa o lock na BD. Nunca cria um token novo só para comparar.
     */
    public function release(int $userId): void
    {
        $local = $this->peekDeviceId();

        if ($userId > 0 && $this->hasColumns()) {
            $row = $this->readUser($userId);
            $stored = trim((string) ($row['active_device_token'] ?? ''));

            if ($stored === '') {
                // já livre
            } elseif ($local !== '' && hash_equals($stored, $local)) {
                $this->clearPrimary($userId);
            } elseif ($local === '') {
                // Sem cookie/sessão: logout explícito deve libertar o lock
                // para o mesmo browser poder voltar a assistir após login.
                $this->clearPrimary($userId);
            }
        }

        session()->remove(self::SESSION_KEY);
    }

    /**
     * Força limpeza completa do lock desta conta.
     */
    public function forceRelease(int $userId): void
    {
        if ($userId > 0 && $this->hasColumns()) {
            $this->clearPrimary($userId);
        }

        session()->remove(self::SESSION_KEY);
    }

    public function touchIfPrimary(int $userId): void
    {
        if ($userId <= 0 || ! $this->hasColumns()) {
            return;
        }

        $local = $this->peekDeviceId();
        if ($local === '') {
            return;
        }

        session()->set(self::SESSION_KEY, $local);

        $row = $this->readUser($userId);
        $stored = trim((string) ($row['active_device_token'] ?? ''));
        if ($stored !== '' && hash_equals($stored, $local)) {
            $this->touchPrimary($userId);
        }
    }

    private function writeDeviceCookie(string $token): void
    {
        service('response')->setCookie([
            'name'     => self::DEVICE_COOKIE,
            'value'    => $token,
            'expire'   => 365 * 24 * 60 * 60,
            'httponly' => true,
            'secure'   => (bool) (config('Cookie')->secure ?? false),
            'samesite' => (string) (config('Cookie')->samesite ?? 'Lax'),
            'path'     => (string) (config('Cookie')->path ?? '/'),
        ]);
    }

    private function setPrimary(int $userId, string $token): void
    {
        db_connect()->table('users')->where('id', $userId)->update([
            'active_device_token' => $token,
            'active_device_at'    => date('Y-m-d H:i:s'),
        ]);
        session()->set(self::SESSION_KEY, $token);
        $this->writeDeviceCookie($token);
    }

    private function touchPrimary(int $userId): void
    {
        db_connect()->table('users')->where('id', $userId)->update([
            'active_device_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function clearPrimary(int $userId): void
    {
        db_connect()->query(
            'UPDATE users SET active_device_token = NULL, active_device_at = NULL WHERE id = ?',
            [$userId]
        );
    }

    /**
     * @return array{active_device_token?: string|null, active_device_at?: string|null}
     */
    private function readUser(int $userId): array
    {
        $row = db_connect()
            ->table('users')
            ->select('active_device_token, active_device_at')
            ->where('id', $userId)
            ->get()
            ->getRowArray();

        return is_array($row) ? $row : [];
    }

    private function isLockActive(string $activeAt): bool
    {
        if ($activeAt === '' || str_starts_with($activeAt, '0000-00-00')) {
            return false;
        }

        $ts = strtotime($activeAt);

        return $ts !== false && (time() - $ts) < $this->lockTtlSeconds();
    }

    private function hasColumns(): bool
    {
        static $ok = null;
        if ($ok !== null) {
            return $ok;
        }

        try {
            $ok = db_connect()->fieldExists('active_device_token', 'users');
        } catch (\Throwable $e) {
            $ok = false;
        }

        return $ok;
    }
}
