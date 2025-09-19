<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;

class Dashboard extends BaseController
{
    private function sidebarLinks()
    {
        return [
            [
                'label' => 'Início',
                'icon' => 'bi-house-door',
                'url' => '/student/dashboard',
                'pattern' => '/student/dashboard' // Apenas correspondência exata
            ],
            [
                'label' => 'Meus Cursos',
                'icon' => 'bi-book',
                'url' => '/student/dashboard/meus_cursos',
                'pattern' => '/student/dashboard/meus_cursos*' // Com * para subpáginas
            ],
            [
                'label' => 'Todos Cursos',
                'icon' => 'bi-book',
                'url' => '/student/dashboard/cursos',
                'pattern' => '/student/dashboard/cursos*' // Com * para subpáginas
            ],
            [
                'label' => 'Perfil',
                'icon' => 'bi-person-circle',
                'url' => '/student/dashboard/perfil',
                'pattern' => '/student/dashboard/perfil*' // Com * para subpáginas
            ],
        ];
    }

    public function index()
    {
        $user = service('auth')->user();

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $coursesModel = new CourseModel();
        $courses = $coursesModel->findAll();
        $user = service('auth')->user();
        $enrollmentModel = new \App\Models\EnrollmentModel();

        // Pega todas as inscrições do usuário
        $enrollments = $enrollmentModel->where('id_student_enrollment', $user->id)
            ->findAll();

        // Cria um array com os IDs de cursos ativos
        $activeCourseIds = [];
        foreach ($enrollments as $enr) {
            if ($enr->status_enrollment === 'Ativo') {
                $activeCourseIds[] = $enr->id_course_enrollment;
            }
        }

        $pendingCourseIds = [];
        foreach ($enrollments as $enr) {
            if ($enr->status_enrollment === 'Pendente') {
                $pendingCourseIds[] = $enr->id_course_enrollment;
            }
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $lessonModel = new \App\Models\LessonModel();
        $user = service('auth')->user();

        // Pega os cursos nos quais o aluno está inscrito
        $lessons = $enrollmentModel->getStudentEnrolledCourses($user->id);

        // Para cada curso, pega a primeira aula (ordenada por posição do módulo e da aula)
        foreach ($lessons as &$lesson) {
            $firstLesson = $lessonModel
                ->select('lessons.id_lesson')
                ->join('modules', 'modules.id_module = lessons.id_module_lesson')
                ->where('modules.id_course_module', $lesson->id_course)
                ->orderBy('modules.position_module', 'ASC')
                ->orderBy('lessons.position_lesson', 'ASC')
                ->first();

            $lesson->firstLessonId = $firstLesson->id_lesson ?? null;
        }

        return view('pages/student/home', [
            'user' => $user,
            'courses' => $courses,
            'lesson' => $lesson,
            'activeCourseIds' => $activeCourseIds,
            'pendingCourseIds' => $pendingCourseIds,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function my_courses()
    {
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $lessonModel = new \App\Models\LessonModel();
        $user = service('auth')->user();

        // Pega os cursos nos quais o aluno está inscrito
        $courses = $enrollmentModel->getStudentEnrolledCourses($user->id);

        // Para cada curso, pega a primeira aula (ordenada por posição do módulo e da aula)
        foreach ($courses as &$course) {
            $firstLesson = $lessonModel
                ->select('lessons.id_lesson')
                ->join('modules', 'modules.id_module = lessons.id_module_lesson')
                ->where('modules.id_course_module', $course->id_course)
                ->orderBy('modules.position_module', 'ASC')
                ->orderBy('lessons.position_lesson', 'ASC')
                ->first();

            $course->firstLessonId = $firstLesson->id_lesson ?? null;
        }

        return view('pages/student/my_courses', [
            'user' => $user,
            'courses' => $courses,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }


    public function lessons($id)
    {
        $lessonModel = new LessonModel();
        $moduleModel = new ModuleModel();
        $courseModel = new CourseModel();

        $id = (int) $id;

        // Tenta encontrar a aula pelo ID
        $lesson = $lessonModel->find($id);

        // Se não existir, assume que é o ID do curso e pega a primeira aula
        if (!$lesson) {
            $firstLesson = $lessonModel
                ->join('modules', 'modules.id_module = lessons.id_module_lesson')
                ->where('modules.id_course_module', $id)
                ->orderBy('position_lesson', 'ASC')
                ->first();

            if (!$firstLesson) {
                return redirect()->back()->with('error', 'Nenhuma aula encontrada para este curso.');
            }

            return redirect()->to('/student/dashboard/ver_aulas/' . $firstLesson->id_lesson);
        }

        // Pega módulo e curso da aula
        $module = $moduleModel->find($lesson->id_module_lesson);
        $course = $courseModel->find($module->id_course_module);

        // Pega todos os módulos do curso e suas aulas
        $modules = $moduleModel->where('id_course_module', $course->id_course)->orderBy('position_module')->findAll();
        foreach ($modules as &$m) {
            $m->lessons = $lessonModel->where('id_module_lesson', $m->id_module)->orderBy('position_lesson')->findAll();
        }

        // Navegação entre aulas do módulo atual
        $allLessons = $lessonModel->where('id_module_lesson', $lesson->id_module_lesson)
            ->orderBy('position_lesson')
            ->findAll();
        $lessonKeys = array_column($allLessons, 'id_lesson');
        $currentIndex = array_search($lesson->id_lesson, $lessonKeys);

        $prevLesson = $lessonKeys[$currentIndex - 1] ?? null;
        $nextLesson = $lessonKeys[$currentIndex + 1] ?? null;

        $user = service('auth')->user();

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
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $coursesModel = new CourseModel();
        $courses = $coursesModel->findAll();
        $user = service('auth')->user();
        $enrollmentModel = new \App\Models\EnrollmentModel();

        // Pega todas as inscrições do usuário
        $enrollments = $enrollmentModel->where('id_student_enrollment', $user->id)
            ->findAll();

        // Cria um array com os IDs de cursos ativos
        $activeCourseIds = [];
        foreach ($enrollments as $enr) {
            if ($enr->status_enrollment === 'Ativo') {
                $activeCourseIds[] = $enr->id_course_enrollment;
            }
        }

        $pendingCourseIds = [];
        foreach ($enrollments as $enr) {
            if ($enr->status_enrollment === 'Pendente') {
                $pendingCourseIds[] = $enr->id_course_enrollment;
            }
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $lessonModel = new \App\Models\LessonModel();
        $user = service('auth')->user();

        // Pega os cursos nos quais o aluno está inscrito
        $lessons = $enrollmentModel->getStudentEnrolledCourses($user->id);

        // Para cada curso, pega a primeira aula (ordenada por posição do módulo e da aula)
        foreach ($lessons as &$lesson) {
            $firstLesson = $lessonModel
                ->select('lessons.id_lesson')
                ->join('modules', 'modules.id_module = lessons.id_module_lesson')
                ->where('modules.id_course_module', $lesson->id_course)
                ->orderBy('modules.position_module', 'ASC')
                ->orderBy('lessons.position_lesson', 'ASC')
                ->first();

            $lesson->firstLessonId = $firstLesson->id_lesson ?? null;
        }

        return view('pages/student/courses', [
            'user' => $user,
            'courses' => $courses,
            // 'lesson' => $lesson,
            'activeCourseIds' => $activeCourseIds,
            'pendingCourseIds' => $pendingCourseIds,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function checkout($idCourse)
    {
        $courseModel = new CourseModel();
        $course = $courseModel->find($idCourse);

        $enrollmentModel = new EnrollmentModel();
        $existingEnrollment = $enrollmentModel
            ->select('enrollments.*, courses.title_course')
            ->join('courses', 'courses.id_course = enrollments.id_course_enrollment')
            ->where('id_student_enrollment', service('auth')->user()->id)
            ->where('id_course_enrollment', $idCourse)
            ->first();

        $user = service('auth')->user();

        return view('pages/student/checkout', [
            'user' => $user,
            'course' => $course,
            'enrollment' => $existingEnrollment,
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
