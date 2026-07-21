<?php

namespace App\Services;

use App\Models\EnrollmentModel;
use App\Models\ExtendedUserModel;
use App\Models\PasswordResetModel;
use App\Models\PaymentModel;
use App\Models\PendingUserModel;
use App\Models\StudentModel;
use CodeIgniter\Shield\Entities\User as ShieldUser;
use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class CheckoutEnrollmentService
{
    private EnrollmentNotificationService $enrollmentNotificationService;

    public function __construct()
    {
        $this->enrollmentNotificationService = new EnrollmentNotificationService();
    }

    public function findUserByEmail(string $email): ?object
    {
        $email = trim(strtolower($email));
        if ($email === '') {
            return null;
        }

        return (new ShieldUserModel())->findByCredentials(['email' => $email]);
    }

    public function findPendingPaidCheckout(string $email, int $courseId): ?object
    {
        $email = trim(strtolower($email));
        if ($email === '' || $courseId <= 0) {
            return null;
        }

        return (new PendingUserModel())
            ->where('email', $email)
            ->where('course_id', $courseId)
            ->where('status', 'paid')
            ->orderBy('id', 'DESC')
            ->first();
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
        ?object $authenticatedUser = null,
        int $preferredUserId = 0
    ): object {
        $user = $authenticatedUser;

        if (! $user && $preferredUserId > 0) {
            $user = (new ShieldUserModel())->find($preferredUserId);
        }

        if (! $user) {
            $user = $this->findUserByEmail($email);
        }

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
        ?object $authenticatedUser = null,
        int $preferredUserId = 0
    ): array {
        $db = db_connect();
        $db->transException(true);
        $db->transStart();

        $user = $this->prepareCheckoutUser($email, $fullName, $authenticatedUser, $preferredUserId);
        $enrollment = $this->ensureEnrollment((int) $user->id, $courseId);
        $enrollmentId = $enrollment['id'];

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

        if ($enrollment['created']) {
            $this->enrollmentNotificationService->notifyInstructorAboutNewEnrollment($enrollmentId);
        }

        return [
            'user_id'                 => (int) $user->id,
            'enrollment_id'           => $enrollmentId,
            'course_path'             => $coursePath,
            'redirect_url'            => $redirectUrl,
            'requires_password_setup' => $authenticatedUser === null,
            'email_sent'              => true,
        ];
    }

    public function finalizeApprovedGuestPayment(
        int $paymentId,
        int $courseId,
        string $courseTitle,
        string $email,
        string $fullName
    ): array {
        $normalizedEmail = trim(strtolower($email));
        $resolvedName = $this->cleanName($fullName);

        if ($normalizedEmail === '') {
            throw new \RuntimeException('Nao foi possivel identificar o email do checkout pendente.');
        }

        if ($resolvedName === '') {
            $resolvedName = 'Aluno';
        }

        $existingUser = $this->findUserByEmail($normalizedEmail);
        if ($existingUser && ! $this->canCheckoutAsStudent($existingUser)) {
            throw new \RuntimeException('Esta conta nao pode ser usada para concluir a compra como estudante.');
        }

        $paymentModel = new PaymentModel();
        $pendingModel = new PendingUserModel();
        $payment = $paymentModel->find($paymentId);

        if (! $payment) {
            throw new \RuntimeException('Pagamento nao encontrado para criar o acesso pendente.');
        }

        if ((int) ($payment->id_enrollment_payment ?? 0) > 0) {
            return [
                'redirect_url'            => $this->buildCourseAccessPath($courseId),
                'course_path'             => $this->buildCourseAccessPath($courseId),
                'requires_password_setup' => false,
                'email_sent'              => true,
                'pending_user_id'         => 0,
            ];
        }

        $db = db_connect();
        $db->transException(true);
        $db->transStart();

        $setupToken = bin2hex(random_bytes(16));
        $setupExpiresAt = date('Y-m-d H:i:s', strtotime('+1 day'));

        $pendingUser = $pendingModel
            ->where('payment_id', $paymentId)
            ->first();

        $pendingData = [
            'username'         => $resolvedName,
            'email'            => $normalizedEmail,
            'course_id'        => $courseId,
            'payment_id'       => $paymentId,
            'status'           => 'paid',
            'setup_token'      => $setupToken,
            'setup_expires_at' => $setupExpiresAt,
        ];

        if ($pendingUser) {
            $updated = $pendingModel->update((int) $pendingUser->id, $pendingData);
            if (! $updated) {
                throw new \RuntimeException('Nao foi possivel atualizar o acesso pendente.');
            }

            $pendingId = (int) $pendingUser->id;
        } else {
            $pendingId = (int) $pendingModel->insert($pendingData, true);
            if ($pendingId <= 0) {
                throw new \RuntimeException(implode(', ', $pendingModel->errors() ?: ['Nao foi possivel criar o acesso pendente.']));
            }
        }

        $updatedPayment = $paymentModel->update($paymentId, [
            'id_user_payment'       => $pendingId,
            'id_enrollment_payment' => 0,
            'status_payment'        => 'Aprovado',
            'approved_by_payment'   => 0,
            'guest_email_payment'   => $normalizedEmail,
            'guest_name_payment'    => $resolvedName,
            'updated_at'            => date('Y-m-d H:i:s'),
        ]);

        if (! $updatedPayment) {
            throw new \RuntimeException('Nao foi possivel atualizar o pagamento aprovado para acesso pendente.');
        }

        $coursePath = $this->buildCourseAccessPath($courseId);
        $redirectUrl = site_url('reset-password') . '?' . http_build_query([
            'token'  => $setupToken,
            'next'   => $coursePath,
            'course' => $courseTitle,
        ]);

        $db->transComplete();

        $emailSent = $this->sendPendingSetupEmail($normalizedEmail, $resolvedName, $courseTitle, $redirectUrl);

        return [
            'pending_user_id'         => $pendingId,
            'course_path'             => $coursePath,
            'redirect_url'            => $redirectUrl,
            'requires_password_setup' => true,
            'email_sent'              => $emailSent,
        ];
    }

    public function completePendingUserCheckout(string $token, string $password): array
    {
        $pendingModel = new PendingUserModel();
        $pendingUser = $pendingModel
            ->where('setup_token', trim($token))
            ->first();

        if (! $pendingUser) {
            throw new \RuntimeException('Token invalido.');
        }

        if (! empty($pendingUser->setup_expires_at) && strtotime((string) $pendingUser->setup_expires_at) < time()) {
            throw new \RuntimeException('Token expirado.');
        }

        $paymentModel = new PaymentModel();
        $payment = $paymentModel->find((int) $pendingUser->payment_id);

        if (! $payment) {
            throw new \RuntimeException('Pagamento associado ao acesso pendente nao foi encontrado.');
        }

        $db = db_connect();
        $db->transException(true);
        $db->transStart();

        $existingUser = $this->findUserByEmail((string) $pendingUser->email);
        if ($existingUser) {
            if (! $this->canCheckoutAsStudent($existingUser)) {
                throw new \RuntimeException('Esta conta nao pode ser usada para ativar a inscricao.');
            }

            $user = $this->setUserPassword((int) $existingUser->id, $password);
        } else {
            $user = $this->createStudentUser((string) $pendingUser->email, (string) $pendingUser->username, $password);
        }

        $this->promoteToStudent((int) $user->id);
        $user = (new ShieldUserModel())->find((int) $user->id);

        if (! $user) {
            throw new \RuntimeException('Nao foi possivel carregar a conta apos criar a senha.');
        }

        $this->ensureStudentProfile((int) $user->id, (string) $pendingUser->username, (string) $pendingUser->email);
        $enrollment = $this->ensureEnrollment((int) $user->id, (int) $pendingUser->course_id);
        $enrollmentId = $enrollment['id'];

        $updated = $paymentModel->update((int) $payment->id_payment, [
            'id_user_payment'       => (int) $user->id,
            'id_enrollment_payment' => $enrollmentId,
            'status_payment'        => 'Aprovado',
            'approved_by_payment'   => 0,
            'updated_at'            => date('Y-m-d H:i:s'),
        ]);

        if (! $updated) {
            throw new \RuntimeException('Nao foi possivel concluir a ativacao do pagamento.');
        }

        $pendingModel->delete((int) $pendingUser->id);
        (new PasswordResetModel())->where('user_id', (int) $user->id)->delete();

        $db->transComplete();

        if ($enrollment['created']) {
            $this->enrollmentNotificationService->notifyInstructorAboutNewEnrollment($enrollmentId);
        }

        return [
            'user'          => $user,
            'user_id'       => (int) $user->id,
            'enrollment_id' => $enrollmentId,
            'course_id'     => (int) $pendingUser->course_id,
            'course_path'   => $this->buildCourseAccessPath((int) $pendingUser->course_id),
        ];
    }

    private function canCheckoutAsStudent(object $user): bool
    {
        $role = strtolower(trim((string) ($user->role ?? 'student')));

        return $role === '' || $role === 'student';
    }

    private function createStudentUser(string $email, string $fullName, ?string $plainPassword = null): object
    {
        $users = new ShieldUserModel();
        $normalizedEmail = trim(strtolower($email));

        $entity = new ShieldUser([
            'username' => $this->generateUniqueUsername($fullName, $email),
            'active'   => 1,
        ]);
        $entity->email = $normalizedEmail;
        $entity->password = $plainPassword ?: bin2hex(random_bytes(8));

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

    private function setUserPassword(int $userId, string $password): object
    {
        $users = new ShieldUserModel();
        $user = $users->find($userId);

        if (! $user) {
            throw new \RuntimeException('Usuario nao encontrado para configuracao de senha.');
        }

        $user->password = $password;
        if (! $users->save($user)) {
            throw new \RuntimeException(implode(', ', $users->errors() ?: ['Nao foi possivel atualizar a senha.']));
        }

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

    private function ensureEnrollment(int $userId, int $courseId): array
    {
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel
            ->where('id_student_enrollment', $userId)
            ->where('id_course_enrollment', $courseId)
            ->first();

        if ($enrollment) {
            $updates = array_merge([
                'status_enrollment' => 'ativa',
            ], (new DemoEnrollmentService())->clearedDemoPayload());

            if (empty($enrollment->enrolled_at_enrollment)) {
                $updates['enrolled_at_enrollment'] = date('Y-m-d');
            }

            $updated = $enrollmentModel->update((int) $enrollment->id_enrollment, $updates);
            if (! $updated) {
                throw new \RuntimeException(implode(', ', $enrollmentModel->errors() ?: ['Nao foi possivel reativar a matricula.']));
            }

            return [
                'id'      => (int) $enrollment->id_enrollment,
                'created' => false,
            ];
        }

        $inserted = $enrollmentModel->insert(array_merge([
            'id_student_enrollment'  => $userId,
            'id_course_enrollment'   => $courseId,
            'status_enrollment'      => 'ativa',
            'enrolled_at_enrollment' => date('Y-m-d'),
        ], (new DemoEnrollmentService())->clearedDemoPayload()), true);

        if ($inserted === false) {
            throw new \RuntimeException(implode(', ', $enrollmentModel->errors() ?: ['Nao foi possivel criar a matricula.']));
        }

        return [
            'id'      => (int) $enrollmentModel->getInsertID(),
            'created' => true,
        ];
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

    private function sendPendingSetupEmail(string $email, string $fullName, string $courseTitle, string $link): bool
    {
        try {
            $mail = \Config\Services::email();
            $mail->setTo($email);
            $mail->setSubject('Configure a sua senha e ative o acesso ao curso');
            $mail->setMailType('html');
            $mail->setMessage(
                '<p>Ola ' . esc($fullName) . ',</p>' .
                '<p>Recebemos o seu pagamento do curso <strong>' . esc($courseTitle) . '</strong>.</p>' .
                '<p>Para ativar o acesso, configure a sua senha no link abaixo:</p>' .
                '<p><a href="' . esc($link) . '">Configurar senha e ativar acesso</a></p>' .
                '<p>Se o botao nao funcionar, copie e cole este link no navegador:</p>' .
                '<p>' . esc($link) . '</p>'
            );

            if (! $mail->send()) {
                log_message('warning', 'Falha ao enviar email de configuracao do checkout pendente.', [
                    'email' => $email,
                    'course' => $courseTitle,
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            log_message('warning', 'Excecao ao enviar email de configuracao do checkout pendente: ' . $e->getMessage(), [
                'email' => $email,
                'course' => $courseTitle,
            ]);

            return false;
        }
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
