<?php

namespace App\Services;

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

        $instructorEmail = trim((string) ($row->instructor_login_email ?? ''));
        if ($instructorEmail === '') {
            $instructorEmail = trim((string) ($row->instructor_profile_email ?? ''));
        }

        if ($instructorEmail === '') {
            log_message('warning', 'Nao foi possivel notificar o instrutor sobre nova inscricao: email ausente.', [
                'enrollment_id' => $enrollmentId,
                'course'        => $row->title_course ?? null,
            ]);

            return false;
        }

        $instructorName = trim((string) ($row->instructor_name ?? 'Instrutor'));
        $studentName = trim((string) ($row->name_student ?? 'Aluno'));
        $studentEmail = trim((string) ($row->email_student ?? ''));
        $courseTitle = trim((string) ($row->title_course ?? 'Curso'));
        $enrolledAt = $this->formatDateTime(
            (string) ($row->enrolled_at_enrollment ?: $row->enrollment_created_at)
        );
        $studentsUrl = site_url('instructor/dashboard/meus_estudantes');

        try {
            $email = \Config\Services::email();
            $email->setTo($instructorEmail);
            $email->setSubject('Nova inscricao no seu curso');
            $email->setMailType('html');
            $email->setMessage(
                '<p>Ola ' . esc($instructorName) . ',</p>' .
                '<p>Uma nova inscricao foi registada na plataforma.</p>' .
                '<p><strong>Aluno:</strong> ' . esc($studentName) . '</p>' .
                '<p><strong>Email:</strong> ' . esc($studentEmail !== '' ? $studentEmail : 'Nao informado') . '</p>' .
                '<p><strong>Curso:</strong> ' . esc($courseTitle) . '</p>' .
                '<p><strong>Data da inscricao:</strong> ' . esc($enrolledAt) . '</p>' .
                '<p><a href="' . esc($studentsUrl) . '">Ver pagina de estudantes</a></p>'
            );

            if (! $email->send()) {
                log_message('warning', 'Falha ao enviar email de nova inscricao ao instrutor.', [
                    'enrollment_id'   => $enrollmentId,
                    'instructor_email' => $instructorEmail,
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            log_message('warning', 'Excecao ao enviar email de nova inscricao ao instrutor: ' . $e->getMessage(), [
                'enrollment_id'   => $enrollmentId,
                'instructor_email' => $instructorEmail,
            ]);

            return false;
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
