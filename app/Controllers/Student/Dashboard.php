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
            if ($enr->status_enrollment === 'Ativa') {
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
        $lessonModel     = new LessonModel();
        $moduleModel     = new ModuleModel();
        $courseModel     = new CourseModel();
        $enrollmentModel = new EnrollmentModel();
        $db              = db_connect();

        $authUser = service('auth')->user();
        $userId   = function_exists('user_id') ? user_id() : ($authUser->id ?? $authUser->getId());

        $id = (int) $id;

        // Tenta achar como AULA
        $lesson = $lessonModel->find($id);

        // Se NÃO for aula, tratamos como CURSO e redirecionamos para "retomar"
        if (!$lesson) {
            $course = $courseModel->find($id);
            if (!$course) {
                return redirect()->back()->with('error', 'Curso não encontrado.');
            }

            // matrícula do usuário nesse curso
            $enrollment = $enrollmentModel
                ->where('id_course_enrollment', $course->id_course)
                ->first();

            if (!$enrollment) {
                return redirect()->to('/student/dashboard/checkout/' . $course->id_course)
                    ->with('warning', 'Você precisa estar inscrito neste curso.');
            }

            // aulas do curso (ordenadas por módulo e posição)
            $ordered = $db->table('lessons l')
                ->select('l.id_lesson')
                ->join('modules m', 'm.id_module = l.id_module_lesson')
                ->where('m.id_course_module', $course->id_course)
                ->orderBy('m.position_module', 'ASC')
                ->orderBy('l.position_lesson', 'ASC')
                ->get()->getResultArray();
            $orderedIds = array_map(fn($r) => (int)$r['id_lesson'], $ordered);

            // aulas concluídas pela matrícula
            $completedLessonIds = array_column(
                $db->table('progress')
                    ->select('id_lesson_progress')
                    ->where('id_enrollment_progress', $enrollment['id_enrollment'])
                    ->where('completed_at_progress IS NOT NULL', null, false)
                    ->get()->getResultArray(),
                'id_lesson_progress'
            );
            $completedSet = array_flip($completedLessonIds);

            // primeira NÃO concluída (resume). Se todas concluídas, vai para a última aula.
            $resumeId = null;
            foreach ($orderedIds as $lid) {
                if (!isset($completedSet[$lid])) {
                    $resumeId = $lid;
                    break;
                }
            }
            if ($resumeId === null && !empty($orderedIds)) {
                $resumeId = end($orderedIds); // última do curso
            }

            if ($resumeId) {
                return redirect()->to('/student/dashboard/ver_aulas/' . $resumeId)
                    ->with('info', 'Retomando de onde parou.');
            }

            return redirect()->back()->with('error', 'Nenhuma aula encontrada para este curso.');
        }

        // Daqui pra baixo: $lesson É uma aula válida
        // Pega módulo e curso da aula
        $module = $moduleModel->find($lesson->id_module_lesson);
        $course = $courseModel->find($module->id_course_module);

        // matrícula do usuário no curso
        $enrollment = $enrollmentModel
            ->where('id_course_enrollment', $course->id_course)
            ->first();

        if (!$enrollment) {
            return redirect()->to('/student/dashboard/checkout/' . $course->id_course)
                ->with('warning', 'Você precisa estar inscrito neste curso.');
        }

        // carregar módulos + aulas
        $modules = $moduleModel->where('id_course_module', $course->id_course)
            ->orderBy('position_module')->findAll();
        foreach ($modules as &$m) {
            $m->lessons = $lessonModel->where('id_module_lesson', $m->id_module)
                ->orderBy('position_lesson')->findAll();
        }

        // ordenação "global" de aulas do curso (para navegação e bloqueio server-side)
        $ordered = $db->table('lessons l')
            ->select('l.id_lesson')
            ->join('modules m', 'm.id_module = l.id_module_lesson')
            ->where('m.id_course_module', $course->id_course)
            ->orderBy('m.position_module', 'ASC')
            ->orderBy('l.position_lesson', 'ASC')
            ->get()->getResultArray();
        $orderedIds   = array_map(fn($r) => (int)$r['id_lesson'], $ordered);

        // aulas concluídas da matrícula (para checkboxes e bloqueio)
        $completedLessonIds = array_column(
            $db->table('progress')
                ->select('id_lesson_progress')
                ->where('id_enrollment_progress', $enrollment->id_enrollment)
                ->where('completed_at_progress IS NOT NULL', null, false)
                ->get()->getResultArray(),
            'id_lesson_progress'
        );

        // navegação anterior/próxima dentro do MÓDULO atual (como você já fazia)
        $allLessons   = $lessonModel->where('id_module_lesson', $lesson->id_module_lesson)
            ->orderBy('position_lesson')->findAll();
        $lessonKeys   = array_column($allLessons, 'id_lesson');
        $currentIndex = array_search($lesson->id_lesson, $lessonKeys);
        $prevLesson   = $lessonKeys[$currentIndex - 1] ?? null;
        $nextLesson   = $lessonKeys[$currentIndex + 1] ?? null;

        // (Opcional/segurança) Impedir pular: se tentou abrir uma aula "depois" da primeira não concluída, redireciona para ela
        $completedSet = array_flip($completedLessonIds);
        $firstLocked  = null;
        foreach ($orderedIds as $lid) {
            if (!isset($completedSet[$lid])) {
                $firstLocked = $lid;
                break;
            }
        }
        if ($firstLocked !== null) {
            $reqIndex  = array_search((int)$lesson->id_lesson, $orderedIds, true);
            $lockIndex = array_search($firstLocked, $orderedIds, true);
            if ($reqIndex > $lockIndex) {
                return redirect()->to('/student/dashboard/ver_aulas/' . $firstLocked)
                    ->with('warning', 'Conclua a aula anterior para continuar.');
            }
        }

        return view('pages/student/lessons', [
            'course'             => $course,
            'enrollment'         => (object)$enrollment,   // sua view usa ->id_enrollment
            'modules'            => $modules,
            'lesson'             => $lesson,
            'prevLesson'         => $prevLesson,
            'nextLesson'         => $nextLesson,
            'completedLessonIds' => $completedLessonIds,
            'user'               => $authUser,
            'sidebarLinks'       => $this->sidebarLinks(),
            'currentUrl'         => current_url(),
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
            if ($enr->status_enrollment === 'Ativa') {
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
