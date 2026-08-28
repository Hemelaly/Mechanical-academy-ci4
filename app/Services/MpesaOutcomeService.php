<?php

namespace App\Services;

/**
 * Interpreta respostas do gateway M-Pesa e produz campos persistíveis
 * + etiquetas legíveis para o painel financeiro.
 */
class MpesaOutcomeService
{
    public const REASON_INSUFFICIENT_BALANCE = 'insufficient_balance';
    public const REASON_INVALID_MSISDN       = 'invalid_msisdn';
    public const REASON_WRONG_PIN           = 'wrong_pin';
    public const REASON_CANCELLED           = 'cancelled';
    public const REASON_DECLINED            = 'declined';
    public const REASON_TIMEOUT             = 'timeout';
    public const REASON_GATEWAY_ERROR       = 'gateway_error';
    public const REASON_REJECTED            = 'rejected';
    public const REASON_EXCEPTION           = 'exception';

    /**
     * @param array<string, mixed> $mpesaData
     * @return array{
     *   gateway_code_payment: ?string,
     *   gateway_message_payment: ?string,
     *   gateway_txn_status_payment: ?string,
     *   failure_reason_payment: string,
     *   status_payment: string,
     *   display_label: string,
     *   display_detail: string
     * }
     */
    public function fromMpesaResponse(array $mpesaData, string $fallbackReason = self::REASON_REJECTED): array
    {
        $code = strtoupper(trim((string) ($mpesaData['output_ResponseCode'] ?? '')));
        $message = trim((string) ($mpesaData['output_ResponseDesc'] ?? ''));
        $txnStatus = trim((string) ($mpesaData['output_ResponseTransactionStatus'] ?? ''));
        $gatewayError = trim((string) ($mpesaData['output_error'] ?? ''));

        if ($gatewayError !== '') {
            $message = $message !== '' ? $message : $gatewayError;
            $reason = self::REASON_GATEWAY_ERROR;
        } else {
            $reason = $this->detectReason($code, $message, $txnStatus, $fallbackReason);
        }

        if ($message === '' || $this->shouldPreferFriendlyMessage($code, $message, $reason)) {
            $message = $this->defaultMessageForReason($reason);
        }

        $message = mb_substr($message, 0, 255);

        return [
            'gateway_code_payment'       => $code !== '' ? mb_substr($code, 0, 32) : null,
            'gateway_message_payment'    => $message,
            'gateway_txn_status_payment' => $txnStatus !== '' ? mb_substr($txnStatus, 0, 64) : null,
            'failure_reason_payment'     => $reason,
            'status_payment'             => 'Rejeitado',
            'display_label'              => $this->labelForReason($reason),
            'display_detail'             => $message,
        ];
    }

    /**
     * SWAL amigável a partir da resposta M-Pesa.
     *
     * @param array<string, mixed> $mpesaData
     * @return array{icon: string, title: string, text: string}
     */
    public function failureSwal(array $mpesaData): array
    {
        $outcome = $this->fromMpesaResponse($mpesaData);
        $reason = $outcome['failure_reason_payment'];

        $icon = match ($reason) {
            self::REASON_INSUFFICIENT_BALANCE, self::REASON_WRONG_PIN, self::REASON_TIMEOUT => 'warning',
            default => 'error',
        };

        return [
            'icon'  => $icon,
            'title' => $outcome['display_label'],
            'text'  => $outcome['display_detail'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function fromException(string $message): array
    {
        $message = trim($message);
        if ($message === '') {
            $message = $this->defaultMessageForReason(self::REASON_EXCEPTION);
        }

        return [
            'gateway_code_payment'       => null,
            'gateway_message_payment'    => mb_substr($message, 0, 255),
            'gateway_txn_status_payment' => null,
            'failure_reason_payment'     => self::REASON_EXCEPTION,
            'status_payment'             => 'Rejeitado',
            'display_label'              => $this->labelForReason(self::REASON_EXCEPTION),
            'display_detail'             => $message,
        ];
    }

    /**
     * Payload para UPDATE de pagamento rejeitado.
     *
     * @param array<string, mixed> $mpesaData
     * @return array<string, mixed>
     */
    public function rejectionUpdate(array $mpesaData, string $fallbackReason = self::REASON_REJECTED): array
    {
        $outcome = $this->fromMpesaResponse($mpesaData, $fallbackReason);

        return [
            'status_payment'             => 'Rejeitado',
            'gateway_code_payment'       => $outcome['gateway_code_payment'],
            'gateway_message_payment'    => $outcome['gateway_message_payment'],
            'gateway_txn_status_payment' => $outcome['gateway_txn_status_payment'],
            'failure_reason_payment'     => $outcome['failure_reason_payment'],
            'id_enrollment_payment'      => 0,
            'approved_by_payment'        => 0,
            'updated_at'                 => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function exceptionUpdate(string $message): array
    {
        $outcome = $this->fromException($message);

        return [
            'status_payment'             => 'Rejeitado',
            'gateway_code_payment'       => null,
            'gateway_message_payment'    => $outcome['gateway_message_payment'],
            'gateway_txn_status_payment' => null,
            'failure_reason_payment'     => self::REASON_EXCEPTION,
            'updated_at'                 => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array<string, ?string>
     */
    public function approvalClearFields(): array
    {
        return [
            'gateway_txn_status_payment' => 'Completed',
            'failure_reason_payment'     => null,
            'gateway_message_payment'    => 'Request processed successfully',
            'gateway_code_payment'       => 'INS-0',
        ];
    }

    public function labelForReason(?string $reason): string
    {
        return match ($reason) {
            self::REASON_INSUFFICIENT_BALANCE => 'Sem saldo',
            self::REASON_INVALID_MSISDN       => 'Número inválido',
            self::REASON_WRONG_PIN           => 'PIN errado',
            self::REASON_CANCELLED           => 'Cancelado',
            self::REASON_DECLINED            => 'Negado',
            self::REASON_TIMEOUT             => 'Tempo esgotado',
            self::REASON_GATEWAY_ERROR       => 'Erro no gateway',
            self::REASON_EXCEPTION           => 'Falha de comunicação',
            self::REASON_REJECTED            => 'Rejeitado',
            default                          => 'Rejeitado',
        };
    }

    /**
     * Etiqueta curta para UI financeira (estado + motivo).
     *
     * @param object|array<string, mixed> $payment
     */
    public function displayStatus($payment): string
    {
        $status = is_array($payment)
            ? (string) ($payment['status_payment'] ?? '')
            : (string) ($payment->status_payment ?? '');

        if ($status === 'Aprovado') {
            return 'Aprovado';
        }

        if ($status === 'Pendente') {
            return 'Pendente';
        }

        if ($status !== 'Rejeitado') {
            return $status !== '' ? $status : '—';
        }

        $reason = is_array($payment)
            ? ($payment['failure_reason_payment'] ?? null)
            : ($payment->failure_reason_payment ?? null);

        $label = $this->labelForReason($reason !== null && $reason !== '' ? (string) $reason : self::REASON_REJECTED);

        if ($label === 'Rejeitado') {
            return 'Rejeitado';
        }

        return 'Rejeitado · ' . $label;
    }

    /**
     * Detalhe técnico/humano (mensagem do M-Pesa).
     *
     * @param object|array<string, mixed> $payment
     */
    public function displayDetail($payment): string
    {
        $reason = is_array($payment)
            ? ($payment['failure_reason_payment'] ?? null)
            : ($payment->failure_reason_payment ?? null);

        $code = is_array($payment)
            ? strtoupper(trim((string) ($payment['gateway_code_payment'] ?? '')))
            : strtoupper(trim((string) ($payment->gateway_code_payment ?? '')));

        $message = is_array($payment)
            ? trim((string) ($payment['gateway_message_payment'] ?? ''))
            : trim((string) ($payment->gateway_message_payment ?? ''));

        if ($reason && $this->shouldPreferFriendlyMessage($code, $message, (string) $reason)) {
            return $this->defaultMessageForReason((string) $reason);
        }

        if ($message !== '') {
            return $message;
        }

        if ($reason) {
            return $this->defaultMessageForReason((string) $reason);
        }

        return '';
    }

    private function detectReason(string $code, string $message, string $txnStatus, string $fallback): string
    {
        $messageLower = strtolower($message);
        $txnLower = strtolower(trim($txnStatus));

        // Códigos oficiais / comunidade MZ
        if ($code === 'INS-2006') {
            return self::REASON_INSUFFICIENT_BALANCE;
        }
        if ($code === 'INS-2051') {
            return self::REASON_INVALID_MSISDN;
        }
        if ($code === 'INS-6') {
            // Documentação local: INS-6 = PIN errado ("Transaction Failed")
            return self::REASON_WRONG_PIN;
        }
        if ($code === 'INS-9') {
            return self::REASON_TIMEOUT;
        }
        if ($code === 'INS-5') {
            return self::REASON_CANCELLED;
        }

        if (
            str_contains($messageLower, 'insufficient')
            || str_contains($messageLower, 'saldo insuficiente')
            || str_contains($messageLower, 'not enough')
        ) {
            return self::REASON_INSUFFICIENT_BALANCE;
        }

        if (
            str_contains($messageLower, 'msisdn invalid')
            || str_contains($messageLower, 'invalid msisdn')
            || str_contains($messageLower, 'invalid number')
        ) {
            return self::REASON_INVALID_MSISDN;
        }

        if (
            str_contains($messageLower, 'wrong pin')
            || str_contains($messageLower, 'incorrect pin')
            || str_contains($messageLower, 'invalid pin')
            || str_contains($messageLower, 'pin errado')
            || str_contains($messageLower, 'pin incorrecto')
            || str_contains($messageLower, 'pin inválido')
            || str_contains($messageLower, 'pin invalido')
        ) {
            return self::REASON_WRONG_PIN;
        }

        if (
            in_array($txnLower, ['timeout', 'timed out'], true)
            || str_contains($messageLower, 'timeout')
            || str_contains($messageLower, 'timed out')
            || str_contains($messageLower, 'request timeout')
        ) {
            return self::REASON_TIMEOUT;
        }

        if (
            in_array($txnLower, ['cancelled', 'canceled'], true)
            || str_contains($messageLower, 'cancel')
        ) {
            return self::REASON_CANCELLED;
        }

        // "Transaction Failed" sem código já tratado → PIN errado (padrão MZ)
        if ($messageLower === 'transaction failed' || str_contains($messageLower, 'transaction failed')) {
            return self::REASON_WRONG_PIN;
        }

        if (in_array($txnLower, ['declined', 'rejected', 'failed'], true) || str_contains($messageLower, 'declin')) {
            return self::REASON_DECLINED;
        }

        if ($code !== '' && $code !== 'INS-0') {
            return self::REASON_REJECTED;
        }

        return $fallback;
    }

    private function shouldPreferFriendlyMessage(string $code, string $message, string $reason): bool
    {
        $messageLower = strtolower(trim($message));

        if ($messageLower === '') {
            return true;
        }

        if (in_array($reason, [
            self::REASON_WRONG_PIN,
            self::REASON_TIMEOUT,
            self::REASON_CANCELLED,
            self::REASON_INSUFFICIENT_BALANCE,
        ], true)) {
            return in_array($messageLower, [
                'transaction failed',
                'request timeout',
                'request timed out',
                'transaction cancelled by customer',
                'insufficient balance',
            ], true)
                || in_array($code, ['INS-6', 'INS-9', 'INS-5', 'INS-2006'], true);
        }

        return false;
    }

    private function defaultMessageForReason(string $reason): string
    {
        return match ($reason) {
            self::REASON_INSUFFICIENT_BALANCE => 'Pagamento negado por saldo insuficiente na conta M-Pesa.',
            self::REASON_INVALID_MSISDN       => 'Número M-Pesa inválido.',
            self::REASON_WRONG_PIN           => 'PIN errado. O pagamento foi rejeitado pelo M-Pesa.',
            self::REASON_CANCELLED           => 'Pagamento cancelado pelo utilizador.',
            self::REASON_DECLINED            => 'Pagamento negado pelo M-Pesa.',
            self::REASON_TIMEOUT             => 'Tempo esgotado: o PIN não foi confirmado a tempo.',
            self::REASON_GATEWAY_ERROR       => 'Erro técnico no gateway M-Pesa.',
            self::REASON_EXCEPTION           => 'Falha de comunicação com o M-Pesa.',
            default                          => 'Pagamento rejeitado pelo M-Pesa.',
        };
    }
}
