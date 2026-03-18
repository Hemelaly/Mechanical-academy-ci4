<?php

namespace App\Services;

use App\Models\EnrollmentModel;
use App\Models\ExtendedUserModel;
use App\Models\PasswordResetModel;
use App\Models\PaymentModel;
use App\Models\StudentModel;
use CodeIgniter\Shield\Entities\User as ShieldUser;
use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class CheckoutEnrollmentService
{
    public function findUserByEmail(string $email): ?object
    {
        $email = trim(strtolower($email));
        if ($email === '') {
            return null;
        }

        return (new ShieldUserModel())->findByCredentials(['email' => $email]);
    }

    public function hasActiveEnrollment(int $userId, int $courseId): bool
    {
        return (new EnrollmentModel())
            ->where('id_student_enrollment', $userId)
            ->where('id_course_enrollment', $courseId)
            ->where('status_enrollment', 'ativa')
            ->first() !== null;
    }

    public function buildCourseAccessPath(int $courseId): string
    {
        return '/student/dashboard/ver_aulas/' . $courseId;
    }

    public function prepareCheckoutUser(
        string $email,
        string $fullName,
        ?object $authenticatedUser = null
    ): object {
        $user = $authenticatedUser ?: $this->findUserByEmail($email);

        if ($user && ! $this->canCheckoutAsStudent($user)) {
            throw new \RuntimeException('Esta conta nao pode ser usada para comprar cursos como estudante.');
        }

        if (! $user) {
            $user = $this->createStudentUser($email, $fullName);
        }

        $this->promoteToStudent((int) $user->id);
        $user = (new ShieldUserModel())->find((int) $user->id);

        if (! $user) {
            throw new \RuntimeException('Nao foi possivel carregar a conta do checkout.');
        }

        $resolvedName = $this->cleanName($fullName) ?: (string) ($user->username ?? '');
        $resolvedEmail = trim(strtolower($email));
        if ($resolvedEmail === '') {
            $resolvedEmail = trim(strtolower((string) ($this->getUserLoginEmail((int) $user->id) ?? '')));
        }

        $this->ensureStudentProfile((int) $user->id, $resolvedName, $resolvedEmail);

        return $user;
    }

    public function finalizeApprovedPayment(
        int $paymentId,
        int $courseId,
        string $courseTitle,
        string $email,
        string $fullName,
        ?object $authenticatedUser = null
    ): array {
        $db = db_connect();
        $db->transException(true);
        $db->transStart();

        $user = $this->prepareCheckoutUser($email, $fullName, $authenticatedUser);
        $enrollmentId = $this->ensureEnrollment((int) $user->id, $courseId);

        $paymentModel = new PaymentModel();
        $updated = $paymentModel->update($paymentId, [
            'id_user_payment'       => (int) $user->id,
            'id_enrollment_payment' => $enrollmentId,
            'status_payment'        => 'Aprovado',
            'approved_by_payment'   => 0,
            'updated_at'            => date('Y-m-d H:i:s'),
        ]);

        if (! $updated) {
            throw new \RuntimeException('Nao foi possivel atualizar o pagamento aprovado.');
        }

        $coursePath = $this->buildCourseAccessPath($courseId);
        $redirectUrl = $coursePath;

        if ($authenticatedUser === null) {
            $token = $this->issuePasswordResetToken((int) $user->id);
            $redirectUrl = site_url('reset-password') . '?' . http_build_query([
                'token'  => $token,
                'next'   => $coursePath,
                'course' => $courseTitle,
            ]);
        }

        $db->transComplete();

        return [
            'user_id'                => (int) $user->id,
            'enrollment_id'          => $enrollmentId,
            'course_path'            => $coursePath,
            'redirect_url'           => $redirectUrl,
            'requires_password_setup' => $authenticatedUser === null,
        ];
    }

    private function canCheckoutAsStudent(object $user): bool
    {
        $role = strtolower(trim((string) ($user->role ?? 'student')));

        return $role === '' || $role === 'student';
    }

    private function createStudentUser(string $email, string $fullName): object
    {
        $users = new ShieldUserModel();
        $normalizedEmail = trim(strtolower($email));

        $entity = new ShieldUser([
            'username' => $this->generateUniqueUsername($fullName, $email),
            'active'   => 1,
        ]);
        $entity->email = $normalizedEmail;
        $entity->password = bin2hex(random_bytes(8));

        if (! $users->save($entity)) {
            $existing = $users->findByCredentials(['email' => $normalizedEmail]);
            if ($existing) {
                return $existing;
            }

            throw new \RuntimeException(implode(', ', $users->errors() ?: ['Nao foi possivel criar a conta do aluno.']));
        }

        $userId = (int) $users->getInsertID();
        $created = $users->find($userId);

        if (! $created) {
            throw new \RuntimeException('Nao foi possivel carregar a conta criada.');
        }

        $users->addToDefaultGroup($created);
        $this->promoteToStudent($userId);

        return $users->find($userId);
    }

    private function promoteToStudent(int $userId): void
    {
        $extendedUsers = new ExtendedUserModel();
        $current = $extendedUsers->find($userId);

        if (! $current) {
            throw new \RuntimeException('Usuario nao encontrado para ativacao.');
        }

        $role = strtolower(trim((string) ($current->role ?? '')));
        if ($role !== '' && $role !== 'student') {
            throw new \RuntimeException('Esta conta nao pode ser convertida em estudante.');
        }

        $extendedUsers->update($userId, [
            'role'   => 'student',
            'active' => 1,
        ]);
    }

    private function ensureStudentProfile(int $userId, string $fullName, string $email): void
    {
        $studentModel = new StudentModel();
        $normalizedEmail = trim(strtolower($email));
        $student = $studentModel->where('id_user_student', $userId)->first();
        $conflictingStudent = $this->findStudentByEmail($normalizedEmail);

        if ($conflictingStudent && (int) $conflictingStudent->id_user_student !== $userId) {
            $this->releaseStaleStudentEmailConflict(
                (int) $conflictingStudent->id_student,
                (int) $conflictingStudent->id_user_student,
                $normalizedEmail
            );
        }

        $data = [
            'id_user_student' => $userId,
            'name_student'    => $this->cleanName($fullName),
            'email_student'   => $normalizedEmail,
        ];

        if ($student) {
            $updated = $studentModel->update((int) $student->id_student, [
                'name_student'  => $data['name_student'],
                'email_student' => $data['email_student'],
            ]);

            if (! $updated) {
                throw new \RuntimeException(implode(', ', $studentModel->errors() ?: ['Nao foi possivel atualizar o perfil do aluno.']));
            }

            return;
        }

        $inserted = $studentModel->insert($data, true);
        if ($inserted === false) {
            throw new \RuntimeException(implode(', ', $studentModel->errors() ?: ['Nao foi possivel criar o perfil do aluno.']));
        }
    }

    private function findStudentByEmail(string $email): ?object
    {
        if ($email === '') {
            return null;
        }

        return (new StudentModel())
            ->where('LOWER(email_student)', $email)
            ->first();
    }

    private function releaseStaleStudentEmailConflict(int $studentId, int $ownerUserId, string $requestedEmail): void
    {
        $ownerLoginEmail = $this->getUserLoginEmail($ownerUserId);
        $ownerLoginEmail = trim(strtolower($ownerLoginEmail ?? ''));

        if ($ownerLoginEmail === '' || $ownerLoginEmail === $requestedEmail) {
            throw new \RuntimeException('Este email ja esta vinculado a outro perfil de aluno.');
        }

        $updated = db_connect()
            ->table('students')
            ->where('id_student', $studentId)
            ->update([
                'email_student' => $ownerLoginEmail,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);

        if (! $updated) {
            throw new \RuntimeException('Nao foi possivel normalizar um perfil de aluno com email desatualizado.');
        }

        log_message('warning', 'Perfil de aluno normalizado durante checkout.', [
            'student_id'        => $studentId,
            'owner_user_id'     => $ownerUserId,
            'requested_email'   => $requestedEmail,
            'owner_login_email' => $ownerLoginEmail,
        ]);
    }

    private function getUserLoginEmail(int $userId): ?string
    {
        $row = db_connect()
            ->table('auth_identities')
            ->select('secret')
            ->where('user_id', $userId)
            ->where('type', 'email_password')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRow();

        return $row->secret ?? null;
    }

    private function ensureEnrollment(int $userId, int $courseId): int
    {
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel
            ->where('id_student_enrollment', $userId)
            ->where('id_course_enrollment', $courseId)
            ->first();

        if ($enrollment) {
            $updates = [];
            if (strtolower((string) ($enrollment->status_enrollment ?? '')) !== 'ativa') {
                $updates['status_enrollment'] = 'ativa';
            }

            if (empty($enrollment->enrolled_at_enrollment)) {
                $updates['enrolled_at_enrollment'] = date('Y-m-d');
            }

            if ($updates !== []) {
                $updated = $enrollmentModel->update((int) $enrollment->id_enrollment, $updates);
                if (! $updated) {
                    throw new \RuntimeException(implode(', ', $enrollmentModel->errors() ?: ['Nao foi possivel reativar a matricula.']));
                }
            }

            return (int) $enrollment->id_enrollment;
        }

        $inserted = $enrollmentModel->insert([
            'id_student_enrollment'  => $userId,
            'id_course_enrollment'   => $courseId,
            'status_enrollment'      => 'ativa',
            'enrolled_at_enrollment' => date('Y-m-d'),
        ], true);

        if ($inserted === false) {
            throw new \RuntimeException(implode(', ', $enrollmentModel->errors() ?: ['Nao foi possivel criar a matricula.']));
        }

        return (int) $enrollmentModel->getInsertID();
    }

    private function issuePasswordResetToken(int $userId): string
    {
        $passwordResetModel = new PasswordResetModel();
        $passwordResetModel->where('user_id', $userId)->delete();

        $token = bin2hex(random_bytes(16));

        $passwordResetModel->insert([
            'user_id'    => $userId,
            'token'      => $token,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $token;
    }

    private function generateUniqueUsername(string $fullName, string $email): string
    {
        $base = $this->cleanName($fullName);
        if ($base === '') {
            $base = trim((string) strstr($email, '@', true));
        }
        if ($base === '') {
            $base = 'Aluno';
        }

        $base = $this->slice($base, 30);
        $db = db_connect();
        $counter = 1;

        while (true) {
            $suffix = $counter === 1 ? '' : ' ' . $counter;
            $candidate = $this->slice($base, 30 - $this->length($suffix)) . $suffix;

            $exists = $db->table('users')
                ->select('id')
                ->where('username', $candidate)
                ->get()
                ->getRow();

            if (! $exists) {
                return $candidate;
            }

            $counter++;
        }
    }

    private function cleanName(string $value): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');

        return $this->slice($value, 100);
    }

    private function slice(string $value, int $length): string
    {
        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, $length);
        }

        return substr($value, 0, $length);
    }

    private function length(string $value): int
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }

        return strlen($value);
    }
}
