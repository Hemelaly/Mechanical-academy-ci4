<?php

namespace App\Controllers\Instructor;

use App\Controllers\BaseController;
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
    private function sidebarLinks()
    {
        return [
            ['label' => 'Início', 'icon' => 'bi-house-door', 'url' => '/instructor/dashboard'],
            ['label' => 'Meus Cursos', 'icon' => 'bi-book', 'url' => '/instructor/dashboard/meus_cursos'],
            ['label' => 'Aula ao Vivo', 'icon' => 'bi-camera-reels', 'url' => '/instructor/dashboard/jitsi'],
            ['label' => 'Estudantes', 'icon' => 'bi-people', 'url' => '/instructor/dashboard/meus_estudantes'],
            ['label' => 'Finanças', 'icon' => 'bi-cash-coin', 'url' => '/instructor/dashboard/financas'],
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

        return view('pages/instructor/add_course', [
            'user' => $user,
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

        if (!$id) {
            return redirect()->back()->with('error', 'ID do curso não fornecido');
        }

        $course = $courseModel->find($id);
        if (!$course) {
            return redirect()->back()->with('error', 'Curso não encontrado');
        }

        if ($course->id_instructor_course != auth()->id()) {
            return redirect()->back()->with('error', 'Acesso negado');
        }

        // Carregar módulos e aulas do curso
        $modules = $moduleModel->where('id_course_module', $id)->orderBy('position_module')->findAll();
        foreach ($modules as &$m) {
            $m->lessons = $lessonModel
                ->where('id_module_lesson', $m->id_module)
                ->orderBy('position_lesson')
                ->findAll();
        }

        // Quando for POST → salvar
        if ($this->request->getMethod() === 'post') {
            $data = $this->request->getPost();

            // Processar módulos recebidos do form
            $data['modules'] = [];
            if ($this->request->getPost('modules')) {
                $modulesRaw = $this->request->getPost('modules');
                $data['modules'] = is_string($modulesRaw) ? json_decode($modulesRaw, true) : $modulesRaw;
            }

            // Atualizar curso
            $courseData = [
                'title_course'       => $data['title_course'] ?? $course->title_course,
                'subtitle_course'    => $data['subtitle_course'] ?? $course->subtitle_course,
                'description_course' => $data['description_course'] ?? $course->description_course,
                'price_course'       => ($data['courseType'] ?? 'free') === 'paid' ? ($data['price_course'] ?? 0) : 0,
                'status_course'      => $data['status_course'] ?? $course->status_course,
            ];

            if ($file = $this->request->getFile('image_course')) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(FCPATH . 'assets/instructor/img/courses', $newName);
                    $courseData['image_course'] = $newName;
                }
            }

            $courseModel->update($id, $courseData);

            // Atualizar módulos e aulas
            if (!empty($data['modules'])) {
                $oldModules = $moduleModel->where('id_course_module', $id)->findAll();
                foreach ($oldModules as $mod) {
                    $lessonModel->where('id_module_lesson', $mod->id_module)->delete();
                }
                $moduleModel->where('id_course_module', $id)->delete();

                foreach ($data['modules'] as $mIndex => $module) {
                    $moduleInsert = [
                        'id_course_module'   => $id,
                        'title_module'       => $module['title'] ?? 'Módulo ' . ($mIndex + 1),
                        'description_module' => $module['description'] ?? '',
                        'position_module'    => $mIndex + 1,
                    ];
                    $moduleModel->insert($moduleInsert);
                    $moduleId = $moduleModel->insertID();

                    if (!empty($module['lessons'])) {
                        foreach ($module['lessons'] as $lIndex => $lesson) {
                            $lessonInsert = [
                                'id_module_lesson' => $moduleId,
                                'title_lesson'     => $lesson['title'] ?? 'Aula sem título',
                                'type_lesson'      => $lesson['type'] ?? 'text',
                                'duration_lesson'  => $lesson['duration'] ?? 0,
                                'position_lesson'  => $lIndex + 1,
                                'video_url_lesson' => $lesson['video_url'] ?? null,
                            ];
                            $lessonModel->insert($lessonInsert);
                        }
                    }
                }
            }

            return redirect()->back()->with('success', 'Curso atualizado com sucesso!');
        }

        return view('pages/instructor/edit_course', [
            'user' => $user,
            'course'  => $course,
            'modules' => $modules,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function live($id = null)
    {
        $user  = service('auth')->user();
        $req   = $this->request;
        $model = new JitsiModel();
        $courseModel = new CourseModel();

        // ============================================================
        // 1) GET → LISTAGEM + VIEW
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
        // 2) POST → definir se é CRIAR ou EDITAR
        // ============================================================
        $isEdit = !empty($req->getPost('id_jitsi'));
        $editId = $req->getPost('id_jitsi');

        // ============================================================
        // 3) REGRAS DE VALIDAÇÃO
        // ============================================================
        $rules = [
            'classTitle' => [
                'label'  => 'Título da Aula',
                'rules'  => 'required|min_length[3]|max_length[255]',
            ],
            'classDescription' => [
                'label'  => 'Descrição',
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

        // Se for AULA AGENDADA → obriga data e horários
        if ($req->getPost('classType') === 'scheduled') {

            $rules['classDate'] = [
                'label' => 'Data da Aula',
                'rules' => 'required|valid_date[Y-m-d]',
            ];

            $rules['startTime'] = [
                'label' => 'Hora de Início',
                'rules' => 'required',
            ];

            $rules['endTime'] = [
                'label' => 'Hora de Término',
                'rules' => 'required',
            ];
        }

        // Se for PRIVACIDADE COM SENHA → obriga senha
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
                'title' => 'Aula não encontrada',
                'text'  => 'A aula que tentou excluir não existe.'
            ]);
        }

        // Impede que um instrutor exclua aula de outro instrutor
        if ($aula->id_user_jitsi != $user->id) {
            return redirect()->back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Acesso negado',
                'text'  => 'Você não tem permissão para excluir esta aula.'
            ]);
        }

        // Delete
        if ($model->delete($id)) {
            return redirect()
                ->to('/instructor/dashboard/jitsi')
                ->with('swal', [
                    'icon'  => 'success',
                    'title' => 'Aula excluída!',
                    'text'  => 'A aula foi removida com sucesso.'
                ]);
        }

        // Caso ocorra algum erro inesperado
        return redirect()
            ->back()
            ->with('swal', [
                'icon'  => 'error',
                'title' => 'Erro ao excluir',
                'text'  => 'Não foi possível excluir esta aula.'
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
                'title' => 'Aula não encontrada',
                'text' => 'A aula que tentou acessar não existe.'
            ]);
        }

        // Permitir somente instrutor dono + alunos inscritos (se quiser implementar depois)
        if ($aula->id_user_jitsi != $user->id) {
            return redirect()->back()->with('swal', [
                'icon' => 'error',
                'title' => 'Sem Permissão',
                'text' => 'Você não pode acessar esta sala.'
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

    public function financial()
    {
        $user = service('auth')->user();

        return view('pages/instructor/financial', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function profile()
    {
        $users = new ExtendedUserModel();
        $userModel = new UserModel();

        $user = auth()->user();

        if (! $user) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $profileUrl = current_url(false);

        if ($this->request->getMethod() !== 'POST') {
            return view('pages/student/profile', [
                'user'         => $user,
                'sidebarLinks' => $this->sidebarLinks(),
                'currentUrl'   => $profileUrl,
            ]);
        }

        // Validação
        $rules = [
            'nome'      => 'permit_empty|min_length[2]',
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

        if (! empty($userName)) {
            $existingUser = $userModel
                ->where('username', $userName)
                ->where('id !=', $user->id)   // ignora o próprio usuário
                ->first();

            if ($existingUser) {
                return redirect()->to($profileUrl)->with('error', 'Já existe um usuário com esse nome!');
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

        if (! $users->update($user->id, $dataProfile)) {
            return redirect()->to($profileUrl)
                ->with('error', implode(', ', $users->errors()));
        }

        // Atualiza usuário logado
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

        // 1. Buscar dados do pending_user
        $pendingUser = $pendingUserModel->find($pendingId);
        if (!$pendingUser) {
            return redirect()->back()->with('error', 'Usuário pendente não encontrado.');
        }

        // 2. Verificar se já existe um user real com este email
        $existingUser = $users->findByCredentials(['email' => $pendingUser->email]);

        if ($existingUser !== null) {
            // Usuário já existe → apenas inscrever no curso

            // Buscar o estudante vinculado a este user
            $student = $studentModel->where('id_user_student', $existingUser->id)->first();

            if (!$student) {
                // Se ainda não existe estudante, cria
                $studentId = $studentModel->insert([
                    'id_user_student' => $existingUser->id,
                    'name_student'    => $existingUser->username,
                    'email_student'   => $existingUser->email,
                ]);
            } else {
                $studentId = $student->id_student;
            }

            // Verifica se já está inscrito no curso
            $alreadyEnrolled = $enrollmentModel
                ->where('id_student_enrollment', $studentId)
                ->where('id_course_enrollment', $courseId)
                ->first();

            if ($alreadyEnrolled) {
                return redirect()->back()->with('error', 'Usuário já está inscrito neste curso.');
            }

            // Criar nova inscrição
            $enrollmentModel->insert([
                'id_course_enrollment'   => $courseId,
                'id_student_enrollment'  => $existingUser->id,
                'status_enrollment'      => 'Ativa',
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


            return redirect()->back()->with('success', 'Inscrição aprovada para usuário já existente!');
        }

        // 3. Se o usuário ainda não existe → criar um novo
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
        Olá {$user->username},<br><br>
        Sua matrícula foi aprovada! Clique no link abaixo para criar sua senha e acessar o curso:<br><br>
        <a href='{$link}'>Criar minha senha</a>
        ");
        $email->send();

        // 4. Criar estudante vinculado
        $studentId = $studentModel->insert([
            'id_user_student' => $userId,
            'name_student'    => $pendingUser->username,
            'email_student'   => $pendingUser->email,
        ]);

        // 5. Criar inscrição
        $result = $enrollmentModel->insert([
            'id_course_enrollment'   => $courseId,
            'id_student_enrollment'  => $userId,
            'status_enrollment'      => 'Ativa',
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

        return redirect()->back()->with('success', 'Inscrição aprovada e usuário criado com sucesso!');
    }
}
