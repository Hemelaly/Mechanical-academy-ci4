<?php

namespace App\Controllers\Instructor;

use App\Controllers\BaseController;
use App\Models\CertificateModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CourseModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;
use App\Models\CourseSettingModel;
use App\Models\ExtendedUserModel;
use App\Models\JitsiModel;
use App\Models\PendingUserModel;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Authentication\Passwords;
use CodeIgniter\Shield\Models\PasswordResetModel;

class Dashboard extends BaseController
{
    private function wantsJson(): bool
    {
        return $this->request->isAJAX()
            || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    private function jsonMessage(string $message, int $statusCode = 200, array $extra = [])
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON(array_merge([
                'message' => $message,
                'csrf' => csrf_hash(),
            ], $extra));
    }

    private function sidebarLinks()
    {
        return [
            ['label' => 'InÃ­cio', 'icon' => 'bi-house-door', 'url' => '/instructor/dashboard'],
            ['label' => 'Meus Cursos', 'icon' => 'bi-book', 'url' => '/instructor/dashboard/meus_cursos'],
            ['label' => 'Aula ao Vivo', 'icon' => 'bi-camera-reels', 'url' => '/instructor/dashboard/jitsi'],
            ['label' => 'Estudantes', 'icon' => 'bi-people', 'url' => '/instructor/dashboard/meus_estudantes'],
            ['label' => 'FinanÃ§as', 'icon' => 'bi-cash-coin', 'url' => '/instructor/dashboard/financas'],
            ['label' => 'Certificados', 'icon' => 'bi-folder', 'url' => '/instructor/dashboard/certificados'],
            ['label' => 'Perfil', 'icon' => 'bi-person-circle', 'url' => '/instructor/dashboard/perfil'],
        ];
    }

    public function index()
    {
        $user = service('auth')->user();

        return view('pages/instructor/home', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function my_courses()
    {
        $courseModel = new CourseModel();
        $user = service('auth')->user();
        $courses = $courseModel->getCoursesByInstructor($user->id);

        return view('pages/instructor/my_courses', [
            'user' => $user,
            'courses' => $courses,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function add_course()
    {
        $user = service('auth')->user();
        $courseModel = new CourseModel();
        $moduleModel = new ModuleModel();
        $lessonModel = new LessonModel();
        $projectModel = new \App\Models\ProjectModel();

        $savedDraft = $courseModel
            ->where('id_instructor_course', $user->id)
            ->where('status_course', 'Rascunho')
            ->orderBy('updated_at', 'DESC')
            ->first();
        $loadDraft = $this->request->getGet('load_draft') === '1';
        $draft = $loadDraft ? $savedDraft : null;

        $draftModules = [];
        $draftProjects = [];
        if ($draft) {
            $draftModules = $moduleModel
                ->where('id_course_module', $draft->id_course)
                ->orderBy('position_module')
                ->findAll();

            foreach ($draftModules as &$m) {
                $m->lessons = $lessonModel
                    ->where('id_module_lesson', $m->id_module)
                    ->orderBy('position_lesson')
                    ->findAll();
            }
            unset($m);

            $draftProjects = $projectModel
                ->where('id_course_project', $draft->id_course)
                ->findAll();
        }

        return view('pages/instructor/add_course', [
            'user' => $user,
            'draft' => $draft,
            'savedDraft' => $savedDraft,
            'draftModules' => $draftModules,
            'draftProjects' => $draftProjects,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function edit_course($id)
    {
        $user = service('auth')->user();

        $courseModel = new \App\Models\CourseModel();
        $moduleModel = new \App\Models\ModuleModel();
        $lessonModel = new \App\Models\LessonModel();
        $projectModel = new \App\Models\ProjectModel();

        if (!$id) {
            return redirect()->to('instructor/dashboard/meus_cursos')
                ->with('error', 'ID do curso nÃƒÂ£o fornecido');
        }

        $course = $courseModel->find($id);

        if (!$course) {
            return redirect()->to('instructor/dashboard/meus_cursos')
                ->with('error', 'Curso nÃƒÂ£o encontrado');
        }

        if ($course->id_instructor_course != auth()->id()) {
            return redirect()->to('instructor/dashboard/meus_cursos')
                ->with('error', 'Acesso negado');
        }

        // Ã°Å¸â€Â¹ Carregar mÃƒÂ³dulos e aulas
        $modules = $moduleModel
            ->where('id_course_module', $id)
            ->orderBy('position_module')
            ->findAll();

        foreach ($modules as &$m) {
            $m->lessons = $lessonModel
                ->where('id_module_lesson', $m->id_module)
                ->orderBy('position_lesson')
                ->findAll();
        }

        $projects = $projectModel
            ->where('id_course_project', $id)
            ->findAll();

        $db = db_connect();
        $enrolledCount = (int) $db->table('enrollments')
            ->where('id_course_enrollment', (int) $id)
            ->where('status_enrollment', 'ativa')
            ->countAllResults();

        // Ã¢Å“â€¦ APENAS retorna a view
        return view('pages/instructor/edit_course', [
            'user'         => $user,
            'course'       => $course,
            'modules'      => $modules,
            'projects'     => $projects,
            'enrolledCount' => $enrolledCount,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl'   => current_url()
        ]);
    }

    public function live($id = null)
    {
        $user  = service('auth')->user();
        $req   = $this->request;
        $model = new JitsiModel();
        $courseModel = new CourseModel();

        // ============================================================
        // 1) GET Ã¢â€ â€™ LISTAGEM + VIEW
        // ============================================================
        if ($req->getMethod() === 'GET') {

            $aulas = $model->where('id_user_jitsi', $user->id)
                ->orderBy('id_jitsi', 'DESC')
                ->findAll();

            $courses = $courseModel->getCoursesByInstructor($user->id);

            return view('pages/instructor/live_class', [
                'user'         => $user,
                'sidebarLinks' => $this->sidebarLinks(),
                'currentUrl'   => current_url(),
                'aulas'        => $aulas,
                'courses'      => $courses
            ]);
        }

        // ============================================================
        // 2) POST Ã¢â€ â€™ definir se ÃƒÂ© CRIAR ou EDITAR
        // ============================================================
        $isEdit = !empty($req->getPost('id_jitsi'));
        $editId = $req->getPost('id_jitsi');

        // ============================================================
        // 3) REGRAS DE VALIDAÃƒâ€¡ÃƒÆ’O
        // ============================================================
        $rules = [
            'classTitle' => [
                'label'  => 'TÃƒÂ­tulo da Aula',
                'rules'  => 'required|min_length[3]|max_length[255]',
            ],
            'classDescription' => [
                'label'  => 'DescriÃƒÂ§ÃƒÂ£o',
                'rules'  => 'permit_empty|max_length[65535]',
            ],
            'associatedCourse' => [
                'label'  => 'Curso Associado',
                'rules'  => 'permit_empty|integer',
            ],
            'classType' => [
                'label' => 'Tipo de Aula',
                'rules' => 'required|in_list[instant,scheduled]',
            ],
            'roomStatus' => [
                'label' => 'Estado',
                'rules' => 'required|in_list[Pendente,Ao vivo,Expirado]',
            ],
            'roomPrivacy' => [
                'label' => 'Privacidade',
                'rules' => 'required|in_list[public,private,password]',
            ],
            'roomPassword' => [
                'label' => 'Senha da Sala',
                'rules' => 'permit_empty|min_length[4]|max_length[100]',
            ],
        ];

        // Se for AULA AGENDADA Ã¢â€ â€™ obriga data e horÃƒÂ¡rios
        if ($req->getPost('classType') === 'scheduled') {

            $rules['classDate'] = [
                'label' => 'Data da Aula',
                'rules' => 'required|valid_date[Y-m-d]',
            ];

            $rules['startTime'] = [
                'label' => 'Hora de InÃƒÂ­cio',
                'rules' => 'required',
            ];

            $rules['endTime'] = [
                'label' => 'Hora de TÃƒÂ©rmino',
                'rules' => 'required',
            ];
        }

        // Se for PRIVACIDADE COM SENHA Ã¢â€ â€™ obriga senha
        if ($req->getPost('roomPrivacy') === 'password') {
            $rules['roomPassword']['rules'] = 'required|min_length[4]|max_length[100]';
        }

        // ============================================================
        // 4) VALIDAR
        // ============================================================
        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // ============================================================
        // 5) PREPARAR OS DADOS PARA SALVAR
        // ============================================================
        $data = [
            'title_jitsi'        => $req->getPost('classTitle'),
            'description_jitsi'  => $req->getPost('classDescription'),
            'id_course_jitsi'    => $req->getPost('associatedCourse') ?: null,
            'class_type_jitsi'   => $req->getPost('classType'),
            'meeting_date_jitsi' => $req->getPost('classDate') ?: null,
            'start_time_jitsi'   => $req->getPost('startTime') ?: null,
            'end_time_jitsi'     => $req->getPost('endTime') ?: null,
            'status_jitsi'       => $req->getPost('roomStatus'),
            'privacy_jitsi'      => $req->getPost('roomPrivacy'),
            'password_jitsi'     => $req->getPost('roomPassword') ?: null,
            'recording_jitsi'    => $req->getPost('enableRecording') ? 1 : 0,
            'chat_jitsi'         => $req->getPost('enableChat') ? 1 : 0,
            'screenshare_jitsi'  => $req->getPost('enableScreenShare') ? 1 : 0,
            'id_user_jitsi'      => $user->id,
        ];

        // Novo room_jitsi apenas ao criar
        if (!$isEdit) {
            $data['room_jitsi'] = "room_" . uniqid();
        }

        // ============================================================
        // 6) SALVAR (CRIAR OU EDITAR)
        // ============================================================
        if ($isEdit) {
            // Atualizar
            $model->update($editId, $data);

            return redirect()
                ->to('/instructor/dashboard/jitsi')
                ->with('swal', [
                    'icon'  => 'success',
                    'title' => 'Aula atualizada!',
                    'text'  => 'A aula foi editada com sucesso.'
                ]);
        } else {
            // Criar
            $model->insert($data);

            return redirect()
                ->to('/instructor/dashboard/jitsi')
                ->with('swal', [
                    'icon'  => 'success',
                    'title' => 'Aula criada!',
                    'text'  => 'A nova aula virtual foi criada com sucesso.'
                ]);
        }
    }

    public function deleteJitsi($id)
    {
        $user  = service('auth')->user();
        $model = new JitsiModel();

        // Verifica se a aula existe
        $aula = $model->find($id);

        if (!$aula) {
            return redirect()->back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Aula nÃƒÂ£o encontrada',
                'text'  => 'A aula que tentou excluir nÃƒÂ£o existe.'
            ]);
        }

        // Impede que um instrutor exclua aula de outro instrutor
        if ($aula->id_user_jitsi != $user->id) {
            return redirect()->back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Acesso negado',
                'text'  => 'VocÃƒÂª nÃƒÂ£o tem permissÃƒÂ£o para excluir esta aula.'
            ]);
        }

        // Delete
        if ($model->delete($id)) {
            return redirect()
                ->to('/instructor/dashboard/jitsi')
                ->with('swal', [
                    'icon'  => 'success',
                    'title' => 'Aula excluÃƒÂ­da!',
                    'text'  => 'A aula foi removida com sucesso.'
                ]);
        }

        // Caso ocorra algum erro inesperado
        return redirect()
            ->back()
            ->with('swal', [
                'icon'  => 'error',
                'title' => 'Erro ao excluir',
                'text'  => 'NÃƒÂ£o foi possÃƒÂ­vel excluir esta aula.'
            ]);
    }

    public function stream($id)
    {
        $user  = service('auth')->user();
        $model = new JitsiModel();

        $aula = $model->find($id);

        if (!$aula) {
            return redirect()->back()->with('swal', [
                'icon' => 'error',
                'title' => 'Aula nÃƒÂ£o encontrada',
                'text' => 'A aula que tentou acessar nÃƒÂ£o existe.'
            ]);
        }

        // Permitir somente instrutor dono + alunos inscritos (se quiser implementar depois)
        if ($aula->id_user_jitsi != $user->id) {
            return redirect()->back()->with('swal', [
                'icon' => 'error',
                'title' => 'Sem PermissÃƒÂ£o',
                'text' => 'VocÃƒÂª nÃƒÂ£o pode acessar esta sala.'
            ]);
        }

        return view('pages/instructor/live_stream', [
            'aula' => $aula,
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function students()
    {
        $user = service('auth')->user();

        $enrollmentModel = new \App\Models\EnrollmentModel();

        $enrollments = $enrollmentModel
            ->getInstructorEnrollments($user->id)
            ->paginate(5, 'enrollments');

        $paymentModel = new \App\Models\PaymentModel();
        $payments = $paymentModel->getInstructorPendingPayments($user->id);

        return view('pages/instructor/students', [
            'user' => $user,
            'payments' => $payments,
            'enrollments' => $enrollments,
            'pager' => $enrollmentModel->pager,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
        ]);
    }

    public function studentsData()
    {
        $user = service('auth')->user();
        $search = trim((string) $this->request->getGet('q'));
        $status = strtolower((string) $this->request->getGet('status'));
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = (int) $this->request->getGet('per_page');
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min(max($perPage, 5), 50);
        $offset = ($page - 1) * $perPage;

        $db = db_connect();
        $builder = $db->table('enrollments e')
            ->select([
                'e.id_enrollment',
                'e.status_enrollment',
                'e.progress_enrollment',
                'e.updated_at AS last_enrollment_update',
                's.name_student',
                's.email_student',
                'c.title_course',
            ])
            ->select('(SELECT MAX(COALESCE(p.updated_at, p.created_at, p.completed_at_progress)) FROM progress p WHERE p.id_enrollment_progress = e.id_enrollment) AS last_activity', false)
            ->join('courses c', 'c.id_course = e.id_course_enrollment')
            ->join('students s', 's.id_user_student = e.id_student_enrollment')
            ->where('c.id_instructor_course', $user->id);

        if ($search !== '') {
            $builder->groupStart()
                ->like('s.name_student', $search)
                ->orLike('s.email_student', $search)
                ->orLike('c.title_course', $search)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('e.status_enrollment', $status);
        }

        $countBuilder = clone $builder;
        $total = (int) $countBuilder->countAllResults();

        $rows = $builder
            ->orderBy('e.updated_at', 'DESC')
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

    public function pendingPaymentsData()
    {
        $user = service('auth')->user();
        $search = trim((string) $this->request->getGet('q'));
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = (int) $this->request->getGet('per_page');
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min(max($perPage, 5), 50);
        $offset = ($page - 1) * $perPage;

        $db = db_connect();
        $builder = $db->table('payments p')
            ->select([
                'p.id_payment',
                'p.status_payment',
                'p.proof_file_payment',
                'p.created_at',
                'pu.id AS id_user_payment',
                'pu.username',
                'pu.email',
                'c.id_course',
                'c.title_course',
            ])
            ->join('pending_users pu', 'pu.id = p.id_user_payment')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Pendente');

        if ($search !== '') {
            $builder->groupStart()
                ->like('pu.username', $search)
                ->orLike('pu.email', $search)
                ->orLike('c.title_course', $search)
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $total = (int) $countBuilder->countAllResults();

        $rows = $builder
            ->orderBy('p.created_at', 'DESC')
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

    public function toggleEnrollment($enrollmentId)
    {
        $user = service('auth')->user();
        $enrollmentId = (int) $enrollmentId;

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $row = $enrollmentModel
            ->select('enrollments.id_enrollment, enrollments.status_enrollment, courses.id_instructor_course')
            ->join('courses', 'courses.id_course = enrollments.id_course_enrollment')
            ->where('enrollments.id_enrollment', $enrollmentId)
            ->get()
            ->getRow();

        if (! $row || (int) $row->id_instructor_course !== (int) $user->id) {
            if ($this->wantsJson()) {
                return $this->jsonMessage('Acesso negado.', 403);
            }
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $currentStatus = strtolower((string) $row->status_enrollment);
        $newStatus = $currentStatus === 'ativa' ? 'cancelada' : 'ativa';
        $enrollmentModel->update($enrollmentId, ['status_enrollment' => $newStatus]);

        $msg = $newStatus === 'ativa'
            ? 'Acesso do aluno liberado.'
            : 'Acesso do aluno bloqueado.';

        if ($this->wantsJson()) {
            return $this->jsonMessage($msg);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function financial()
    {
        $user = service('auth')->user();

        return view('pages/instructor/financial', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function financialData()
    {
        $user = service('auth')->user();
        $year = (int) $this->request->getGet('year');
        if ($year <= 0) {
            $year = (int) date('Y');
        }

        $db = db_connect();
        $rows = $db->table('payments p')
            ->select('MONTH(p.created_at) as month, SUM(p.amount_payment) as total')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('p.status_payment', 'Aprovado')
            ->where('c.id_instructor_course', $user->id)
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
        $users = new ExtendedUserModel();
        $userModel = new UserModel();
        $courseModel = new CourseModel();

        $user = auth()->user();

        if (! $user) {
            return redirect()->to(site_url('login'))
                ->with('error', 'SessÃƒÂ£o expirada. FaÃƒÂ§a login novamente.');
        }

        $profileUrl = current_url(false);
        $db = db_connect();
        $courseCount = (int) $courseModel
            ->where('id_instructor_course', $user->id)
            ->countAllResults();
        $avgProgressRow = $db->table('enrollments e')
            ->selectAvg('e.progress_enrollment', 'avg_progress')
            ->join('courses c', 'c.id_course = e.id_course_enrollment')
            ->where('c.id_instructor_course', $user->id)
            ->where('e.status_enrollment', 'ativa')
            ->get()
            ->getRow();
        $avgProgress = (int) round($avgProgressRow->avg_progress ?? 0);

        if ($this->request->getMethod() !== 'POST') {
            return view('pages/instructor/profile', [
                'user'         => $user,
                'courseCount'  => $courseCount,
                'avgProgress'  => $avgProgress,
                'sidebarLinks' => $this->sidebarLinks(),
                'currentUrl'   => $profileUrl,
            ]);
        }

        // ValidaÃƒÂ§ÃƒÂ£o
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

        // Se o nome foi enviado, verificar duplicidade
        $userName = trim($post['nome'] ?? '');
        $email    = trim($post['email'] ?? '');

        if (! empty($userName)) {
            $existingUser = $userModel
                ->where('username', $userName)
                ->where('id !=', $user->id)   // ignora o prÃƒÂ³prio usuÃƒÂ¡rio
                ->first();

            if ($existingUser) {
                return redirect()->to($profileUrl)->with('error', 'JÃƒÂ¡ existe um usuÃƒÂ¡rio com esse nome!');
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
                return redirect()->to($profileUrl)->with('error', 'Ja existe um usuario com esse email!');
            }
        }

        // Upload da imagem
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

            // Apagar antiga
            if (! empty($user->img)) {
                $old = FCPATH . $user->img;
                if (is_file($old)) {
                    @unlink($old);
                }
            }

            $filePath = 'assets/img/' . $newName;
        }

        // Dados para atualizar
        $dataProfile = [
            'username' => $userName ?: $user->username,
            'img'      => $filePath,
            'country'  => $post['pais']      ?? $user->country,
            'province' => $post['provincia'] ?? $user->province,
            'city'     => $post['cidade']    ?? $user->city,
            'phone'    => $post['telefone']  ?? $user->phone,
        ];

        // ---------------------------------------
        //    >>> ALTERACAO DE SENHA (SHIELD) <<<
        // ---------------------------------------
        $currentPassword = $post['password_actual'] ?? '';
        $newPassword     = $post['new_password'] ?? '';
        $confirmPassword = $post['confirm_password'] ?? '';
        $emailChanged    = ! empty($email) && $email !== ($user->email ?? '');

        if ($emailChanged) {
            $identity = db_connect()
                ->table('auth_identities')
                ->where('user_id', $user->id)
                ->where('type', 'email_password')
                ->get()
                ->getRow();

            if (! $identity) {
                return redirect()->to($profileUrl)->with('error', 'Identidade de senha nao encontrada.');
            }

            db_connect()
                ->table('auth_identities')
                ->where('id', $identity->id)
                ->update([
                    'secret'     => $email,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        if ($currentPassword || $newPassword || $confirmPassword) {
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                return redirect()->to($profileUrl)->with('error', 'Para alterar a senha, preencha todos os campos.');
            }

            $identity = db_connect()
                ->table('auth_identities')
                ->where('user_id', $user->id)
                ->where('type', 'email_password')
                ->get()
                ->getRow();

            if (! $identity) {
                return redirect()->to($profileUrl)->with('error', 'Identidade de senha nao encontrada.');
            }

            if (! password_verify($currentPassword, $identity->secret2)) {
                return redirect()->to($profileUrl)->with('error', 'A senha atual fornecida esta incorreta.');
            }

            if (strlen($newPassword) < 6) {
                return redirect()->to($profileUrl)->with('error', 'A nova senha deve ter no minimo 6 caracteres.');
            }

            if ($newPassword !== $confirmPassword) {
                return redirect()->to($profileUrl)->with('error', 'A confirmacao da senha nao coincide.');
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

        // Atualiza usuÃƒÂ¡rio logado
        $updated = $users->find($user->id);
        auth()->setUser($updated);

        return redirect()->to($profileUrl)->with('success', 'Perfil atualizado com sucesso.');
    }

    public function approveEnrollment($courseId, $pendingId)
    {
        $actualUser = service('auth')->user();

        $db = db_connect();
        helper('text');

        $studentModel     = new \App\Models\StudentModel();
        $enrollmentModel  = new \App\Models\EnrollmentModel();
        $paymentModel     = new \App\Models\PaymentModel();
        $pendingUserModel = new \App\Models\PendingUserModel();
        $users            = new UserModel();

        $isReject = $this->request->getPost('status_payment') === 'Rejeitado';
        if ($isReject) {
            $paymentModel
                ->where('id_user_payment', $pendingId)
                ->where('id_course_payment', $courseId)
                ->set([
                    'status_payment' => 'Rejeitado',
                    'approved_by_payment' => $actualUser->id,
                ])
                ->update();

            if ($this->wantsJson()) {
                return $this->jsonMessage('Pagamento rejeitado.');
            }

            return redirect()->back()->with('success', 'Pagamento rejeitado.');
        }

        // 1. Buscar dados do pending_user
        $pendingUser = $pendingUserModel->find($pendingId);
        if (!$pendingUser) {
            if ($this->wantsJson()) {
                return $this->jsonMessage('Usuario pendente nao encontrado.', 404);
            }
            return redirect()->back()->with('error', 'UsuÃƒÂ¡rio pendente nÃƒÂ£o encontrado.');
        }

        // 2. Verificar se jÃƒÂ¡ existe um user real com este email
        $existingUser = $users->findByCredentials(['email' => $pendingUser->email]);

        if ($existingUser !== null) {
            // UsuÃƒÂ¡rio jÃƒÂ¡ existe Ã¢â€ â€™ apenas inscrever no curso

            // Buscar o estudante vinculado a este user
            $student = $studentModel->where('id_user_student', $existingUser->id)->first();

            if (!$student) {
                // Se ainda nÃƒÂ£o existe estudante, cria
                $studentId = $studentModel->insert([
                    'id_user_student' => $existingUser->id,
                    'name_student'    => $existingUser->username,
                    'email_student'   => $existingUser->email,
                ]);
            } else {
                $studentId = $student->id_student;
            }

            // Verifica se jÃƒÂ¡ estÃƒÂ¡ inscrito no curso
            $alreadyEnrolled = $enrollmentModel
                ->where('id_student_enrollment', $studentId)
                ->where('id_course_enrollment', $courseId)
                ->first();

            if ($alreadyEnrolled) {
                if ($this->wantsJson()) {
                    return $this->jsonMessage('Usuario ja inscrito neste curso.', 409);
                }
                return redirect()->back()->with('error', 'UsuÃƒÂ¡rio jÃƒÂ¡ estÃƒÂ¡ inscrito neste curso.');
            }

            // Criar nova inscriÃƒÂ§ÃƒÂ£o
            $enrollmentModel->insert([
                'id_course_enrollment'   => $courseId,
                'id_student_enrollment'  => $existingUser->id,
                'status_enrollment'      => 'ativa',
                'progress_enrollment'    => 0.00,
                'enrolled_at_enrollment' => date('Y-m-d H:i:s'),
            ]);

            // Atualizar pagamento
            $paymentModel
                ->where('id_user_payment', $pendingId)
                ->set([
                    'id_user_payment'     => $existingUser->id,
                    'status_payment'      => 'Aprovado',
                    'approved_by_payment' => $actualUser->id,
                ])
                ->update();

            // Remover pending_user
            $pendingUserModel->delete($pendingId);


            if ($this->wantsJson()) {
                return $this->jsonMessage('Inscricao aprovada para usuario existente.');
            }

            return redirect()->back()->with('success', 'InscriÃƒÂ§ÃƒÂ£o aprovada para usuÃƒÂ¡rio jÃƒÂ¡ existente!');
        }

        // 3. Se o usuÃƒÂ¡rio ainda nÃƒÂ£o existe Ã¢â€ â€™ criar um novo
        $user = new User([
            'username' => $pendingUser->username,
        ]);


        $user->email = $pendingUser->email;
        $tempPassword = random_string('alnum', 12);
        $user->password = $tempPassword;
        $users->save($user);

        $userId = $users->getInsertID();
        $user   = $users->find($userId);

        // 3.1 Criar token de reset de senha
        $token   = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', strtotime('+1 day'));

        $db->table('password_resets')->insert([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => $expires
        ]);

        $link = site_url("reset-password?token={$token}");

        // Enviar email
        $email = \Config\Services::email();
        $email->setTo($user->email);
        $email->setSubject('Crie sua senha e acesse o curso');
        $email->setMessage("
        OlÃƒÂ¡ {$user->username},<br><br>
        Sua matrÃƒÂ­cula foi aprovada! Clique no link abaixo para criar sua senha e acessar o curso:<br><br>
        <a href='{$link}'>Criar minha senha</a>
        ");
        $email->send();

        // 4. Criar estudante vinculado
        $studentId = $studentModel->insert([
            'id_user_student' => $userId,
            'name_student'    => $pendingUser->username,
            'email_student'   => $pendingUser->email,
        ]);

        // 5. Criar inscriÃƒÂ§ÃƒÂ£o
        $result = $enrollmentModel->insert([
            'id_course_enrollment'   => $courseId,
            'id_student_enrollment'  => $userId,
            'status_enrollment'      => 'ativa',
            'progress_enrollment'    => 0.00,
            'enrolled_at_enrollment' => date('Y-m-d H:i:s'),
        ]);

        // 6. Atualizar pagamento
        $paymentModel
            ->where('id_user_payment', $pendingId)
            ->set([
                'id_user_payment'     => $userId,
                'status_payment'      => 'Aprovado',
                'approved_by_payment' => $actualUser->id,
            ])
            ->update();

        // 7. Remover pending_user
        $pendingUserModel->delete($pendingId);

        if ($this->wantsJson()) {
            return $this->jsonMessage('Inscricao aprovada e usuario criado com sucesso.');
        }

        return redirect()->back()->with('success', 'InscriÃƒÂ§ÃƒÂ£o aprovada e usuÃƒÂ¡rio criado com sucesso!');
    }
    public function certificate()
    {
        $courseModel = new CourseModel();
        // $course = $courseModel->find($idCourse);
        $certificateModel = new CertificateModel();

        $user = service('auth')->user();

        $certificates = $certificateModel->getForInstructorDashboard($user->id);

        return view('pages/instructor/certificates', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
            'certificates' => $certificates
        ]);
    }
}






