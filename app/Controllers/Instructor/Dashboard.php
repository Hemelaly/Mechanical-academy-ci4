<?php

namespace App\Controllers\Instructor;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CourseModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;
use App\Models\CourseSettingModel;

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

        return view('pages/instructor/students', [
            'user' => $user,
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

    public function updateEnrollment($idEnrollment)
    {
        $request = service('request');
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $paymentModel = new \App\Models\PaymentModel();

        // Dados vindos do formulário
        $statusEnrollment = $request->getPost('status_enrollment');
        $statusPayment = $request->getPost('status_payment');

        // Atualizar inscrição (se foi aceito, muda para Ativo)
        if ($statusEnrollment) {
            $enrollmentModel->update($idEnrollment, [
                'status_enrollment' => $statusEnrollment,
            ]);
        }

        // Atualizar pagamento correspondente
        if ($statusPayment) {
            $payment = $paymentModel->where('id_enrollment_payment', $idEnrollment)->first();
            if ($payment) {
                $paymentModel->update($payment->id_payment, [
                    'status_payment' => $statusPayment,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Decisão aplicada com sucesso!');
    }
}
