<?php

namespace App\Services;

use App\Models\ExtendedUserModel;

/**
 * Uma sessão/dispositivo activo por conta.
 * O 2.º dispositivo só entra depois do logout do 1.º (ou se a sessão expirar).
 */
class SingleDeviceSessionService
{
    public const SESSION_KEY = 'active_device_token';

    /**
     * Segundos sem actividade após os quais se considera a sessão abandonada.
     */
    public function lockTtlSeconds(): int
    {
        $expiration = (int) (config('Session')->expiration ?? 7200);

        return max(300, $expiration);
    }

    public function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function claimOrReject(int $userId): array
    {
        if ($userId <= 0) {
            return ['ok' => false, 'message' => 'Utilizador inválido.'];
        }

        if (! $this->hasColumns()) {
            // Migração ainda não aplicada — não bloquear o site.
            return ['ok' => true, 'token' => $this->generateToken()];
        }

        $users = new ExtendedUserModel();
        $row   = $users->select('id, active_device_token, active_device_at')->find($userId);

        if (! $row) {
            return ['ok' => false, 'message' => 'Utilizador não encontrado.'];
        }

        $storedToken = trim((string) ($row->active_device_token ?? ''));
        $storedAt    = trim((string) ($row->active_device_at ?? ''));
        $current     = trim((string) (session()->get(self::SESSION_KEY) ?? ''));

        if ($storedToken !== '' && $this->isLockActive($storedAt)) {
            // Mesmo browser a renovar / re-login na mesma sessão.
            if ($current !== '' && hash_equals($storedToken, $current)) {
                $this->touch($userId, $storedToken);

                return ['ok' => true, 'token' => $storedToken];
            }

            return [
                'ok'      => false,
                'message' => 'Esta conta já está ligada noutro dispositivo. Faça logout nesse dispositivo antes de entrar aqui.',
            ];
        }

        $token = $this->generateToken();
        $this->bind($userId, $token);

        return ['ok' => true, 'token' => $token];
    }

    public function bind(int $userId, string $token): void
    {
        if (! $this->hasColumns() || $userId <= 0 || $token === '') {
            return;
        }

        (new ExtendedUserModel())->update($userId, [
            'active_device_token' => $token,
            'active_device_at'    => date('Y-m-d H:i:s'),
        ]);

        session()->set(self::SESSION_KEY, $token);
    }

    public function touch(int $userId, ?string $token = null): void
    {
        if (! $this->hasColumns() || $userId <= 0) {
            return;
        }

        $token ??= trim((string) (session()->get(self::SESSION_KEY) ?? ''));
        if ($token === '') {
            return;
        }

        (new ExtendedUserModel())->update($userId, [
            'active_device_token' => $token,
            'active_device_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    public function release(int $userId): void
    {
        if (! $this->hasColumns() || $userId <= 0) {
            return;
        }

        $current = trim((string) (session()->get(self::SESSION_KEY) ?? ''));
        $users   = new ExtendedUserModel();
        $row     = $users->select('id, active_device_token')->find($userId);

        if ($row) {
            $stored = trim((string) ($row->active_device_token ?? ''));
            // Só limpa se for o dispositivo que tem o token (evita o 2.º dispositivo limpar o 1.º).
            if ($current === '' || $stored === '' || hash_equals($stored, $current)) {
                $users->update($userId, [
                    'active_device_token' => null,
                    'active_device_at'    => null,
                ]);
            }
        }

        session()->remove(self::SESSION_KEY);
    }

    /**
     * true = sessão válida neste dispositivo; false = desalojado / outro dispositivo.
     */
    public function assertCurrentDevice(int $userId): bool
    {
        if (! $this->hasColumns() || $userId <= 0) {
            return true;
        }

        $users = new ExtendedUserModel();
        $row   = $users->select('id, active_device_token, active_device_at')->find($userId);
        if (! $row) {
            return false;
        }

        $stored = trim((string) ($row->active_device_token ?? ''));
        $at     = trim((string) ($row->active_device_at ?? ''));
        $local  = trim((string) (session()->get(self::SESSION_KEY) ?? ''));

        if ($stored === '') {
            // Conta sem lock (legado / após expiração) — reivindica este dispositivo.
            $token = $local !== '' ? $local : $this->generateToken();
            $this->bind($userId, $token);

            return true;
        }

        if ($local === '' || ! hash_equals($stored, $local)) {
            return false;
        }

        if (! $this->isLockActive($at)) {
            // Lock expirou: renovar neste dispositivo.
            $this->touch($userId, $stored);

            return true;
        }

        // Mantém o heartbeat do dispositivo activo.
        $this->touch($userId, $stored);

        return true;
    }

    private function isLockActive(string $activeAt): bool
    {
        if ($activeAt === '' || $activeAt === '0000-00-00 00:00:00') {
            return false;
        }

        $ts = strtotime($activeAt);
        if ($ts === false) {
            return false;
        }

        return (time() - $ts) < $this->lockTtlSeconds();
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
