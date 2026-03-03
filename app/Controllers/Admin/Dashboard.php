<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CourseModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;
use App\Models\CourseSettingModel;
use App\Models\ExtendedUserModel;
use CodeIgniter\Shield\Models\UserModel;
use Config\Services;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Events\Events;

class Dashboard extends BaseController
{
    private function sidebarLinks()
    {
        return [
            ['label' => 'Início', 'icon' => 'bi-house-door', 'url' => '/admin/dashboard'],
            ['label' => 'Cursos', 'icon' => 'bi-book', 'url' => '/admin/dashboard/cursos'],
            ['label' => 'Estudantes', 'icon' => 'bi-people', 'url' => '/admin/dashboard/estudantes'],
            ['label' => 'Instrutores', 'icon' => 'bi-people', 'url' => '/admin/dashboard/instrutores'],
            ['label' => 'Finanças', 'icon' => 'bi-cash-coin', 'url' => '/admin/dashboard/financas'],
            ['label' => 'User Profile', 'icon' => 'bi-person-circle', 'url' => '/admin/dashboard/perfil'],
        ];
    }

    public function index()
    {
        $user = service('auth')->user();

        return view('pages/admin/home', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function courses()
    {
        $courseModel  = new CourseModel();
        $lessonsModel = new LessonModel();
        $db           = \Config\Database::connect();

        $user    = service('auth')->user();
        $courses = $courseModel->findAll();

        // TOTAL DE AULAS (todas)
        $totalLessonsAll = $lessonsModel
            ->join('modules m', 'm.id_module = lessons.id_module_lesson')
            ->countAllResults();

        // CONTAGEM DE INSCRITOS POR CURSO (considerando status_enrollment = 'ativa')
        $rows = $db->table('courses c')
            ->select('c.id_course, COUNT(DISTINCT e.id_student_enrollment) AS inscritos', false)
            ->join(
                'enrollments e',
                'e.id_course_enrollment = c.id_course AND e.status_enrollment = "ativa"',
                'left'
            )
            ->groupBy('c.id_course')
            ->get()->getResultArray();

        // Mapear para acesso rápido no view
        $enrolledCounts = [];
        $totalEnrolledAll = 0;
        foreach ($rows as $r) {
            $cid = (int)$r['id_course'];
            $qtd = (int)$r['inscritos'];
            $enrolledCounts[$cid] = $qtd;
            $totalEnrolledAll += $qtd;
        }

        $courses2 = $db->table('courses c')
            ->select('c.id_course, c.title_course AS course_title, u.id AS instructor_id, u.username AS instructor_name')
            ->join('users u', 'u.id = c.id_instructor_course', 'left') // left para cursos sem instrutor
            ->orderBy('c.title_course', 'ASC')
            ->get()->getResult();

        $activeCourses = $courseModel->where('status_course', 'Ativo')->countAllResults();

        return view('pages/admin/courses', [
            'user'          => $user,
            'courses'       => $courses,
            'courses2'       => $courses2,
            'activeCourses' => $activeCourses,
            'totalLessons'  => $totalLessonsAll,
            'enrolledCounts' => $enrolledCounts, // uso no card/lista por curso
            'totalEnrolled' => $totalEnrolledAll, // total geral (opcional)
            'sidebarLinks'  => $this->sidebarLinks(),
            'currentUrl'    => current_url(),
        ]);
    }


    public function add_course()
    {
        $user = service('auth')->user();

        return view('pages/admin/add_course', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function students()
    {
        $user = service('auth')->user();

        return view('pages/admin/students', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function studentsData()
    {
        return $this->usersDataResponse('student');
    }

    public function instructors()
    {
        $user = service('auth')->user();

        return view('pages/admin/instructors', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function instructorsData()
    {
        return $this->usersDataResponse('instructor');
    }

    public function financial()
    {
        $user = service('auth')->user();

        return view('pages/admin/financial', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function financialData()
    {
        $year = (int) $this->request->getGet('year');
        if ($year <= 0) {
            $year = (int) date('Y');
        }

        $db = db_connect();
        $rows = $db->table('payments p')
            ->select('MONTH(p.created_at) as month, SUM(p.amount_payment) as total')
            ->where('p.status_payment', 'Aprovado')
            ->where('YEAR(p.created_at)', $year)
            ->groupBy('MONTH(p.created_at)')
            ->orderBy('MONTH(p.created_at)')
            ->get()
            ->getResultArray();

        $totals = array_fill(1, 12, 0.0);
        foreach ($rows as $row) {
            $month = (int) ($row['month'] ?? 0);
            if ($month >= 1 && $month <= 12) {
                $totals[$month] = (float) ($row['total'] ?? 0);
            }
        }

        $labels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $series = array_values($totals);

        return $this->response->setJSON([
            'labels' => $labels,
            'data' => $series,
        ]);
    }

    public function profile()
    {
        $users     = new ExtendedUserModel();
        $userModel = new UserModel();
        $courseModel = new CourseModel();

        $user = service('auth')->user();

        if (! $user) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $profileUrl = current_url(false);
        $db = db_connect();
        $courseCount = (int) $courseModel->builder()->countAllResults();
        $avgProgressRow = $db->table('enrollments')
            ->selectAvg('progress_enrollment', 'avg_progress')
            ->where('status_enrollment', 'ativa')
            ->get()
            ->getRow();
        $avgProgress = (int) round($avgProgressRow->avg_progress ?? 0);

        if ($this->request->getMethod() !== 'POST') {
            return view('pages/admin/profile', [
                'user'         => $user,
                'courseCount'  => $courseCount,
                'avgProgress'  => $avgProgress,
                'sidebarLinks' => $this->sidebarLinks(),
                'currentUrl'   => $profileUrl
            ]);
        }

        $rules = [
            'nome'      => 'permit_empty|min_length[2]',
            'email'     => 'permit_empty|valid_email',
            'pais'      => 'permit_empty|max_length[100]',
            'provincia' => 'permit_empty|max_length[100]',
            'cidade'    => 'permit_empty|max_length[100]',
            'telefone'  => 'permit_empty|max_length[20]',
            'imagem'    => 'if_exist|is_image[imagem]|mime_in[imagem,image/jpg,image/jpeg,image/png,image/webp]|max_size[imagem,4096]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to($profileUrl)
                ->withInput()
                ->with('error', implode(', ', $this->validator->getErrors()));
        }

        $post = $this->request->getPost();
        $userName = trim($post['nome'] ?? '');
        $email    = trim($post['email'] ?? '');

        if (! empty($userName)) {
            $existingUser = $userModel
                ->where('username', $userName)
                ->where('id !=', $user->id)
                ->first();

            if ($existingUser) {
                return redirect()->to($profileUrl)
                    ->with('error', 'Já existe um usuário com esse nome!');
            }
        }

        if (! empty($email) && $email !== ($user->email ?? '')) {
            $existingEmail = db_connect()
                ->table('auth_identities')
                ->where('type', 'email_password')
                ->where('secret', $email)
                ->where('user_id !=', $user->id)
                ->get()
                ->getRow();

            if ($existingEmail) {
                return redirect()->to($profileUrl)
                    ->with('error', 'Já existe um usuário com esse email!');
            }
        }

        $file     = $this->request->getFile('imagem');
        $filePath = $user->img;

        if ($file && $file->isValid() && $file->getError() === UPLOAD_ERR_OK) {
            $targetDir = FCPATH . 'assets/img/';
            if (! is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            $newName = $file->getRandomName();

            if (! $file->move($targetDir, $newName)) {
                return redirect()->to($profileUrl)
                    ->withInput()
                    ->with('error', 'Falha ao mover a imagem.');
            }

            if (! empty($user->img)) {
                $old = FCPATH . $user->img;
                if (is_file($old)) {
                    @unlink($old);
                }
            }

            $filePath = 'assets/img/' . $newName;
        }

        $dataProfile = [
            'username' => $userName ?: $user->username,
            'img'      => $filePath,
            'country'  => $post['pais']      ?? $user->country,
            'province' => $post['provincia'] ?? $user->province,
            'city'     => $post['cidade']    ?? $user->city,
            'phone'    => $post['telefone']  ?? $user->phone,
        ];

        $currentPassword = $post['password_actual'] ?? '';
        $newPassword     = $post['new_password'] ?? '';
        $confirmPassword = $post['confirm_password'] ?? '';
        $emailChanged    = ! empty($email) && $email !== ($user->email ?? '');
        $wantsPassword   = $currentPassword || $newPassword || $confirmPassword;

        if ($emailChanged || $wantsPassword) {
            $identity = db_connect()
                ->table('auth_identities')
                ->where('user_id', $user->id)
                ->where('type', 'email_password')
                ->get()
                ->getRow();

            if (! $identity) {
                return redirect()->to($profileUrl)->with('error', 'Identidade de senha não encontrada.');
            }

            if ($emailChanged) {
                db_connect()
                    ->table('auth_identities')
                    ->where('id', $identity->id)
                    ->update([
                        'secret'     => $email,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }
        }

        if ($wantsPassword) {
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                return redirect()->to($profileUrl)->with('error', 'Para alterar a senha, preencha todos os campos.');
            }

            if (! password_verify($currentPassword, $identity->secret2)) {
                return redirect()->to($profileUrl)->with('error', 'A senha atual fornecida está incorreta.');
            }

            if (strlen($newPassword) < 6) {
                return redirect()->to($profileUrl)->with('error', 'A nova senha deve ter no mínimo 6 caracteres.');
            }

            if ($newPassword !== $confirmPassword) {
                return redirect()->to($profileUrl)->with('error', 'A confirmação da senha não coincide.');
            }

            db_connect()
                ->table('auth_identities')
                ->where('id', $identity->id)
                ->update([
                    'secret2'    => password_hash($newPassword, PASSWORD_DEFAULT),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        if (! $users->update($user->id, $dataProfile)) {
            return redirect()->to($profileUrl)
                ->with('error', implode(', ', $users->errors()));
        }

        $updated = $users->find($user->id);
        auth()->setUser($updated);

        return redirect()->to($profileUrl)->with('success', 'Perfil atualizado com sucesso.');
    }

    private function usersDataResponse(string $role)
    {
        $search = trim((string) $this->request->getGet('q'));
        $status = (string) $this->request->getGet('status');
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = (int) $this->request->getGet('per_page');
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min(max($perPage, 5), 50);

        $offset = ($page - 1) * $perPage;

        $builder = $this->baseUsersQuery($role);
        $this->applyUserFilters($builder, $search, $status);

        $countBuilder = clone $builder;
        $total = (int) $countBuilder->countAllResults();

        $rows = $builder
            ->orderBy('u.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row += [
                'img' => null,
                'country' => null,
                'province' => null,
                'city' => null,
                'phone' => null,
                'force_pass_reset' => 0,
            ];
        }
        unset($row);

        $totalPages = (int) ceil($total / $perPage);

        return $this->response->setJSON([
            'items' => $rows,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ]);
    }

    private function baseUsersQuery(string $role)
    {
        $db = db_connect();
        $tables = config('Auth')->tables;
        $usersTable = $tables['users'] ?? 'users';
        $identitiesTable = $tables['identities'] ?? 'auth_identities';

        $select = [
            'u.id',
            'u.username',
            'u.active',
            'u.last_active',
            'u.created_at',
            'u.role',
            'ai.secret as email',
        ];

        foreach ($this->usersOptionalColumns($db, $usersTable) as $column) {
            $select[] = "u.{$column}";
        }

        $builder = $db->table("{$usersTable} u")
            ->select(implode(', ', $select))
            ->join("{$identitiesTable} ai", 'ai.user_id = u.id AND ai.type = "email_password"', 'left')
            ->where('u.role', $role);

        if ($db->fieldExists('deleted_at', $usersTable)) {
            $builder->where('u.deleted_at', null);
        }

        return $builder;
    }

    private function usersOptionalColumns($db, string $usersTable): array
    {
        $candidates = ['img', 'country', 'province', 'city', 'phone', 'force_pass_reset'];
        $existing = [];

        foreach ($candidates as $column) {
            if ($db->fieldExists($column, $usersTable)) {
                $existing[] = $column;
            }
        }

        return $existing;
    }

    private function applyUserFilters($builder, string $search, string $status): void
    {
        if ($search !== '') {
            $builder->groupStart()
                ->like('u.username', $search)
                ->orLike('ai.secret', $search)
                ->orLike('u.id', $search)
                ->groupEnd();
        }

        if ($status === 'ativo') {
            $builder->where('u.active', 1);
        } elseif ($status === 'inativo') {
            $builder->where('u.active', 0);
        }
    }

    public function updateUser()
    {
        $id = (int) $this->request->getPost('id');
        $username = trim((string) $this->request->getPost('username'));
        $email = trim((string) $this->request->getPost('email'));
        $role = strtolower(trim((string) $this->request->getPost('role')));
        $active = (int) $this->request->getPost('active');
        $password = (string) $this->request->getPost('password');
        $passwordConfirm = (string) $this->request->getPost('password_confirm');
        $country = trim((string) $this->request->getPost('country'));
        $province = trim((string) $this->request->getPost('province'));
        $city = trim((string) $this->request->getPost('city'));
        $phone = trim((string) $this->request->getPost('phone'));
        $img = trim((string) $this->request->getPost('img'));
        $imageFile = $this->request->getFile('image_file');
        $forcePassResetRaw = $this->request->getPost('force_pass_reset');
        $forcePassReset = $forcePassResetRaw === null ? null : (int) $forcePassResetRaw;

        if ($id <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'ID invalido.',
                'csrf' => csrf_hash(),
            ]);
        }

        if (! in_array($role, ['student', 'instructor', 'admin'], true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Role invalido.',
                'csrf' => csrf_hash(),
            ]);
        }

        if ($username === '' || strlen($username) < 3 || strlen($username) > 30) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Username invalido. Use entre 3 e 30 caracteres.',
                'csrf' => csrf_hash(),
            ]);
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Email invalido.',
                'csrf' => csrf_hash(),
            ]);
        }

        if (! in_array($active, [0, 1], true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Status invalido.',
                'csrf' => csrf_hash(),
            ]);
        }

        if ($password !== '') {
            if (strlen($password) < 8) {
                return $this->response->setStatusCode(422)->setJSON([
                    'message' => 'A nova senha deve ter no minimo 8 caracteres.',
                    'csrf' => csrf_hash(),
                ]);
            }

            if ($password !== $passwordConfirm) {
                return $this->response->setStatusCode(422)->setJSON([
                    'message' => 'A confirmacao da senha nao confere.',
                    'csrf' => csrf_hash(),
                ]);
            }
        }

        $len = static function (string $value): int {
            return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
        };

        if ($country !== '' && $len($country) > 100) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Pais muito longo (maximo 100).',
                'csrf' => csrf_hash(),
            ]);
        }

        if ($province !== '' && $len($province) > 100) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Provincia muito longa (maximo 100).',
                'csrf' => csrf_hash(),
            ]);
        }

        if ($city !== '' && $len($city) > 100) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Cidade muito longa (maximo 100).',
                'csrf' => csrf_hash(),
            ]);
        }

        if ($phone !== '' && $len($phone) > 20) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Telefone muito longo (maximo 20).',
                'csrf' => csrf_hash(),
            ]);
        }

        if ($img !== '' && $len($img) > 255) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Campo de imagem muito longo (maximo 255).',
                'csrf' => csrf_hash(),
            ]);
        }

        if ($imageFile && $imageFile->isValid() && $imageFile->getError() === UPLOAD_ERR_OK) {
            $maxBytes = 4 * 1024 * 1024;
            if ($imageFile->getSize() > $maxBytes) {
                return $this->response->setStatusCode(422)->setJSON([
                    'message' => 'A imagem deve ter no maximo 4MB.',
                    'csrf' => csrf_hash(),
                ]);
            }

            $allowedMime = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
            if (! in_array(strtolower((string) $imageFile->getMimeType()), $allowedMime, true)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'message' => 'Formato de imagem invalido.',
                    'csrf' => csrf_hash(),
                ]);
            }
        }

        $db = db_connect();
        $tables = config('Auth')->tables;
        $usersTable = $tables['users'] ?? 'users';
        $identitiesTable = $tables['identities'] ?? 'auth_identities';
        $optionalColumns = $this->usersOptionalColumns($db, $usersTable);
        $hasForcePassReset = in_array('force_pass_reset', $optionalColumns, true);

        if ($hasForcePassReset && $forcePassReset !== null && ! in_array($forcePassReset, [0, 1], true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => 'Valor de reset de senha invalido.',
                'csrf' => csrf_hash(),
            ]);
        }

        $existingSelect = ['id', 'role'];
        if (in_array('img', $optionalColumns, true)) {
            $existingSelect[] = 'img';
        }

        $existingUser = $db->table($usersTable)
            ->select(implode(', ', $existingSelect))
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (! $existingUser) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Usuario nao encontrado.',
                'csrf' => csrf_hash(),
            ]);
        }

        $sameUsername = $db->table($usersTable)
            ->select('id')
            ->where('username', $username)
            ->where('id !=', $id)
            ->get()
            ->getRowArray();

        if ($sameUsername) {
            return $this->response->setStatusCode(409)->setJSON([
                'message' => 'Username ja esta em uso.',
                'csrf' => csrf_hash(),
            ]);
        }

        $sameEmail = $db->table($identitiesTable)
            ->select('id')
            ->where('type', 'email_password')
            ->where('secret', $email)
            ->where('user_id !=', $id)
            ->get()
            ->getRowArray();

        if ($sameEmail) {
            return $this->response->setStatusCode(409)->setJSON([
                'message' => 'Email ja esta em uso.',
                'csrf' => csrf_hash(),
            ]);
        }

        $identity = $db->table($identitiesTable)
            ->select('id')
            ->where('user_id', $id)
            ->where('type', 'email_password')
            ->get()
            ->getRowArray();

        if (! $identity) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Identidade de login nao encontrada.',
                'csrf' => csrf_hash(),
            ]);
        }

        $uploadedImagePath = null;
        if ($imageFile && $imageFile->isValid() && $imageFile->getError() === UPLOAD_ERR_OK && in_array('img', $optionalColumns, true)) {
            $targetDir = FCPATH . 'assets/img/';
            if (! is_dir($targetDir) && ! @mkdir($targetDir, 0755, true)) {
                return $this->response->setStatusCode(500)->setJSON([
                    'message' => 'Nao foi possivel preparar a pasta de imagem.',
                    'csrf' => csrf_hash(),
                ]);
            }

            $newName = $imageFile->getRandomName();
            if (! $imageFile->move($targetDir, $newName)) {
                return $this->response->setStatusCode(500)->setJSON([
                    'message' => 'Falha ao guardar a imagem enviada.',
                    'csrf' => csrf_hash(),
                ]);
            }

            $uploadedImagePath = 'assets/img/' . $newName;
        }

        $userData = [
            'username' => $username,
            'role' => $role,
            'active' => $active,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (in_array('country', $optionalColumns, true)) {
            $userData['country'] = $country !== '' ? $country : null;
        }

        if (in_array('province', $optionalColumns, true)) {
            $userData['province'] = $province !== '' ? $province : null;
        }

        if (in_array('city', $optionalColumns, true)) {
            $userData['city'] = $city !== '' ? $city : null;
        }

        if (in_array('phone', $optionalColumns, true)) {
            $userData['phone'] = $phone !== '' ? $phone : null;
        }

        if (in_array('img', $optionalColumns, true)) {
            if ($uploadedImagePath !== null) {
                $userData['img'] = $uploadedImagePath;
            } elseif ($img !== '') {
                $userData['img'] = $img;
            }
        }

        if ($hasForcePassReset && $forcePassReset !== null) {
            $userData['force_pass_reset'] = $forcePassReset;
        }

        $identityData = [
            'secret' => $email,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($password !== '') {
            $identityData['secret2'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($uploadedImagePath !== null && in_array('img', $optionalColumns, true)) {
            $oldImg = (string) ($existingUser['img'] ?? '');
            if ($oldImg !== '' && $oldImg !== $uploadedImagePath) {
                $normalized = str_replace('\\', '/', $oldImg);
                if (str_starts_with($normalized, 'assets/img/')) {
                    $oldPath = FCPATH . ltrim($oldImg, '/\\');
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }
            }
        }

        $db->transStart();
        $db->table($usersTable)->where('id', $id)->update($userData);
        $db->table($identitiesTable)->where('id', $identity['id'])->update($identityData);
        $this->syncRoleTableData($db, $id, $role, $username, $email);
        $db->transComplete();

        if (! $db->transStatus()) {
            $error = $db->error();
            log_message('error', 'Falha ao atualizar usuario #{id}: {msg}', [
                'id' => $id,
                'msg' => $error['message'] ?? 'erro desconhecido',
            ]);

            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Nao foi possivel atualizar o usuario.',
                'csrf' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'message' => 'Usuario atualizado com sucesso.',
            'csrf' => csrf_hash(),
        ]);
    }

    private function syncRoleTableData($db, int $userId, string $role, string $username, string $email): void
    {
        $student = $db->table('students')
            ->select('id_student')
            ->where('id_user_student', $userId)
            ->get()
            ->getRowArray();

        if ($student) {
            $db->table('students')
                ->where('id_student', $student['id_student'])
                ->update([
                    'name_student' => $username,
                    'email_student' => $email,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        $instructor = $db->table('instructors')
            ->select('id_instructor')
            ->where('id_user_instructor', $userId)
            ->get()
            ->getRowArray();

        if ($instructor) {
            $db->table('instructors')
                ->where('id_instructor', $instructor['id_instructor'])
                ->update([
                    'name_instructor' => $username,
                    'email_instructor' => $email,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        if ($role === 'student') {
            if (! $student) {
                $db->table('students')->insert([
                    'id_user_student' => $userId,
                    'name_student' => $username,
                    'email_student' => $email,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            return;
        }

        if ($role === 'instructor') {
            if (! $instructor) {
                $db->table('instructors')->insert([
                    'id_user_instructor' => $userId,
                    'name_instructor' => $username,
                    'email_instructor' => $email,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function toggleUserStatus()
    {
        $id = (int) $this->request->getPost('id');
        $role = (string) $this->request->getPost('role');

        if ($id <= 0 || ! in_array($role, ['student', 'instructor'], true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Dados invalidos.',
                'csrf' => csrf_hash(),
            ]);
        }

        $users = new ExtendedUserModel();
        $user = $users->find($id);
        if (! $user || $user->role !== $role) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Usuario nao encontrado.',
                'csrf' => csrf_hash(),
            ]);
        }

        $newStatus = $user->active ? 0 : 1;
        $users->update($id, ['active' => $newStatus]);

        return $this->response->setJSON([
            'message' => $newStatus ? 'Usuario ativado.' : 'Usuario desativado.',
            'active' => $newStatus,
            'csrf' => csrf_hash(),
        ]);
    }

    public function deleteUser()
    {
        $id = (int) $this->request->getPost('id');
        $role = (string) $this->request->getPost('role');

        if ($id <= 0 || ! in_array($role, ['student', 'instructor'], true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Dados invalidos.',
                'csrf' => csrf_hash(),
            ]);
        }

        $db = db_connect();
        $user = $db->table('users')
            ->select('id, role')
            ->where('id', $id)
            ->get()
            ->getRow();

        if (! $user || $user->role !== $role) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Usuario nao encontrado.',
                'csrf' => csrf_hash(),
            ]);
        }

        $tables = config('Auth')->tables;
        $usersTable = $tables['users'] ?? 'users';
        $identitiesTable = $tables['identities'] ?? 'auth_identities';
        $groupsUsersTable = $tables['groups_users'] ?? 'auth_groups_users';
        $permissionsUsersTable = $tables['permissions_users'] ?? 'auth_permissions_users';
        $rememberTable = $tables['remember_tokens'] ?? 'auth_remember_tokens';
        $loginsTable = $tables['logins'] ?? 'auth_logins';
        $tokenLoginsTable = $tables['token_logins'] ?? 'auth_token_logins';

        $db->transStart();
        $db->table('students')->where('id_user_student', $id)->delete();
        $db->table('instructors')->where('id_user_instructor', $id)->delete();
        $db->table($rememberTable)->where('user_id', $id)->delete();
        $db->table($permissionsUsersTable)->where('user_id', $id)->delete();
        $db->table($groupsUsersTable)->where('user_id', $id)->delete();
        $db->table($loginsTable)->where('user_id', $id)->delete();
        $db->table($tokenLoginsTable)->where('user_id', $id)->delete();
        $db->table($identitiesTable)->where('user_id', $id)->delete();
        $db->table($usersTable)->where('id', $id)->where('role', $role)->delete();
        $deletedUsers = $db->affectedRows();
        $db->transComplete();

        if (! $db->transStatus() || $deletedUsers < 1) {
            $error = $db->error();
            log_message('error', 'Falha ao excluir usuario #{id}: {msg}', [
                'id' => $id,
                'msg' => $error['message'] ?? 'erro desconhecido',
            ]);

            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Nao foi possivel excluir o usuario.',
                'csrf' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'message' => 'Usuario excluido.',
            'csrf' => csrf_hash(),
        ]);
    }

    public function sendUserMessage()
    {
        $id = (int) $this->request->getPost('id');
        $role = (string) $this->request->getPost('role');
        $message = trim((string) $this->request->getPost('message'));

        if ($id <= 0 || $message === '' || ! in_array($role, ['student', 'instructor'], true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Dados invalidos.',
                'csrf' => csrf_hash(),
            ]);
        }

        $db = db_connect();
        $row = $db->table('users u')
            ->select('u.id, u.username, ai.secret as email, u.role')
            ->join('auth_identities ai', 'ai.user_id = u.id AND ai.type = "email_password"', 'left')
            ->where('u.id', $id)
            ->get()
            ->getRow();

        if (! $row || $row->role !== $role || empty($row->email)) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Usuario nao encontrado.',
                'csrf' => csrf_hash(),
            ]);
        }

        $mail = Services::email();
        $mail->setTo($row->email);
        $mail->setSubject('Mensagem do administrador');
        $mail->setMessage(nl2br(esc($message)));
        $mail->send();

        return $this->response->setJSON([
            'message' => 'Mensagem enviada.',
            'csrf' => csrf_hash(),
        ]);
    }

    public function createUser()
    {
        $role = (string) $this->request->getPost('role');
        $email = trim((string) $this->request->getPost('email'));
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $confirm = (string) $this->request->getPost('password_confirm');
        $imageFile = $this->request->getFile('image_file');

        if (! in_array($role, ['student', 'instructor'], true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Role invalido.',
                'csrf' => csrf_hash(),
            ]);
        }

        $rules = [
            'email'            => 'required|valid_email',
            'username'         => 'required|min_length[3]|max_length[30]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'message' => implode(', ', $this->validator->getErrors()),
                'csrf' => csrf_hash(),
            ]);
        }

        $db = db_connect();
        $tables = config('Auth')->tables;
        $usersTable = $tables['users'] ?? 'users';
        $identityTable = $tables['identities'] ?? 'auth_identities';
        $optionalColumns = $this->usersOptionalColumns($db, $usersTable);
        $hasImg = in_array('img', $optionalColumns, true);

        if ($imageFile && $imageFile->isValid() && $imageFile->getError() === UPLOAD_ERR_OK) {
            if (! $hasImg) {
                return $this->response->setStatusCode(422)->setJSON([
                    'message' => 'A base de dados nao suporta campo de imagem para usuarios.',
                    'csrf' => csrf_hash(),
                ]);
            }

            $maxBytes = 4 * 1024 * 1024;
            if ($imageFile->getSize() > $maxBytes) {
                return $this->response->setStatusCode(422)->setJSON([
                    'message' => 'A imagem deve ter no maximo 4MB.',
                    'csrf' => csrf_hash(),
                ]);
            }

            $allowedMime = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
            if (! in_array(strtolower((string) $imageFile->getMimeType()), $allowedMime, true)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'message' => 'Formato de imagem invalido.',
                    'csrf' => csrf_hash(),
                ]);
            }
        }

        $users = new UserModel();
        $existingEmail = $db
            ->table($identityTable)
            ->where('type', 'email_password')
            ->where('secret', $email)
            ->get()
            ->getRow();

        if ($existingEmail) {
            return $this->response->setStatusCode(409)->setJSON([
                'message' => 'Email ja registrado.',
                'csrf' => csrf_hash(),
            ]);
        }

        $user = new User([
            'username' => $username,
            'active' => 1,
        ]);
        $user->email = $email;
        $user->password = $password;

        $uploadedImagePath = null;
        try {
            if (! $users->save($user)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'message' => implode(', ', $users->errors() ?: ['Nao foi possivel criar o usuario.']),
                    'csrf' => csrf_hash(),
                ]);
            }

            $userId = (int) $users->getInsertID();

            if ($hasImg && $imageFile && $imageFile->isValid() && $imageFile->getError() === UPLOAD_ERR_OK) {
                $targetDir = FCPATH . 'assets/img/';
                if (! is_dir($targetDir) && ! @mkdir($targetDir, 0755, true)) {
                    throw new \RuntimeException('Nao foi possivel preparar a pasta de imagem.');
                }

                $newName = $imageFile->getRandomName();
                if (! $imageFile->move($targetDir, $newName)) {
                    throw new \RuntimeException('Falha ao guardar a imagem enviada.');
                }

                $uploadedImagePath = 'assets/img/' . $newName;
                $db->table($usersTable)
                    ->where('id', $userId)
                    ->update([
                        'img' => $uploadedImagePath,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $created = $users->find($userId);
            if (! $created) {
                return $this->response->setStatusCode(500)->setJSON([
                    'message' => 'Nao foi possivel carregar o usuario criado.',
                    'csrf' => csrf_hash(),
                ]);
            }

            $users->addToDefaultGroup($created);

            // Trigger existing register hook to set role and related records.
            Events::trigger('register', $created);
        } catch (\Throwable $e) {
            if ($uploadedImagePath) {
                $fullPath = FCPATH . ltrim($uploadedImagePath, '/\\');
                if (is_file($fullPath)) {
                    @unlink($fullPath);
                }
            }
            log_message('error', 'Falha ao criar usuario no admin: {error}', ['error' => $e->getMessage()]);

            $message = 'Nao foi possivel criar o usuario.';
            $statusCode = 500;
            if (stripos($e->getMessage(), 'users.username') !== false || stripos($e->getMessage(), 'Duplicate entry') !== false) {
                $message = 'A base de dados ainda esta bloqueando nomes repetidos. Execute as migracoes.';
                $statusCode = 409;
            }

            return $this->response->setStatusCode($statusCode)->setJSON([
                'message' => $message,
                'csrf' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'message' => 'Usuario criado com sucesso.',
            'csrf' => csrf_hash(),
        ]);
    }
}
