<?php

namespace App\Controllers\Admin;

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

    public function instructors()
    {
        $user = service('auth')->user();

        return view('pages/admin/instructors', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
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

    public function profile()
    {
        $user = service('auth')->user();

        return view('pages/admin/profile', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }
}
