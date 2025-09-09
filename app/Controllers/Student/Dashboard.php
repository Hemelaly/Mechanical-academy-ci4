<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\EnrollmentModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;
use App\Models\CourseSettingModel;

class Dashboard extends BaseController
{
    private function sidebarLinks()
    {
        return [
            ['label' => 'Início', 'icon' => 'bi-house-door', 'url' => '/student/dashboard'],
            ['label' => 'Meus Cursos', 'icon' => 'bi-book', 'url' => '/student/dashboard/meus_cursos'],
            ['label' => 'Todos Cursos', 'icon' => 'bi-book', 'url' => '/student/dashboard/cursos'],
            ['label' => 'User Profile', 'icon' => 'bi-person-circle', 'url' => '/student/dashboard/perfil'],
        ];
    }

    public function index()
    {
        $user = service('auth')->user();

        return view('pages/student/home', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function my_courses()
    {
        $enrollmentModel = new EnrollmentModel();
        $user = service('auth')->user();
        $enrollment = $enrollmentModel->getStudentEnrolledCourses($user->id);

        return view('pages/student/my_courses', [
            'user' => $user,
            'courses' => $enrollment,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function lessons($id)
    {
        $user = service('auth')->user();

        return view('pages/student/lessons', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function courses()
    {

        $coursesModel = new CourseModel();
        $Courses = $coursesModel->findAll();
        $user = service('auth')->user();

        return view('pages/student/courses', [
            'user' => $user,
            'courses' => $Courses,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function profile()
    {
        $user = service('auth')->user();

        return view('pages/student/profile', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

}
