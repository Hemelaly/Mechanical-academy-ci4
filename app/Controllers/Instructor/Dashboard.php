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

    public function students()
    {
        $user = service('auth')->user();

        return view('pages/instructor/students', [
            'user' => $user,
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

}
