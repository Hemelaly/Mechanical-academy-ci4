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

        // CONTAGEM DE INSCRITOS POR CURSO (considerando status_enrollment = 'Ativa')
        $rows = $db->table('courses c')
            ->select('c.id_course, COUNT(DISTINCT e.id_student_enrollment) AS inscritos', false)
            ->join(
                'enrollments e',
                'e.id_course_enrollment = c.id_course AND e.status_enrollment = "Ativa"',
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
            ->where('status_enrollment', 'Ativa')
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

        return $db->table('users u')
            ->select('u.id, u.username, u.active, u.last_active, u.created_at, u.img, u.role, ai.secret as email')
            ->join('auth_identities ai', 'ai.user_id = u.id AND ai.type = "email_password"', 'left')
            ->where('u.role', $role)
            ->where('u.deleted_at', null);
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

        $users = new ExtendedUserModel();
        $user = $users->find($id);
        if (! $user || $user->role !== $role) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Usuario nao encontrado.',
                'csrf' => csrf_hash(),
            ]);
        }

        $users->delete($id);

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

        $users = new UserModel();

        $existing = $users->findByCredentials(['email' => $email]);
        if ($existing) {
            return $this->response->setStatusCode(409)->setJSON([
                'message' => 'Email ja registrado.',
                'csrf' => csrf_hash(),
            ]);
        }

        $existingUsername = $users->where('username', $username)->first();
        if ($existingUsername) {
            return $this->response->setStatusCode(409)->setJSON([
                'message' => 'Nome de usuario ja registrado.',
                'csrf' => csrf_hash(),
            ]);
        }

        $user = new User([
            'username' => $username,
        ]);
        $user->email = $email;
        $user->password = $password;

        if (! $users->save($user)) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Nao foi possivel criar o usuario.',
                'csrf' => csrf_hash(),
            ]);
        }

        $userId = (int) $users->getInsertID();
        $created = $users->find($userId);

        // Trigger existing register hook to set role and related records.
        Events::trigger('register', $created);

        return $this->response->setJSON([
            'message' => 'Usuario criado com sucesso.',
            'csrf' => csrf_hash(),
        ]);
    }
}
