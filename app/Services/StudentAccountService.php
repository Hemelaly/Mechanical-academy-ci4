<?php

namespace App\Services;

use App\Models\ExtendedUserModel;
use App\Models\StudentModel;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Entities\User as ShieldUser;
use CodeIgniter\Shield\Models\UserIdentityModel;
use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class StudentAccountService
{
    public const GOOGLE_IDENTITY = 'google';

    public function findByEmail(string $email): ?object
    {
        $email = trim(strtolower($email));
        if ($email === '') {
            return null;
        }

        return (new ShieldUserModel())->findByCredentials(['email' => $email]);
    }

    public function findByGoogleId(string $googleId): ?object
    {
        $googleId = trim($googleId);
        if ($googleId === '') {
            return null;
        }

        $row = db_connect()
            ->table('auth_identities')
            ->select('user_id')
            ->where('type', self::GOOGLE_IDENTITY)
            ->where('secret', $googleId)
            ->get()
            ->getRow();

        if (! $row) {
            return null;
        }

        return (new ShieldUserModel())->find((int) $row->user_id);
    }

    public function createStudent(string $email, string $fullName, ?string $plainPassword = null): object
    {
        $users = new ShieldUserModel();
        $normalizedEmail = trim(strtolower($email));

        $entity = new ShieldUser([
            'username' => $this->generateUniqueUsername($fullName, $normalizedEmail),
            'active'   => 1,
        ]);
        $entity->email    = $normalizedEmail;
        $entity->password = $plainPassword ?: bin2hex(random_bytes(10));

        if (! $users->save($entity)) {
            $existing = $this->findByEmail($normalizedEmail);
            if ($existing) {
                $this->ensureStudentProfile($existing, $fullName, $normalizedEmail);

                return $existing;
            }

            throw new \RuntimeException(implode(', ', $users->errors() ?: ['Não foi possível criar a conta.']));
        }

        $created = $users->find((int) $users->getInsertID());
        if (! $created) {
            throw new \RuntimeException('Não foi possível carregar a conta criada.');
        }

        $users->addToDefaultGroup($created);
        $this->ensureStudentProfile($created, $fullName, $normalizedEmail);

        return $users->find((int) $created->id) ?: $created;
    }

    public function ensureStudentProfile(object $user, string $fullName, string $email): void
    {
        $userId = (int) ($user->id ?? 0);
        if ($userId <= 0) {
            return;
        }

        $extended = new ExtendedUserModel();
        $current  = $extended->find($userId);
        $role     = strtolower(trim((string) ($current->role ?? $user->role ?? '')));

        if ($role === '' || $role === 'student') {
            $extended->update($userId, [
                'role'   => 'student',
                'active' => 1,
            ]);
            $role = 'student';
        }

        if ($role !== 'student') {
            return;
        }

        $studentModel    = new StudentModel();
        $normalizedEmail = trim(strtolower($email));
        $name            = $this->cleanName($fullName) ?: ($user->username ?? 'Aluno');
        $student         = $studentModel->where('id_user_student', $userId)->first();

        $data = [
            'id_user_student' => $userId,
            'name_student'    => $name,
            'email_student'   => $normalizedEmail,
        ];

        if ($student) {
            $studentModel->skipValidation(true)->update((int) $student->id_student, [
                'name_student'  => $data['name_student'],
                'email_student' => $data['email_student'],
            ]);

            return;
        }

        $inserted = $studentModel->insert($data, true);
        if ($inserted === false) {
            log_message('warning', 'Falha ao criar perfil de aluno: {errors}', [
                'errors' => implode(', ', $studentModel->errors() ?: ['desconhecido']),
            ]);
        }
    }

    public function linkGoogle(object $user, string $googleId, string $email): void
    {
        $userId   = (int) ($user->id ?? 0);
        $googleId = trim($googleId);
        if ($userId <= 0 || $googleId === '') {
            return;
        }

        $identities = model(UserIdentityModel::class);
        $existing   = $identities
            ->where('user_id', $userId)
            ->where('type', self::GOOGLE_IDENTITY)
            ->where('secret', $googleId)
            ->first();

        if ($existing) {
            return;
        }

        try {
            $identities->create([
                'user_id' => $userId,
                'type'    => self::GOOGLE_IDENTITY,
                'name'    => 'google',
                'secret'  => $googleId,
                'secret2' => trim(strtolower($email)),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Não foi possível ligar identidade Google: {error}', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function completeLogin(object $user): void
    {
        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();
        $authenticator->startLogin($user);

        if (method_exists($user, 'activate')) {
            $user->activate();
        }

        $authenticator->completeLogin($user);
    }

    public function generateUniqueUsername(string $fullName, string $email): string
    {
        $base = $this->cleanName($fullName);
        if ($base === '') {
            $base = trim((string) strstr($email, '@', true));
        }
        if ($base === '') {
            $base = 'Aluno';
        }

        $base    = $this->slice($base, 30);
        $db      = db_connect();
        $counter = 1;

        while (true) {
            $suffix    = $counter === 1 ? '' : ' ' . $counter;
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
        return trim((string) preg_replace('/\s+/', ' ', $value));
    }

    private function slice(string $value, int $max): string
    {
        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, $max);
        }

        return substr($value, 0, $max);
    }

    private function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }
}
