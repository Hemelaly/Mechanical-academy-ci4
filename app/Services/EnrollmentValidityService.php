<?php

namespace App\Services;

/**
 * Validade de matrículas pagas: 1 ano a partir da ativação.
 */
class EnrollmentValidityService
{
    public const DURATION = '+1 year';

    public function expiresAt(?string $from = null): string
    {
        $base = $from ?: 'now';

        return date('Y-m-d H:i:s', strtotime(self::DURATION, strtotime($base) ?: time()));
    }

    /**
     * Campos a gravar ao ativar/criar uma matrícula paga.
     *
     * @return array{expires_at_enrollment: string}
     */
    public function paidAccessPayload(?string $from = null): array
    {
        return [
            'expires_at_enrollment' => $this->expiresAt($from),
        ];
    }

    public function isExpired(?object $enrollment): bool
    {
        if (! $enrollment) {
            return true;
        }

        $status = strtolower(trim((string) ($enrollment->status_enrollment ?? '')));
        if ($status === 'cancelada') {
            return true;
        }

        // Demo / preview pendente usam outros relógios.
        if ((int) ($enrollment->is_demo_enrollment ?? 0) === 1) {
            return false;
        }

        if ($status !== 'ativa') {
            return false;
        }

        $expiresAt = trim((string) ($enrollment->expires_at_enrollment ?? ''));
        if ($expiresAt === '' || $expiresAt === '0000-00-00 00:00:00') {
            // Sem data: calcular a partir da inscrição (legado).
            $start = trim((string) ($enrollment->enrolled_at_enrollment ?? ''));
            if ($start === '') {
                return false;
            }
            $expiresAt = $this->expiresAt($start);
        }

        return strtotime($expiresAt) !== false && strtotime($expiresAt) < time();
    }

    public function isActiveAccess(?object $enrollment): bool
    {
        if (! $enrollment) {
            return false;
        }

        $status = strtolower(trim((string) ($enrollment->status_enrollment ?? '')));
        if ($status !== 'ativa') {
            return false;
        }

        if ((int) ($enrollment->is_demo_enrollment ?? 0) === 1) {
            return false;
        }

        return ! $this->isExpired($enrollment);
    }

    public function remainingDays(?object $enrollment): ?int
    {
        $expiresAt = trim((string) ($enrollment->expires_at_enrollment ?? ''));
        if ($expiresAt === '') {
            return null;
        }

        $ts = strtotime($expiresAt);
        if ($ts === false) {
            return null;
        }

        return (int) ceil(($ts - time()) / 86400);
    }
}
