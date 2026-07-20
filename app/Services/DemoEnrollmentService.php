<?php

namespace App\Services;

use App\Models\EnrollmentModel;

/**
 * Acesso demo (não pago) com validade de 2 horas a partir do primeiro acesso.
 */
class DemoEnrollmentService
{
    public const DEFAULT_HOURS = 2;

    public function __construct(
        private ?EnrollmentModel $enrollments = null
    ) {
        $this->enrollments ??= new EnrollmentModel();
    }

    public function isDemo($enrollment): bool
    {
        if (! $enrollment) {
            return false;
        }
        return (int) ($enrollment->is_demo_enrollment ?? 0) === 1;
    }

    public function isExpired($enrollment): bool
    {
        if (! $this->isDemo($enrollment)) {
            return false;
        }

        $expiresAt = $enrollment->demo_expires_at ?? null;
        if ($expiresAt === null || $expiresAt === '') {
            return false; // ainda não iniciou o relógio
        }

        return strtotime((string) $expiresAt) <= time();
    }

    public function isActive($enrollment): bool
    {
        return $this->isDemo($enrollment) && ! $this->isExpired($enrollment);
    }

    /**
     * Inicia o relógio no primeiro acesso às aulas.
     * @return object|null enrollment atualizado
     */
    public function startClockIfNeeded($enrollment, int $hours = self::DEFAULT_HOURS)
    {
        if (! $enrollment || ! $this->isDemo($enrollment)) {
            return $enrollment;
        }

        if (! empty($enrollment->demo_started_at) && ! empty($enrollment->demo_expires_at)) {
            return $enrollment;
        }

        $started = date('Y-m-d H:i:s');
        $expires = date('Y-m-d H:i:s', time() + max(1, $hours) * 3600);

        $this->enrollments->update((int) $enrollment->id_enrollment, [
            'demo_started_at' => $started,
            'demo_expires_at' => $expires,
        ]);

        $enrollment->demo_started_at = $started;
        $enrollment->demo_expires_at = $expires;

        return $enrollment;
    }

    public function remainingSeconds($enrollment): int
    {
        if (! $this->isDemo($enrollment) || empty($enrollment->demo_expires_at)) {
            return 0;
        }
        return max(0, strtotime((string) $enrollment->demo_expires_at) - time());
    }

    /**
     * Concede ou reinicia acesso demo (matrícula não paga / pendente).
     * O timer só começa no primeiro acesso às aulas.
     */
    public function grant(int $userId, int $courseId, int $hours = self::DEFAULT_HOURS): array
    {
        $existing = $this->enrollments
            ->where('id_student_enrollment', $userId)
            ->where('id_course_enrollment', $courseId)
            ->first();

        $payload = [
            'status_enrollment'     => 'pendente',
            'is_demo_enrollment'    => 1,
            'is_manual_enrollment'  => 1,
            'demo_started_at'       => null,
            'demo_expires_at'       => null,
            'enrolled_at_enrollment'=> date('Y-m-d'),
        ];

        if ($existing) {
            $status = strtolower((string) ($existing->status_enrollment ?? ''));
            // Não sobrescrever acesso pago completo sem ser demo.
            if ($status === 'ativa' && ! $this->isDemo($existing)) {
                return [
                    'ok' => false,
                    'message' => 'O aluno já tem acesso completo (pago/manual) a este curso.',
                ];
            }

            $this->enrollments->update((int) $existing->id_enrollment, $payload);
            $emailResult = $this->sendDemoCredentialsEmail($userId, $courseId, $hours);

            return [
                'ok' => true,
                'message' => 'Acesso demo concedido. Expira ' . $hours . 'h após o primeiro acesso às aulas.'
                    . ($emailResult['sent'] ? ' Credenciais enviadas por email.' : ' (Email não enviado: ' . ($emailResult['error'] ?? 'falha') . ')'),
                'enrollment_id' => (int) $existing->id_enrollment,
                'email_sent' => $emailResult['sent'],
            ];
        }

        $payload['id_student_enrollment'] = $userId;
        $payload['id_course_enrollment'] = $courseId;
        $payload['progress_enrollment'] = 0;

        $inserted = $this->enrollments->insert($payload, true);
        if ($inserted === false) {
            return [
                'ok' => false,
                'message' => implode(' ', $this->enrollments->errors() ?: ['Falha ao criar acesso demo.']),
            ];
        }

        $emailResult = $this->sendDemoCredentialsEmail($userId, $courseId, $hours);

        return [
            'ok' => true,
            'message' => 'Acesso demo criado. Expira ' . $hours . 'h após o primeiro acesso às aulas.'
                . ($emailResult['sent'] ? ' Credenciais enviadas por email.' : ' (Email não enviado: ' . ($emailResult['error'] ?? 'falha') . ')'),
            'enrollment_id' => (int) $this->enrollments->getInsertID(),
            'email_sent' => $emailResult['sent'],
        ];
    }

    /**
     * Gera nova palavra-passe temporária e envia email com utilizador + password.
     *
     * @return array{sent:bool,error?:string}
     */
    public function sendDemoCredentialsEmail(int $userId, int $courseId, int $hours = self::DEFAULT_HOURS): array
    {
        $db = db_connect();
        $identity = $db->table('auth_identities')
            ->select('secret')
            ->where('user_id', $userId)
            ->where('type', 'email_password')
            ->get()
            ->getRow();

        $email = trim(strtolower((string) ($identity->secret ?? '')));
        if ($email === '') {
            $student = $db->table('students')
                ->select('email_student, name_student')
                ->where('id_user_student', $userId)
                ->get()
                ->getRow();
            $email = trim(strtolower((string) ($student->email_student ?? '')));
        }

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['sent' => false, 'error' => 'email inválido'];
        }

        $userRow = $db->table('users')->select('id, username')->where('id', $userId)->get()->getRow();
        $username = trim((string) ($userRow->username ?? ''));
        if ($username === '') {
            $username = $email;
        }

        $course = $db->table('courses')->select('title_course')->where('id_course', $courseId)->get()->getRow();
        $courseTitle = trim((string) ($course->title_course ?? 'curso'));

        $tempPassword = substr(bin2hex(random_bytes(8)), 0, 10);

        try {
            $users = auth()->getProvider();
            $user = $users->findById($userId);
            if (! $user) {
                return ['sent' => false, 'error' => 'utilizador não encontrado'];
            }
            $user->fill(['password' => $tempPassword]);
            if (! $users->save($user)) {
                return ['sent' => false, 'error' => 'falha ao atualizar senha'];
            }
        } catch (\Throwable $e) {
            log_message('error', 'Demo password reset failed: ' . $e->getMessage());
            return ['sent' => false, 'error' => 'falha ao atualizar senha'];
        }

        $loginUrl = site_url('login');
        $lessonsUrl = site_url('student/dashboard/ver_aulas/' . $courseId);

        try {
            $mail = \Config\Services::email();
            $mail->setTo($email);
            $mail->setSubject('Acesso demo · ' . $courseTitle);
            $mail->setMailType('html');
            $mail->setMessage(
                '<p>Olá' . ($username !== '' ? ' <strong>' . esc($username) . '</strong>' : '') . ',</p>' .
                '<p>Recebeu acesso <strong>demo</strong> ao curso <strong>' . esc($courseTitle) . '</strong>.</p>' .
                '<p>O acesso demo expira <strong>' . (int) $hours . ' horas após o primeiro acesso</strong> às aulas.</p>' .
                '<p><strong>Dados de acesso:</strong></p>' .
                '<ul>' .
                '<li>Utilizador / Email: <strong>' . esc($email) . '</strong></li>' .
                '<li>Palavra-passe temporária: <strong>' . esc($tempPassword) . '</strong></li>' .
                '</ul>' .
                '<p><a href="' . esc($loginUrl) . '">Entrar na plataforma</a></p>' .
                '<p>Depois de entrar, abra o curso: <a href="' . esc($lessonsUrl) . '">' . esc($courseTitle) . '</a></p>' .
                '<p>Recomendamos alterar a palavra-passe após o primeiro login.</p>'
            );

            if (! $mail->send()) {
                log_message('warning', 'Falha ao enviar email demo para ' . $email);
                return ['sent' => false, 'error' => 'falha no envio'];
            }

            return ['sent' => true];
        } catch (\Throwable $e) {
            log_message('warning', 'Exceção email demo: ' . $e->getMessage());
            return ['sent' => false, 'error' => 'exceção no envio'];
        }
    }

    /**
     * Limpa flags demo ao ativar acesso pago/manual completo.
     */
    public function clearDemoFlags(int $enrollmentId): void
    {
        $this->enrollments->update($enrollmentId, [
            'is_demo_enrollment' => 0,
            'demo_started_at'    => null,
            'demo_expires_at'    => null,
        ]);
    }
}
