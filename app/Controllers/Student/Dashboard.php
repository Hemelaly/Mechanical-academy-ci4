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
        $auth = service('auth');
        $user = $auth->user();

        $coursesModel     = new CourseModel();
        $enrollmentModel  = new \App\Models\EnrollmentModel();
        $lessonModel      = new \App\Models\LessonModel();
        $db               = db_connect();

        // Todos os cursos (se ainda usa na home)
        $courses = $coursesModel->findAll();

        // Inscrições do usuário
        $enrollments = $enrollmentModel
            ->where('id_student_enrollment', $user->id)
            ->findAll();

        // Mapas úteis
        $activeCourseIds      = [];
        $pendingCourseIds     = [];
        $enrollmentByCourseId = []; // [id_course => enrollment row]

        foreach ($enrollments as $enr) {
            $courseId = (int) $enr->id_course_enrollment;
            $enrollmentByCourseId[$courseId] = $enr;

            if ($enr->status_enrollment === 'Ativa')    $activeCourseIds[]  = $courseId;
            if ($enr->status_enrollment === 'Pendente') $pendingCourseIds[] = $courseId;
        }

        // === OBJETO DE PROGRESSO POR CURSO ===
        // Monta um objeto com a estrutura:
        // $progressByCourse->{id_course} = (object)[
        //   courseId, enrollmentId, status, progress, updatedAt
        // ]
        $progressByCourseArr = [];
        foreach ($enrollmentByCourseId as $courseId => $enr) {
            $progressByCourseArr[$courseId] = (object) [
                'courseId'     => (int) $courseId,
                'enrollmentId' => (int) $enr->id_enrollment,
                'status'       => (string) $enr->status_enrollment,
                'progress'     => (int) ($enr->progress_enrollment ?? 0),
                'updatedAt'    => $enr->updated_at ?? null,
            ];
        }
        // Converte o array associativo para stdClass (objeto) como você pediu
        $progressByCourse = (object) $progressByCourseArr;

        // Cursos nos quais o aluno está inscrito (tua função custom)
        $lessons = $enrollmentModel->getStudentEnrolledCourses($user->id);

        // Helper para calcular a aula de retomada (resume) por curso/enrollment
        $calcResume = function (int $courseId, int $enrollmentId) use ($db) {
            $ordered = $db->table('lessons l')
                ->select('l.id_lesson')
                ->join('modules m', 'm.id_module = l.id_module_lesson')
                ->where('m.id_course_module', $courseId)
                ->orderBy('m.position_module', 'ASC')
                ->orderBy('l.position_lesson', 'ASC')
                ->get()->getResultArray();
            $orderedIds = array_map(fn($r) => (int)$r['id_lesson'], $ordered);

            if (empty($orderedIds)) return null;

            $completedLessonIds = array_column(
                $db->table('progress')
                    ->select('id_lesson_progress')
                    ->where('id_enrollment_progress', $enrollmentId)
                    ->where('completed_at_progress IS NOT NULL', null, false)
                    ->get()->getResultArray(),
                'id_lesson_progress'
            );
            $completedSet = array_flip($completedLessonIds);

            foreach ($orderedIds as $lid) {
                if (!isset($completedSet[$lid])) return $lid; // primeira não concluída
            }
            return end($orderedIds); // todas concluídas -> última
        };

        // Para cada curso inscrito: calcular resume e anexar progresso
        foreach ($lessons as &$courseRow) {
            $courseId     = (int) $courseRow->id_course;
            $enrollment   = $enrollmentByCourseId[$courseId] ?? null;
            $enrollmentId = $enrollment->id_enrollment ?? 0;

            // resume
            if ($enrollmentId) {
                $courseRow->resumeLessonId = $calcResume($courseId, (int)$enrollmentId);
            } else {
                $courseRow->resumeLessonId = null;
            }

            // progresso do enrollment (direto na linha do curso)
            $courseRow->progress = isset($progressByCourseArr[$courseId])
                ? $progressByCourseArr[$courseId]->progress
                : 0.0;
        }
        unset($courseRow);

        return view('pages/student/home', [
            'user'              => $user,
            'courses'           => $courses,
            'lesson'           => $lessons,          // lista de cursos inscritos (cada item com ->resumeLessonId e ->progress)
            'progress'  => $progressByCourse, // objeto com progresso por id de curso
            'activeCourseIds'   => $activeCourseIds,
            'pendingCourseIds'  => $pendingCourseIds,
            'sidebarLinks'      => $this->sidebarLinks(),
            'currentUrl'        => current_url(),
        ]);
    }

    public function my_courses()
    {
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $lessonModel     = new \App\Models\LessonModel();
        $db              = db_connect();

        $user = service('auth')->user();

        // Cursos nos quais o aluno está inscrito (função custom)
        $courses = $enrollmentModel->getStudentEnrolledCourses($user->id);

        // Todas as matrículas do aluno
        $enrollments = $enrollmentModel
            ->where('id_student_enrollment', $user->id)
            ->findAll();

        // Mapas: curso -> (id_enrollment, progress, etc.)
        $enrollmentByCourse = [];   // [id_course => id_enrollment]
        $progressByCourseArr = [];  // [id_course => (object)progress info]

        foreach ($enrollments as $enr) {
            $courseId = (int) $enr->id_course_enrollment;
            $enrollmentByCourse[$courseId] = (int) $enr->id_enrollment;

            $progressByCourseArr[$courseId] = (object) [
                'courseId'     => $courseId,
                'enrollmentId' => (int) $enr->id_enrollment,
                'status'       => (string) $enr->status_enrollment,
                // se sua coluna já é % inteiro (0–100), mantenha (int). Se for decimal (0–100.x), use (float)
                'progress'     => (int) ($enr->progress_enrollment ?? 0),
                'updatedAt'    => $enr->updated_at ?? null,
            ];
        }
        $progressByCourse = (object) $progressByCourseArr;

        // Helper: retorna a aula de retomada (primeira não concluída; se todas, a última)
        $calcResume = function (int $courseId, int $enrollmentId) use ($db) {
            $ordered = $db->table('lessons l')
                ->select('l.id_lesson')
                ->join('modules m', 'm.id_module = l.id_module_lesson')
                ->where('m.id_course_module', $courseId)
                ->orderBy('m.position_module', 'ASC')
                ->orderBy('l.position_lesson', 'ASC')
                ->get()->getResultArray();

            $orderedIds = array_map(fn($r) => (int)$r['id_lesson'], $ordered);
            if (empty($orderedIds)) return null;

            $completedLessonIds = array_column(
                $db->table('progress')
                    ->select('id_lesson_progress')
                    ->where('id_enrollment_progress', $enrollmentId)
                    ->where('completed_at_progress IS NOT NULL', null, false)
                    ->get()->getResultArray(),
                'id_lesson_progress'
            );
            $completedSet = array_flip($completedLessonIds);

            foreach ($orderedIds as $lid) {
                if (!isset($completedSet[$lid])) return $lid; // primeira não concluída
            }
            return end($orderedIds); // todas concluídas -> última
        };

        // Para cada curso, definir resumeLessonId e anexar progresso
        foreach ($courses as &$course) {
            $courseId     = (int) $course->id_course;
            $enrollmentId = $enrollmentByCourse[$courseId] ?? 0;

            $course->resumeLessonId = $enrollmentId ? $calcResume($courseId, $enrollmentId) : null;

            // progresso direto no objeto do curso
            $course->progress = isset($progressByCourseArr[$courseId])
                ? $progressByCourseArr[$courseId]->progress
                : 0;

            // se quiser, também pode anexar enrollmentId/status
            $course->enrollmentId = isset($progressByCourseArr[$courseId]) ? $progressByCourseArr[$courseId]->enrollmentId : null;
            $course->enrollmentStatus = isset($progressByCourseArr[$courseId]) ? $progressByCourseArr[$courseId]->status : null;
        }
        unset($course);

        return view('pages/student/my_courses', [
            'user'             => $user,
            'courses'          => $courses,          // cada item tem ->resumeLessonId e ->progress
            'progress' => $progressByCourse, // objeto opcional para lookup por id_course
            'sidebarLinks'     => $this->sidebarLinks(),
            'currentUrl'       => current_url()
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

        // Helper local para calcular a aula de retomada (resume) de um curso
        $calcResume = function (int $courseId, int $enrollmentId) use ($db) {
            // ordem global das aulas do curso
            $ordered = $db->table('lessons l')
                ->select('l.id_lesson')
                ->join('modules m', 'm.id_module = l.id_module_lesson')
                ->where('m.id_course_module', $courseId)
                ->orderBy('m.position_module', 'ASC')
                ->orderBy('l.position_lesson', 'ASC')
                ->get()->getResultArray();
            $orderedIds = array_map(fn($r) => (int)$r['id_lesson'], $ordered);

            if (empty($orderedIds)) {
                return [null, $orderedIds];
            }

            // aulas concluídas
            $completedLessonIds = array_column(
                $db->table('progress')
                    ->select('id_lesson_progress')
                    ->where('id_enrollment_progress', $enrollmentId)
                    ->where('completed_at_progress IS NOT NULL', null, false)
                    ->get()->getResultArray(),
                'id_lesson_progress'
            );
            $completedSet = array_flip($completedLessonIds);

            // primeira NÃO concluída -> resume; se todas concluídas -> última
            $resumeId = null;
            foreach ($orderedIds as $lid) {
                if (!isset($completedSet[$lid])) {
                    $resumeId = $lid;
                    break;
                }
            }
            if ($resumeId === null) {
                $resumeId = end($orderedIds);
            }

            return [$resumeId, $orderedIds];
        };

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

            // calcula a aula de retomada e redireciona SEMPRE
            [$resumeId] = $calcResume((int)$course->id_course, (int)$enrollment->id_enrollment);
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

        // === SEMPRE FORÇAR RESUME, MESMO QUANDO VEIO COM ID DE AULA ===
        // Use ?override=1 na URL para abrir a aula explicitamente sem forçar.
        $override = (int) (service('request')->getGet('override') ?? 0);
        [$resumeId, $orderedIds] = $calcResume((int)$course->id_course, (int)$enrollment->id_enrollment);

        if (!$override && $resumeId) {
            $reqIndex    = array_search((int)$lesson->id_lesson, $orderedIds, true);
            $resumeIndex = array_search((int)$resumeId,        $orderedIds, true);

            // Só bloqueia se estiver tentando abrir uma aula À FRENTE da retomada
            if ($reqIndex !== false && $resumeIndex !== false && $reqIndex > $resumeIndex) {
                return redirect()->to('/student/dashboard/ver_aulas/' . $resumeId)
                    ->with('warning', 'Conclua a aula anterior para continuar.');
            }
        }
        // === FIM DO FORCE RESUME ===

        // carregar módulos + aulas (para sidebar)
        $modules = $moduleModel->where('id_course_module', $course->id_course)
            ->orderBy('position_module')->findAll();
        foreach ($modules as &$m) {
            $m->lessons = $lessonModel->where('id_module_lesson', $m->id_module)
                ->orderBy('position_lesson')->findAll();
        }

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

        // (Opcional) Bloqueio de pulo (mantido)
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
        $coursesModel    = new CourseModel();
        $lessonModel     = new \App\Models\LessonModel();
        $db              = db_connect();

        $user = service('auth')->user();

        // Todos os cursos (se a view usa)
        $courses = $coursesModel->findAll();

        // Todas as matrículas do usuário
        $enrollments = $enrollmentModel
            ->where('id_student_enrollment', $user->id)
            ->findAll();

        // IDs de cursos por status + mapa curso→enrollment row
        $activeCourseIds      = [];
        $pendingCourseIds     = [];
        $enrollmentByCourseId = []; // [id_course => enrollment row]

        foreach ($enrollments as $enr) {
            $courseId = (int) $enr->id_course_enrollment;
            $enrollmentByCourseId[$courseId] = $enr;

            if ($enr->status_enrollment === 'Ativa')    $activeCourseIds[]  = $courseId;
            if ($enr->status_enrollment === 'Pendente') $pendingCourseIds[] = $courseId;
        }

        // Cursos nos quais o aluno está inscrito (tua função custom)
        $lessons = $enrollmentModel->getStudentEnrolledCourses($user->id);

        // Helper: retorna a aula de retomada (primeira não concluída; se todas, a última)
        $calcResume = function (int $courseId, int $enrollmentId) use ($db) {
            // ordem global das aulas do curso
            $ordered = $db->table('lessons l')
                ->select('l.id_lesson')
                ->join('modules m', 'm.id_module = l.id_module_lesson')
                ->where('m.id_course_module', $courseId)
                ->orderBy('m.position_module', 'ASC')
                ->orderBy('l.position_lesson', 'ASC')
                ->get()->getResultArray();

            $orderedIds = array_map(fn($r) => (int)$r['id_lesson'], $ordered);
            if (empty($orderedIds)) return null;

            // aulas concluídas desta matrícula
            $completedLessonIds = array_column(
                $db->table('progress')
                    ->select('id_lesson_progress')
                    ->where('id_enrollment_progress', $enrollmentId)
                    ->where('completed_at_progress IS NOT NULL', null, false)
                    ->get()->getResultArray(),
                'id_lesson_progress'
            );
            $completedSet = array_flip($completedLessonIds);

            // primeira NÃO concluída; se todas concluídas, a última
            foreach ($orderedIds as $lid) {
                if (!isset($completedSet[$lid])) return $lid;
            }
            return end($orderedIds);
        };

        // Para cada curso inscrito: calcular resume + progresso
        foreach ($lessons as &$row) {
            $courseId   = (int) $row->id_course;
            $enrollment = $enrollmentByCourseId[$courseId] ?? null;

            // resume
            if ($enrollment) {
                $row->resumeLessonId = $calcResume($courseId, (int)$enrollment->id_enrollment);
            } else {
                $row->resumeLessonId = null;
            }

            // progresso (progress_enrollment da tabela enrollments)
            $row->progress = $enrollment ? (int) ($enrollment->progress_enrollment ?? 0) : 0;
        }
        unset($row);

        return view('pages/student/courses', [
            'user'            => $user,
            'courses'         => $courses,
            'lesson'         => $lessons,        // << a lista com resumeLessonId e progress
            'activeCourseIds' => $activeCourseIds,
            'pendingCourseIds' => $pendingCourseIds,
            'sidebarLinks'    => $this->sidebarLinks(),
            'currentUrl'      => current_url()
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
