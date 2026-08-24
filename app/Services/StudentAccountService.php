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

    /**
     * Cria user Shield + perfil de aluno. Usado no registo normal e no Google OAuth.
     */
    public function createStudent(string $email, string $fullName, ?string $plainPassword = null): object
    {
        $users = new ShieldUserModel();
        $normalizedEmail = trim(strtolower($email));
        if ($normalizedEmail === '' || ! filter_var($normalizedEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Email inválido para criar conta.');
        }

        $existing = $this->findByEmail($normalizedEmail);
        if ($existing) {
            $this->ensureStudentProfile($existing, $fullName, $normalizedEmail);

            return $existing;
        }

        $entity = new ShieldUser([
            'username' => $this->generateUniqueUsername($fullName, $normalizedEmail),
            'active'   => 1,
        ]);
        $entity->email    = $normalizedEmail;
        $entity->password = $plainPassword ?: bin2hex(random_bytes(16));

        $db = db_connect();
        $db->transStart();

        try {
            if (! $users->save($entity)) {
                // Corrida: outro pedido criou o email entretanto
                $existing = $this->findByEmail($normalizedEmail);
                if ($existing) {
                    $this->ensureStudentProfile($existing, $fullName, $normalizedEmail);
                    $db->transComplete();

                    return $existing;
                }

                throw new \RuntimeException(implode(', ', $users->errors() ?: ['Não foi possível criar a conta.']));
            }

            $userId = (int) $users->getInsertID();
            $created = $users->find($userId);
            if (! $created) {
                throw new \RuntimeException('Não foi possível carregar a conta criada.');
            }

            $users->addToDefaultGroup($created);
            $this->ensureStudentProfile($created, $fullName, $normalizedEmail, true);

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Falha na transação ao criar a conta.');
            }

            return $users->find($userId) ?: $created;
        } catch (\Throwable $e) {
            $db->transRollback();

            throw $e;
        }
    }

    /**
     * Garante role=student e linha em `students`.
     *
     * @param bool $required Se true, falha se não conseguir criar o perfil de aluno.
     */
    public function ensureStudentProfile(object $user, string $fullName, string $email, bool $required = false): void
    {
        $userId = (int) ($user->id ?? 0);
        if ($userId <= 0) {
            if ($required) {
                throw new \RuntimeException('Utilizador inválido ao criar perfil de aluno.');
            }

            return;
        }

        $extended = new ExtendedUserModel();
        $current  = $extended->find($userId);
        $role     = strtolower(trim((string) ($current->role ?? $user->role ?? '')));

        if ($role === '' || $role === 'student') {
            $extended->skipValidation(true)->update($userId, [
                'role'   => 'student',
                'active' => 1,
            ]);
            $role = 'student';
        }

        if ($role !== 'student') {
            if ($required) {
                throw new \RuntimeException('Esta conta não pode ser usada como aluno.');
            }

            return;
        }

        $studentModel    = new StudentModel();
        $normalizedEmail = trim(strtolower($email));
        $name            = $this->cleanName($fullName) ?: ($user->username ?? 'Aluno');
        $student         = $studentModel->where('id_user_student', $userId)->first();

        if (! $student && $normalizedEmail !== '') {
            $student = $studentModel->where('email_student', $normalizedEmail)->first();
        }

        $data = [
            'id_user_student' => $userId,
            'name_student'    => $this->slice($name, 100),
            'email_student'   => $normalizedEmail,
        ];

        if ($student) {
            $studentModel->skipValidation(true)->update((int) $student->id_student, [
                'id_user_student' => $userId,
                'name_student'    => $data['name_student'],
                'email_student'   => $data['email_student'],
            ]);

            return;
        }

        $inserted = $studentModel->skipValidation(true)->insert($data, true);
        if ($inserted === false) {
            $msg = implode(', ', $studentModel->errors() ?: ['desconhecido']);
            log_message('error', 'Falha ao criar perfil de aluno: {errors}', ['errors' => $msg]);
            if ($required) {
                throw new \RuntimeException('Não foi possível criar o perfil de aluno: ' . $msg);
            }
        }
    }

    public function linkGoogle(object $user, string $googleId, string $email): void
    {
        $userId   = (int) ($user->id ?? 0);
        $googleId = trim($googleId);
        $email    = trim(strtolower($email));
        if ($userId <= 0 || $googleId === '') {
            throw new \RuntimeException('Dados Google inválidos para ligar à conta.');
        }

        // Evitar que o mesmo Google ID fique ligado a outra conta
        $taken = db_connect()
            ->table('auth_identities')
            ->select('user_id')
            ->where('type', self::GOOGLE_IDENTITY)
            ->where('secret', $googleId)
            ->where('user_id !=', $userId)
            ->get()
            ->getRow();

        if ($taken) {
            throw new \RuntimeException('Esta conta Google já está ligada a outro utilizador.');
        }

        $identities = model(UserIdentityModel::class);
        $existing   = $identities
            ->where('user_id', $userId)
            ->where('type', self::GOOGLE_IDENTITY)
            ->where('secret', $googleId)
            ->first();

        if ($existing) {
            if ($email !== '') {
                $identities->update((int) $existing->id, ['secret2' => $email]);
            }

            return;
        }

        try {
            $identities->create([
                'user_id' => $userId,
                'type'    => self::GOOGLE_IDENTITY,
                'name'    => 'google',
                'secret'  => $googleId,
                'secret2' => $email,
            ]);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Não foi possível ligar a identidade Google: ' . $e->getMessage(), 0, $e);
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
        $base = $this->sanitizeUsernameSource($fullName);
        if ($base === '') {
            $base = $this->sanitizeUsernameSource((string) strstr($email, '@', true));
        }
        if ($base === '') {
            $base = 'Aluno';
        }

        // Regra Shield: 3–30 chars, começa alfanumérico, permite espaço . _ -
        $base = preg_replace('/[^a-zA-ZÀ-ÿ0-9 ._-]/u', '', $base) ?? '';
        $base = trim((string) preg_replace('/\s+/', ' ', $base));
        $base = $this->slice($base, 30);
        if ($this->length($base) < 3) {
            $base = 'Aluno';
        }

        $db      = db_connect();
        $counter = 1;

        while (true) {
            $suffix    = $counter === 1 ? '' : ' ' . $counter;
            $candidate = $this->slice($base, 30 - $this->length($suffix)) . $suffix;
            if ($this->length($candidate) < 3) {
                $candidate = 'Aluno' . $suffix;
            }

            $exists = $db->table('users')
                ->select('id')
                ->where('username', $candidate)
                ->get()
                ->getRow();

            if (! $exists) {
                return $candidate;
            }

            $counter++;
            if ($counter > 500) {
                return 'Aluno ' . substr(bin2hex(random_bytes(4)), 0, 8);
            }
        }
    }

    private function sanitizeUsernameSource(string $value): string
    {
        $value = $this->cleanName($value);
        // Remove aspas/apóstrofos e símbolos que quebram a regex do Shield
        $value = str_replace(["'", '"', '`', '´', '’', '‘'], '', $value);

        return trim($value);
    }

    private function cleanName(string $value): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $value));
    }

    private function slice(string $value, int $max): string
    {
        if ($max < 1) {
            return '';
        }
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
