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
        $lessonModel = new LessonModel();
        $moduleModel = new \App\Models\ModuleModel();
        $courseModel = new \App\Models\CourseModel();

        $idLesson = (int) $id;
        $lesson = $lessonModel->find($idLesson);

        if (!$lesson) {
            return redirect()->back()->with('error', 'Aula não encontrada');
        }

        $module = $moduleModel->find($lesson->id_module_lesson);
        $course = $courseModel->find($module->id_course_module);

        // pega todos os módulos e suas aulas do curso para a sidebar
        $modules = $moduleModel->where('id_course_module', $course->id_course)->findAll();
        foreach ($modules as &$m) {
            $m->lessons = $lessonModel->where('id_module_lesson', $m->id_module)->findAll();
        }

        $user = service('auth')->user();

        $allLessons = $lessonModel->where('id_module_lesson', $lesson->id_module_lesson)->orderBy('id_lesson')->findAll();
        $lessonKeys = array_column($allLessons, 'id_lesson');
        $currentIndex = array_search($lesson->id_lesson, $lessonKeys);

        $prevLesson = $lessonKeys[$currentIndex - 1] ?? null;
        $nextLesson = $lessonKeys[$currentIndex + 1] ?? null;

        return view('pages/student/lessons', [
            'course' => $course,
            'modules' => $modules,
            'lesson' => $lesson,
            'prevLesson' => $prevLesson,
            'nextLesson' => $nextLesson,
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
