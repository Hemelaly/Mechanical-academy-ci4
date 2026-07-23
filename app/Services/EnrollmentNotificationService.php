<?php

namespace App\Services;

use App\Models\UserNotificationModel;

class EnrollmentNotificationService
{
    public function notifyInstructorAboutNewEnrollment(int $enrollmentId): bool
    {
        if ($enrollmentId <= 0) {
            return false;
        }

        $db = db_connect();
        $row = $db->table('enrollments e')
            ->select([
                'e.id_enrollment',
                'e.enrolled_at_enrollment',
                'e.created_at AS enrollment_created_at',
                'c.id_instructor_course AS instructor_user_id',
                's.name_student',
                's.email_student',
                'c.title_course',
                'u.username AS instructor_name',
            ])
            ->select("(SELECT ai.secret FROM auth_identities ai WHERE ai.user_id = c.id_instructor_course AND ai.type = 'email_password' ORDER BY ai.id DESC LIMIT 1) AS instructor_login_email", false)
            ->select("(SELECT i.email_instructor FROM instructors i WHERE i.id_instructor = c.id_instructor_course LIMIT 1) AS instructor_profile_email", false)
            ->join('courses c', 'c.id_course = e.id_course_enrollment')
            ->join('students s', 's.id_user_student = e.id_student_enrollment', 'left')
            ->join('users u', 'u.id = c.id_instructor_course', 'left')
            ->where('e.id_enrollment', $enrollmentId)
            ->get()
            ->getRow();

        if (! $row) {
            return false;
        }

        $instructorUserId = (int) ($row->instructor_user_id ?? 0);
        $instructorEmail = $this->resolveInstructorEmail($row);
        $instructorName = trim((string) ($row->instructor_name ?? 'Instrutor'));
        $studentName = trim((string) ($row->name_student ?? 'Aluno'));
        $studentEmail = trim((string) ($row->email_student ?? ''));
        $courseTitle = trim((string) ($row->title_course ?? 'Curso'));
        $enrolledAt = $this->formatDateTime(
            (string) ($row->enrolled_at_enrollment ?: $row->enrollment_created_at)
        );
        $studentsUrl = site_url('instructor/dashboard/meus_estudantes');

        $this->createInAppNotification(
            $instructorUserId,
            'enrollment.new',
            'Nova inscrição no seu curso',
            $studentName . ' inscreveu-se em ' . $courseTitle . '.',
            $studentsUrl,
            [
                'enrollment_id' => $enrollmentId,
                'course'        => $courseTitle,
                'student'       => $studentName,
            ]
        );

        if ($instructorEmail === '') {
            log_message('warning', 'Nao foi possivel notificar o instrutor sobre nova inscricao: email ausente.', [
                'enrollment_id' => $enrollmentId,
                'course'        => $row->title_course ?? null,
            ]);

            return false;
        }

        try {
            $email = \Config\Services::email();
            $email->setTo($instructorEmail);
            $email->setSubject('Nova inscricao no seu curso');
            $email->setMailType('html');
            $email->setMessage(\App\Libraries\BrandEmail::render([
                'preheader' => 'Nova inscrição em ' . $courseTitle . '.',
                'eyebrow'   => 'Nova inscrição',
                'greeting'  => 'Olá ' . \App\Libraries\BrandEmail::strong($instructorName) . ',',
                'title'     => 'Nova inscrição no seu curso',
                'body'      => \App\Libraries\BrandEmail::p(
                    'Uma nova inscrição foi registada na plataforma.'
                ),
                'info' => [
                    ['label' => 'Aluno', 'value' => esc($studentName)],
                    ['label' => 'Email', 'value' => esc($studentEmail !== '' ? $studentEmail : 'Não informado')],
                    ['label' => 'Curso', 'value' => esc($courseTitle)],
                    ['label' => 'Data', 'value' => esc($enrolledAt)],
                ],
                'cta' => [
                    'url'   => $studentsUrl,
                    'label' => 'Ver estudantes',
                ],
            ]));

            if (! $email->send()) {
                log_message('warning', 'Falha ao enviar email de nova inscricao ao instrutor.', [
                    'enrollment_id'    => $enrollmentId,
                    'instructor_email' => $instructorEmail,
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            log_message('warning', 'Excecao ao enviar email de nova inscricao ao instrutor: ' . $e->getMessage(), [
                'enrollment_id'    => $enrollmentId,
                'instructor_email' => $instructorEmail,
            ]);

            return false;
        }
    }

    /**
     * Notifica o instrutor do curso (plataforma + email) sobre um pagamento aprovado.
     */
    public function notifyInstructorAboutNewPayment(int $paymentId): bool
    {
        if ($paymentId <= 0) {
            return false;
        }

        $db = db_connect();
        $row = $db->table('payments p')
            ->select([
                'p.id_payment',
                'p.amount_payment',
                'p.method_payment',
                'p.reference_payment',
                'p.status_payment',
                'p.guest_email_payment',
                'p.guest_name_payment',
                'p.id_user_payment',
                'p.id_enrollment_payment',
                'p.created_at AS payment_created_at',
                'p.updated_at AS payment_updated_at',
                'c.id_course',
                'c.title_course',
                'c.id_instructor_course AS instructor_user_id',
                'u.username AS instructor_name',
                's.name_student',
                's.email_student',
            ])
            ->select("(SELECT ai.secret FROM auth_identities ai WHERE ai.user_id = c.id_instructor_course AND ai.type = 'email_password' ORDER BY ai.id DESC LIMIT 1) AS instructor_login_email", false)
            ->select("(SELECT i.email_instructor FROM instructors i WHERE i.id_instructor = c.id_instructor_course LIMIT 1) AS instructor_profile_email", false)
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->join('users u', 'u.id = c.id_instructor_course', 'left')
            ->join('students s', 's.id_user_student = p.id_user_payment', 'left')
            ->where('p.id_payment', $paymentId)
            ->get()
            ->getRow();

        if (! $row) {
            return false;
        }

        if (strtolower(trim((string) ($row->status_payment ?? ''))) !== 'aprovado') {
            return false;
        }

        $instructorUserId = (int) ($row->instructor_user_id ?? 0);
        $instructorEmail = $this->resolveInstructorEmail($row);
        $instructorName = trim((string) ($row->instructor_name ?? 'Instrutor'));
        $courseTitle = trim((string) ($row->title_course ?? 'Curso'));
        $amount = (float) ($row->amount_payment ?? 0);
        $amountLabel = number_format($amount, 2, ',', '.') . ' MZN';
        $method = trim((string) ($row->method_payment ?? ''));
        if ($method === '') {
            $method = 'Nao informado';
        }
        $reference = trim((string) ($row->reference_payment ?? ''));
        $paidAt = $this->formatDateTime(
            (string) ($row->payment_updated_at ?: $row->payment_created_at)
        );

        $studentName = trim((string) ($row->name_student ?? ''));
        $studentEmail = trim((string) ($row->email_student ?? ''));
        if ($studentName === '') {
            $studentName = trim((string) ($row->guest_name_payment ?? 'Aluno'));
        }
        if ($studentEmail === '') {
            $studentEmail = trim((string) ($row->guest_email_payment ?? ''));
        }

        // Guest checkout guarda pending_users.id em id_user_payment antes da ativacao.
        if ($studentEmail === '' || $studentName === 'Aluno') {
            $pending = $db->table('pending_users')
                ->select('username, email')
                ->where('payment_id', $paymentId)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRow();
            if ($pending) {
                if ($studentName === '' || $studentName === 'Aluno') {
                    $studentName = trim((string) ($pending->username ?? $studentName));
                }
                if ($studentEmail === '') {
                    $studentEmail = trim((string) ($pending->email ?? ''));
                }
            }
        }

        $financeUrl = site_url('instructor/dashboard/financas');
        $title = 'Novo pagamento recebido';
        $body = $studentName . ' pagou ' . $amountLabel . ' pelo curso ' . $courseTitle . '.';

        $this->createInAppNotification(
            $instructorUserId,
            'payment.approved',
            $title,
            $body,
            $financeUrl,
            [
                'payment_id'  => $paymentId,
                'course_id'   => (int) ($row->id_course ?? 0),
                'course'      => $courseTitle,
                'student'     => $studentName,
                'amount'      => $amount,
                'method'      => $method,
                'reference'   => $reference,
            ]
        );

        if ($instructorEmail === '') {
            log_message('warning', 'Nao foi possivel notificar o instrutor sobre novo pagamento: email ausente.', [
                'payment_id' => $paymentId,
                'course'     => $courseTitle,
            ]);

            return false;
        }

        try {
            $email = \Config\Services::email();
            $email->setTo($instructorEmail);
            $email->setSubject('Novo pagamento no seu curso — ' . $courseTitle);
            $email->setMailType('html');
            $email->setMessage(\App\Libraries\BrandEmail::render([
                'preheader' => 'Pagamento de ' . $amountLabel . ' no curso ' . $courseTitle . '.',
                'eyebrow'   => 'Novo pagamento',
                'greeting'  => 'Olá ' . \App\Libraries\BrandEmail::strong($instructorName) . ',',
                'title'     => 'Novo pagamento recebido',
                'body'      => \App\Libraries\BrandEmail::p(
                    'Foi registado um pagamento aprovado no seu curso.'
                ),
                'info' => array_values(array_filter([
                    ['label' => 'Aluno', 'value' => esc($studentName)],
                    ['label' => 'Email', 'value' => esc($studentEmail !== '' ? $studentEmail : 'Não informado')],
                    ['label' => 'Curso', 'value' => esc($courseTitle)],
                    ['label' => 'Valor', 'value' => esc($amountLabel)],
                    ['label' => 'Método', 'value' => esc($method)],
                    $reference !== '' ? ['label' => 'Referência', 'value' => esc($reference)] : null,
                    ['label' => 'Data', 'value' => esc($paidAt)],
                ])),
                'cta' => [
                    'url'   => $financeUrl,
                    'label' => 'Ver finanças',
                ],
            ]));

            if (! $email->send()) {
                log_message('warning', 'Falha ao enviar email de novo pagamento ao instrutor.', [
                    'payment_id'       => $paymentId,
                    'instructor_email' => $instructorEmail,
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            log_message('warning', 'Excecao ao enviar email de novo pagamento ao instrutor: ' . $e->getMessage(), [
                'payment_id'       => $paymentId,
                'instructor_email' => $instructorEmail,
            ]);

            return false;
        }
    }

    /**
     * Notifica o aluno sobre inscrição/matrícula.
     *
     * @param 'self'|'manual' $kind
     */
    public function notifyStudentAboutEnrollment(int $enrollmentId, string $kind = 'self'): bool
    {
        if ($enrollmentId <= 0) {
            return false;
        }

        $kind = $kind === 'manual' ? 'manual' : 'self';

        $db = db_connect();
        $row = $db->table('enrollments e')
            ->select([
                'e.id_enrollment',
                'e.id_student_enrollment AS student_user_id',
                'e.id_course_enrollment AS course_id',
                'e.enrolled_at_enrollment',
                'e.created_at AS enrollment_created_at',
                's.name_student',
                's.email_student',
                'c.title_course',
                'u.username AS student_username',
            ])
            ->select("(SELECT ai.secret FROM auth_identities ai WHERE ai.user_id = e.id_student_enrollment AND ai.type = 'email_password' ORDER BY ai.id DESC LIMIT 1) AS student_login_email", false)
            ->join('courses c', 'c.id_course = e.id_course_enrollment')
            ->join('students s', 's.id_user_student = e.id_student_enrollment', 'left')
            ->join('users u', 'u.id = e.id_student_enrollment', 'left')
            ->where('e.id_enrollment', $enrollmentId)
            ->get()
            ->getRow();

        if (! $row) {
            return false;
        }

        $studentEmail = trim((string) ($row->email_student ?? ''));
        if ($studentEmail === '') {
            $studentEmail = trim((string) ($row->student_login_email ?? ''));
        }

        if ($studentEmail === '') {
            log_message('warning', 'Nao foi possivel notificar o aluno sobre inscricao: email ausente.', [
                'enrollment_id' => $enrollmentId,
                'kind'          => $kind,
            ]);

            return false;
        }

        $studentName = trim((string) ($row->name_student ?? ''));
        if ($studentName === '') {
            $studentName = trim((string) ($row->student_username ?? 'Aluno'));
        }

        $courseTitle = trim((string) ($row->title_course ?? 'Curso'));
        $courseId = (int) ($row->course_id ?? 0);
        $enrolledAt = $this->formatDateTime(
            (string) ($row->enrolled_at_enrollment ?: $row->enrollment_created_at)
        );
        $courseUrl = $courseId > 0
            ? site_url('student/dashboard/ver_aulas/' . $courseId)
            : site_url('student/dashboard/inscricoes');

        if ($kind === 'manual') {
            $subject = 'Foi matriculado no curso ' . $courseTitle;
            $title = 'Matrícula confirmada';
            $eyebrow = 'Matrícula manual';
            $preheader = 'Foi matriculado no curso ' . $courseTitle . '.';
            $intro = \App\Libraries\BrandEmail::p(
                'Foi matriculado no curso ' . \App\Libraries\BrandEmail::strong($courseTitle) . ' na Mechanical Academy.'
            ) . \App\Libraries\BrandEmail::p(
                'Já pode aceder às aulas na plataforma.'
            );
        } else {
            $subject = 'Inscrição confirmada · ' . $courseTitle;
            $title = 'Inscrição confirmada';
            $eyebrow = 'Nova inscrição';
            $preheader = 'Inscreveu-se no curso ' . $courseTitle . '.';
            $intro = \App\Libraries\BrandEmail::p(
                'A sua inscrição no curso ' . \App\Libraries\BrandEmail::strong($courseTitle) . ' foi confirmada.'
            ) . \App\Libraries\BrandEmail::p(
                'Bem-vindo(a)! Pode começar a aprender quando quiser.'
            );
        }

        try {
            $email = \Config\Services::email();
            $email->setTo($studentEmail);
            $email->setSubject($subject);
            $email->setMailType('html');
            $email->setMessage(\App\Libraries\BrandEmail::render([
                'preheader' => $preheader,
                'eyebrow'   => $eyebrow,
                'greeting'  => 'Olá ' . \App\Libraries\BrandEmail::strong($studentName) . ',',
                'title'     => $title,
                'body'      => $intro,
                'info' => [
                    ['label' => 'Curso', 'value' => esc($courseTitle)],
                    ['label' => 'Data', 'value' => esc($enrolledAt)],
                    ['label' => 'Estado', 'value' => 'Ativa'],
                ],
                'cta' => [
                    'url'   => $courseUrl,
                    'label' => 'Aceder ao curso',
                ],
            ]));

            if (! $email->send()) {
                log_message('warning', 'Falha ao enviar email de inscricao ao aluno.', [
                    'enrollment_id' => $enrollmentId,
                    'student_email' => $studentEmail,
                    'kind'          => $kind,
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            log_message('warning', 'Excecao ao enviar email de inscricao ao aluno: ' . $e->getMessage(), [
                'enrollment_id' => $enrollmentId,
                'student_email' => $studentEmail,
                'kind'          => $kind,
            ]);

            return false;
        }
    }

    private function resolveInstructorEmail(object $row): string
    {
        $instructorEmail = trim((string) ($row->instructor_login_email ?? ''));
        if ($instructorEmail === '') {
            $instructorEmail = trim((string) ($row->instructor_profile_email ?? ''));
        }

        return $instructorEmail;
    }

    private function createInAppNotification(
        int $userId,
        string $type,
        string $title,
        string $body,
        string $link,
        array $context
    ): void {
        if ($userId <= 0) {
            return;
        }

        try {
            (new UserNotificationModel())->createForUser($userId, $type, $title, $body, $link, $context);
        } catch (\Throwable $e) {
            log_message('warning', 'Falha ao criar notificacao in-app: ' . $e->getMessage(), [
                'user_id' => $userId,
                'type'    => $type,
            ]);
        }
    }

    private function formatDateTime(string $value): string
    {
        $timestamp = strtotime($value);

        if ($timestamp === false) {
            return $value;
        }

        return date('d/m/Y H:i', $timestamp);
    }
}
