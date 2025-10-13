<?php

namespace App\Controllers\Instructor;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CourseModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;
use App\Models\CourseSettingModel;
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
            ['label' => 'Estudantes', 'icon' => 'bi-people', 'url' => '/instructor/dashboard/meus_estudantes'],
            ['label' => 'Finanças', 'icon' => 'bi-cash-coin', 'url' => '/instructor/dashboard/financas'],
            ['label' => 'User Profile', 'icon' => 'bi-person-circle', 'url' => '/instructor/dashboard/perfil'],
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

    public function students()
    {
        $user = service('auth')->user();

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollment = $enrollmentModel->getInstructorEnrollments($user->id);

        $paymentModel = new \App\Models\PaymentModel();
        $payments = $paymentModel->getInstructorPendingPayments($user->id);

        return view('pages/instructor/students', [
            'user' => $user,
            'payments' => $payments,
            'enrollments' => $enrollment,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
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
        $user = service('auth')->user();

        return view('pages/instructor/profile', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
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
