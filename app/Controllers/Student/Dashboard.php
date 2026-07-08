<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\CertificateModel;
use App\Models\EnrollmentModel;
use App\Models\ExtendedUserModel;
use App\Models\JitsiModel;
use App\Models\JitsiRecordingModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;
use App\Libraries\JitsiJwtService;
use CodeIgniter\Shield\Models\UserModel;

class Dashboard extends BaseController
{
    private function makeSlug(string $value): string
    {
        helper('text');
        $value = convert_accented_characters($value);
        return url_title($value, '-', true);
    }

    private function getLessonSlugById(int $lessonId): ?string
    {
        $lessonModel = new LessonModel();
        $lesson = $lessonModel->find($lessonId);
        if (! $lesson) {
            return null;
        }

        return $this->makeSlug((string) $lesson->title_lesson);
    }

    private function sidebarLinks()
    {
        return [
            [
                'label' => 'Iní­cio',
                'icon' => 'bi-house-door',
                'url' => '/student/dashboard',
                'pattern' => '/student/dashboard' // Apenas correspondÃªncia exata
            ],
            [
                'label' => 'Meus Cursos',
                'icon' => 'bi-book',
                'url' => '/student/dashboard/inscricoes',
                'pattern' => '/student/dashboard/inscricoes*' // Com * para subpÃ¡ginas
            ],
            [
                'label' => 'Aulas ao Vivo',
                'icon' => 'bi-camera-video',
                'url' => '/student/dashboard/aulas_ao_vivo',
                'pattern' => '/student/dashboard/aulas_ao_vivo*'
            ],
            [
                'label' => 'Todos Cursos',
                'icon' => 'bi-book',
                'url' => '/student/dashboard/cursos',
                'pattern' => '/student/dashboard/cursos*' // Com * para subpÃ¡ginas
            ],
                    [
                'label' => 'Certificados',
                'icon' => 'bi-folder',
                'url' => '/student/dashboard/certificados',
                'pattern' => '/student/dashboard/certificados*' // Com * para subpÃ¡ginas
            ],
            [
                'label' => 'Perfil',
                'icon' => 'bi-person-circle',
                'url' => '/student/dashboard/perfil',
                'pattern' => '/student/dashboard/perfil*' // Com * para subpÃ¡ginas
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
        $courses = $coursesModel->where('status_course', 'Ativo')->findAll();

        // InscriÃ§Ãµes do usuÃ¡rio
        $enrollments = $enrollmentModel
            ->where('id_student_enrollment', $user->id)
            ->findAll();

        // Mapas Ãºteis
        $activeCourseIds      = [];
        $pendingCourseIds     = [];
        $enrollmentByCourseId = []; // [id_course => enrollment row]

        foreach ($enrollments as $enr) {
            $courseId = (int) $enr->id_course_enrollment;
            $enrollmentByCourseId[$courseId] = $enr;
            $status = strtolower((string) $enr->status_enrollment);

            if ($status === 'ativa')    $activeCourseIds[]  = $courseId;
            if ($status === 'pendente') $pendingCourseIds[] = $courseId;
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
                'status'       => strtolower((string) $enr->status_enrollment),
                'progress'     => (int) ($enr->progress_enrollment ?? 0),
                'updatedAt'    => $enr->updated_at ?? null,
            ];
        }
        // Converte o array associativo para stdClass (objeto) como vocÃª pediu
        $progressByCourse = (object) $progressByCourseArr;

        // Cursos nos quais o aluno estÃ¡ inscrito (tua funÃ§Ã£o custom)
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
                if (!isset($completedSet[$lid])) return $lid; // primeira nÃ£o concluÃ­da
            }
            return end($orderedIds); // todas concluÃ­das -> Ãºltima
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

            $courseRow->courseSlug = $this->makeSlug((string) $courseRow->title_course);
            $courseRow->resumeLessonSlug = $courseRow->resumeLessonId
                ? $this->getLessonSlugById((int) $courseRow->resumeLessonId)
                : null;

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

        // Cursos nos quais o aluno estÃ¡ inscrito (funÃ§Ã£o custom)
        $courses = $enrollmentModel->getStudentEnrolledCourses($user->id);

        // Todas as matrÃ­culas do aluno
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
                'status'       => strtolower((string) $enr->status_enrollment),
                // se sua coluna jÃ¡ Ã© % inteiro (0â€“100), mantenha (int). Se for decimal (0â€“100.x), use (float)
                'progress'     => (int) ($enr->progress_enrollment ?? 0),
                'updatedAt'    => $enr->updated_at ?? null,
            ];
        }
        $progressByCourse = (object) $progressByCourseArr;

        // Helper: retorna a aula de retomada (primeira nÃ£o concluÃ­da; se todas, a Ãºltima)
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
                if (!isset($completedSet[$lid])) return $lid; // primeira nÃ£o concluÃ­da
            }
            return end($orderedIds); // todas concluÃ­das -> Ãºltima
        };

        // Para cada curso, definir resumeLessonId e anexar progresso
        foreach ($courses as &$course) {
            $courseId     = (int) $course->id_course;
            $enrollmentId = $enrollmentByCourse[$courseId] ?? 0;

            $course->resumeLessonId = $enrollmentId ? $calcResume($courseId, $enrollmentId) : null;

            $course->courseSlug = $this->makeSlug((string) $course->title_course);
            $course->resumeLessonSlug = $course->resumeLessonId
                ? $this->getLessonSlugById((int) $course->resumeLessonId)
                : null;

            // progresso direto no objeto do curso
            $course->progress = isset($progressByCourseArr[$courseId])
                ? $progressByCourseArr[$courseId]->progress
                : 0;

            // se quiser, tambÃ©m pode anexar enrollmentId/status
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

        // UsuÃ¡rio atual
        $authUser = service('auth')->user();
        if (! $authUser) {
            return redirect()->to(site_url('login'))
                ->with('error', 'SessÃ£o expirada. FaÃ§a login novamente.');
        }
        $userId = function_exists('user_id') ? user_id() : ($authUser->id ?? $authUser->getId());

        $id = (int) $id;

        // Helper para calcular retomada e ordem global (por curso)
        $calcResume = function (int $courseId, int $enrollmentId) use ($db) {
            $ordered = $db->table('lessons l')
                ->select('l.id_lesson')
                ->join('modules m', 'm.id_module = l.id_module_lesson')
                ->where('m.id_course_module', $courseId)
                ->orderBy('m.position_module', 'ASC')
                ->orderBy('l.position_lesson', 'ASC')
                ->get()->getResultArray();

            $orderedIds = array_map(fn($r) => (int) $r['id_lesson'], $ordered);
            if (empty($orderedIds)) {
                return [null, $orderedIds];
            }

            // ConcluÃ­das POR ESTA MATRÃCULA
            $completedLessonIds = array_column(
                $db->table('progress')
                    ->select('id_lesson_progress')
                    ->where('id_enrollment_progress', $enrollmentId)
                    ->where('completed_at_progress IS NOT NULL', null, false)
                    ->get()->getResultArray(),
                'id_lesson_progress'
            );
            $completedSet = array_flip($completedLessonIds);

            $resumeId = null;
            foreach ($orderedIds as $lid) {
                if (! isset($completedSet[$lid])) {
                    $resumeId = $lid;
                    break;
                }
            }
            if ($resumeId === null) {
                // tudo concluÃ­do â†’ volta Ã  Ãºltima
                $resumeId = end($orderedIds);
            }

            return [$resumeId, $orderedIds];
        };

        // Tenta achar como AULA
        $lesson = $lessonModel->find($id);

        // ===========================
        // NÃƒO Ã‰ AULA â†’ tratar como CURSO
        // ===========================
        if (! $lesson) {
            $course = $courseModel->find($id);
            if (! $course) {
                return redirect()->to('/student/dashboard/inscricoes')
                    ->with('error', 'Curso nÃ£o encontrado.');
            }

            // MATRÃCULA do usuÃ¡rio logado nesse curso
            $enrollment = $enrollmentModel
                ->where('id_course_enrollment',  (int) $course->id_course)
                ->where('id_student_enrollment', (int) $userId)   // <<< FILTRO PELO ESTUDANTE
                ->first();

            if (! $enrollment) {
                return redirect()->to('/student/dashboard/checkout/' . (int) $course->id_course)
                    ->with('warning', 'VocÃª precisa estar inscrito neste curso.');
            }

            if (strtolower((string) ($enrollment->status_enrollment ?? '')) === 'cancelada') {
                [$resumeId, $orderedIds] = $calcResume((int) $course->id_course, (int) $enrollment->id_enrollment);
                $targetId = $resumeId ?: ($orderedIds[0] ?? null);
                if ($targetId) {
                    return redirect()->to('/student/dashboard/ver_aulas/' . (int) $targetId)
                        ->with('blocked_access', true);
                }
                return redirect()->to('/student/dashboard/inscricoes')
                    ->with('error', 'Seu acesso a este curso está bloqueado.');
            }

            [$resumeId] = $calcResume((int) $course->id_course, (int) $enrollment->id_enrollment);
            if ($resumeId) {
                $courseSlug = $this->makeSlug((string) $course->title_course);
                $lessonSlug = $this->getLessonSlugById((int) $resumeId);
                if ($lessonSlug) {
                    return redirect()->to('/student/dashboard/inscricoes/' . $courseSlug . '/' . $lessonSlug)
                        ->with('info', 'Retomando de onde parou.');
                }
                return redirect()->to('/student/dashboard/ver_aulas/' . $resumeId)
                    ->with('info', 'Retomando de onde parou.');
            }

            return redirect()->to('/student/dashboard/inscricoes')
                ->with('error', 'Nenhuma aula encontrada para este curso.');
        }

        // ===========================
        // Ã‰ AULA â†’ obter mÃ³dulo/curso
        // ===========================
        $module = $moduleModel->find($lesson->id_module_lesson);
        if (! $module) {
            return redirect()->to('/student/dashboard/inscricoes')
                ->with('error', 'MÃ³dulo da aula nÃ£o encontrado.');
        }

        $course = $courseModel->find($module->id_course_module);
        if (! $course) {
            return redirect()->to('/student/dashboard/inscricoes')
                ->with('error', 'Curso da aula nÃ£o encontrado.');
        }

        // MATRÃCULA do usuÃ¡rio logado nesse curso
        $enrollment = $enrollmentModel
            ->where('id_course_enrollment',  (int) $course->id_course)
            ->where('id_student_enrollment', (int) $userId)      // <<< FILTRO PELO ESTUDANTE
            ->first();

        if (! $enrollment) {
            return redirect()->to('/student/dashboard/checkout/' . (int) $course->id_course)
                ->with('warning', 'VocÃª precisa estar inscrito neste curso.');
        }

        $accessBlocked = strtolower((string) ($enrollment->status_enrollment ?? '')) === 'cancelada';

        $moduleList = $moduleModel->where('id_course_module', $course->id_course)
            ->orderBy('position_module')
            ->findAll();

        // Bloqueia o mÃ³dulo seguinte atÃ© atingir a nota mÃ­nima no quiz do mÃ³dulo anterior
        $moduleIndex = null;
        foreach ($moduleList as $idx => $mod) {
            if ((int) $mod->id_module === (int) $module->id_module) {
                $moduleIndex = $idx;
                break;
            }
        }

        if ($moduleIndex !== null && $moduleIndex > 0) {
            $prevModule = $moduleList[$moduleIndex - 1];
            $quizLesson = $lessonModel->where('id_module_lesson', $prevModule->id_module)
                ->where('type_lesson', 'quiz')
                ->orderBy('position_lesson')
                ->first();

            if ($quizLesson) {
                $scoreRow = $db->table('progress')
                    ->select('score_progress')
                    ->where('id_enrollment_progress', (int) $enrollment->id_enrollment)
                    ->where('id_lesson_progress', (int) $quizLesson->id_lesson)
                    ->get()
                    ->getRowArray();

                $score = $scoreRow['score_progress'] ?? null;
                $minScore = (int) ($prevModule->min_score_module ?? 80);

                $warningMessage = 'Faça o quiz do módulo anterior e obtenha no mínimo ' . $minScore . '% para avançar.';
                if ($score !== null && (float) $score < $minScore) {
                    $warningMessage = 'Você precisa refazer o quiz anterior e atingir ao menos ' . $minScore . '% para continuar.';
                }

                if ($score === null || (float) $score < $minScore) {
                    $courseSlug = $this->makeSlug((string) $course->title_course);
                    $lessonSlug = $this->makeSlug((string) $quizLesson->title_lesson);
                    return redirect()->to('/student/dashboard/inscricoes/' . $courseSlug . '/' . $lessonSlug)
                        ->with('warning', $warningMessage);
                }
            }
        }

        // ForÃ§a retomar apenas se tentar ir Ã  frente
        $override = (int) (service('request')->getGet('override') ?? 0);
        [$resumeId, $orderedIds] = $calcResume((int) $course->id_course, (int) $enrollment->id_enrollment);

        if (! $override && $resumeId) {
            $reqIndex    = array_search((int) $lesson->id_lesson, $orderedIds, true);
            $resumeIndex = array_search((int) $resumeId,          $orderedIds, true);

            if ($reqIndex !== false && $resumeIndex !== false && $reqIndex > $resumeIndex) {
                $courseSlug = $this->makeSlug((string) $course->title_course);
                $lessonSlug = $this->getLessonSlugById((int) $resumeId);
                if ($lessonSlug) {
                    return redirect()->to('/student/dashboard/inscricoes/' . $courseSlug . '/' . $lessonSlug)
                        ->with('warning', 'Conclua a aula anterior para continuar.');
                }
                return redirect()->to('/student/dashboard/ver_aulas/' . $resumeId)
                    ->with('warning', 'Conclua a aula anterior para continuar.');
            }
        }

        // Sidebar: mÃ³dulos + aulas (sem interferir em prev/next)
        $modules = $moduleList;

        foreach ($modules as &$m) {
            $m->lessons = $lessonModel->where('id_module_lesson', $m->id_module)
                ->orderBy('position_lesson')
                ->findAll();
        }
        unset($m);

        $courseSlug = $this->makeSlug((string) $course->title_course);
        $lessonSlugById = [];
        foreach ($modules as $m) {
            foreach ($m->lessons as $l) {
                $lessonSlugById[(int) $l->id_lesson] = $this->makeSlug((string) $l->title_lesson);
            }
        }

        // IDs concluÃ­dos POR ESTA MATRÃCULA
        $completedLessonIds = array_column(
            $db->table('progress')
                ->select('id_lesson_progress')
                ->where('id_enrollment_progress', (int) $enrollment->id_enrollment)
                ->where('completed_at_progress IS NOT NULL', null, false)
                ->get()->getResultArray(),
            'id_lesson_progress'
        );

        $quizScoreRow = $db->table('progress')
            ->select('score_progress')
            ->where('id_enrollment_progress', (int) $enrollment->id_enrollment)
            ->where('id_lesson_progress', (int) $lesson->id_lesson)
            ->get()
            ->getRowArray();
        $quizScore = $quizScoreRow['score_progress'] ?? null;

        // Prev/Next globais com base em $orderedIds
        $currIndex = array_search((int) $lesson->id_lesson, $orderedIds, true);
        $prevLesson = ($currIndex !== false && $currIndex > 0)
            ? $orderedIds[$currIndex - 1]
            : null;
        $nextLesson = ($currIndex !== false && $currIndex < count($orderedIds) - 1)
            ? $orderedIds[$currIndex + 1]
            : null;

        $prevLessonSlug = $prevLesson ? ($lessonSlugById[$prevLesson] ?? null) : null;
        $nextLessonSlug = $nextLesson ? ($lessonSlugById[$nextLesson] ?? null) : null;

        $nextModuleLessonId = null;
        $nextModuleLessonSlug = null;
        foreach ($modules as $idx => $m) {
            if ((int) $m->id_module === (int) $lesson->id_module_lesson) {
                $nextModule = $modules[$idx + 1] ?? null;
                if ($nextModule && !empty($nextModule->lessons)) {
                    $nextModuleLessonId = (int) $nextModule->lessons[0]->id_lesson;
                    $nextModuleLessonSlug = $lessonSlugById[$nextModuleLessonId] ?? null;
                }
                break;
            }
        }

        $certificateModel = new CertificateModel();
        $certificate = $certificateModel
            ->where('id_user_certificate', (int) $userId)
            ->where('id_course_certificate', (int) $course->id_course)
            ->first();

        $completedAt = $enrollment->completed_enrollment ?? null;
        if (empty($completedAt) && (int) ($enrollment->progress_enrollment ?? 0) >= 100) {
            $completedAtRow = $db->table('progress')
                ->selectMax('completed_at_progress', 'completed_at')
                ->where('id_enrollment_progress', (int) $enrollment->id_enrollment)
                ->get()
                ->getRowArray();
            $completedAt = $completedAtRow['completed_at'] ?? null;
            if (! empty($completedAt)) {
                $db->table('enrollments')
                    ->where('id_enrollment', (int) $enrollment->id_enrollment)
                    ->update(['completed_enrollment' => $completedAt]);
            }
        }
        $availableAt = $certificate['avaiable_at_certificate'] ?? null;
        if (empty($availableAt) && !empty($completedAt)) {
            // Disponibiliza imediatamente apÃ³s a conclusÃ£o.
            $availableAt = $completedAt;
        }
        $availableAtTs = $availableAt ? strtotime($availableAt) : null;
        $pdfReady = !empty($certificate['pdf_path_certificate']) && (!$availableAtTs || time() >= $availableAtTs);

        return view('pages/student/lessons', [
            'course'             => $course,
            'enrollment'         => (object) $enrollment,
            'accessBlocked'      => $accessBlocked ?? false,
            'modules'            => $modules,
            'lesson'             => $lesson,
            'prevLesson'         => $prevLesson,
            'nextLesson'         => $nextLesson,
            'courseSlug'         => $courseSlug,
            'prevLessonSlug'     => $prevLessonSlug,
            'nextLessonSlug'     => $nextLessonSlug,
            'nextModuleLessonId' => $nextModuleLessonId,
            'nextModuleLessonSlug' => $nextModuleLessonSlug,
            'lessonSlugById'     => $lessonSlugById,
            'completedLessonIds' => $completedLessonIds,
            'quizScore'          => $quizScore,
            'certificateInfo'    => [
                'completedAt' => $completedAt ? date('c', strtotime($completedAt)) : null,
                'availableAt' => $availableAt ? date('c', strtotime($availableAt)) : null,
                'pdfReady'    => $pdfReady,
            ],
            'user'               => $authUser,
            'sidebarLinks'       => $this->sidebarLinks(),
            'currentUrl'         => current_url(false),
        ]);
    }

    public function lessonsBySlug($courseSlug, $lessonSlug)
    {
        $courseModel = new CourseModel();
        $lessonModel = new LessonModel();

        $courseId = null;
        $courses = $courseModel->findAll();
        foreach ($courses as $course) {
            if ($this->makeSlug((string) $course->title_course) === $courseSlug) {
                $courseId = (int) $course->id_course;
                break;
            }
        }

        if (! $courseId) {
            return redirect()->to('/student/dashboard/inscricoes')
                ->with('error', 'Curso nÃ£o encontrado.');
        }

        $lessons = $lessonModel
            ->select('lessons.id_lesson, lessons.title_lesson')
            ->join('modules', 'modules.id_module = lessons.id_module_lesson')
            ->where('modules.id_course_module', $courseId)
            ->get()
            ->getResult();

        $lessonId = null;
        foreach ($lessons as $l) {
            if ($this->makeSlug((string) $l->title_lesson) === $lessonSlug) {
                $lessonId = (int) $l->id_lesson;
                break;
            }
        }

        if (! $lessonId) {
            return redirect()->to('/student/dashboard/inscricoes')
                ->with('error', 'Aula nÃ£o encontrada.');
        }

        return $this->lessons($lessonId);
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

        // Todas as matrÃ­culas do usuÃ¡rio
        $enrollments = $enrollmentModel
            ->where('id_student_enrollment', $user->id)
            ->findAll();

        // IDs de cursos por status + mapa cursoâ†’enrollment row
        $activeCourseIds      = [];
        $pendingCourseIds     = [];
        $enrollmentByCourseId = []; // [id_course => enrollment row]

        foreach ($enrollments as $enr) {
            $courseId = (int) $enr->id_course_enrollment;
            $enrollmentByCourseId[$courseId] = $enr;

            $status = strtolower((string) $enr->status_enrollment);
            if ($status === 'ativa')    $activeCourseIds[]  = $courseId;
            if ($status === 'pendente') $pendingCourseIds[] = $courseId;
        }

        // Cursos nos quais o aluno estÃ¡ inscrito (tua funÃ§Ã£o custom)
        $lessons = $enrollmentModel->getStudentEnrolledCourses($user->id);

        // Helper: retorna a aula de retomada (primeira nÃ£o concluÃ­da; se todas, a Ãºltima)
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

            // aulas concluÃ­das desta matrÃ­cula
            $completedLessonIds = array_column(
                $db->table('progress')
                    ->select('id_lesson_progress')
                    ->where('id_enrollment_progress', $enrollmentId)
                    ->where('completed_at_progress IS NOT NULL', null, false)
                    ->get()->getResultArray(),
                'id_lesson_progress'
            );
            $completedSet = array_flip($completedLessonIds);

            // primeira NÃƒO concluÃ­da; se todas concluÃ­das, a Ãºltima
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

            $row->courseSlug = $this->makeSlug((string) $row->title_course);
            $row->resumeLessonSlug = $row->resumeLessonId
                ? $this->getLessonSlugById((int) $row->resumeLessonId)
                : null;

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
        if (! $course || $course->status_course !== 'Ativo') {
            return redirect()->back()->with('error', 'Curso nÃƒÂ£o encontrado.');
        }

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

    public function liveClasses()
    {
        $user = service('auth')->user();
        $db = db_connect();

        $classes = $db->table('jitsi j')
            ->select([
                'j.id_jitsi',
                'j.title_jitsi',
                'j.description_jitsi',
                'j.id_course_jitsi',
                'j.class_type_jitsi',
                'j.meeting_date_jitsi',
                'j.start_time_jitsi',
                'j.end_time_jitsi',
                'j.status_jitsi',
                'j.recording_jitsi',
                'j.room_jitsi',
                'c.title_course',
                'u.username as instructor_name',
            ])
            ->join('courses c', 'c.id_course = j.id_course_jitsi')
            ->join('users u', 'u.id = j.id_user_jitsi')
            ->join('enrollments e', 'e.id_course_enrollment = j.id_course_jitsi')
            ->where('e.id_student_enrollment', (int) $user->id)
            ->where('e.status_enrollment', 'ativa')
            ->orderBy('j.id_jitsi', 'DESC')
            ->get()
            ->getResult();

        $recordingsByClass = [];
        $classIds = array_map(static fn ($row) => (int) $row->id_jitsi, $classes);
        if (! empty($classIds)) {
            $recordingModel = new JitsiRecordingModel();
            $published = $recordingModel
                ->whereIn('id_jitsi_session', $classIds)
                ->where('is_published', 1)
                ->where('status_recording', 'ready')
                ->orderBy('id_jitsi_recording', 'DESC')
                ->findAll();

            foreach ($published as $rec) {
                $sessionId = (int) $rec->id_jitsi_session;
                if (! isset($recordingsByClass[$sessionId])) {
                    $recordingsByClass[$sessionId] = [];
                }
                $recordingsByClass[$sessionId][] = $rec;
            }
        }

        return view('pages/student/live_classes', [
            'user' => $user,
            'classes' => $classes,
            'recordingsByClass' => $recordingsByClass,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
        ]);
    }

    public function liveStream($id)
    {
        $user = service('auth')->user();
        $jitsiModel = new JitsiModel();
        $recordingModel = new JitsiRecordingModel();
        /** @var JitsiJwtService $jitsiJwt */
        $jitsiJwt = service('jitsiJwt');

        $aula = $jitsiModel->find((int) $id);
        if (! $aula) {
            return redirect()->to('/student/dashboard/aulas_ao_vivo')->with('error', 'Aula ao vivo nao encontrada.');
        }

        if (empty($aula->id_course_jitsi)) {
            return redirect()->to('/student/dashboard/aulas_ao_vivo')->with('error', 'Aula sem curso associado.');
        }

        $isEnrolled = db_connect()->table('enrollments')
            ->where('id_course_enrollment', (int) $aula->id_course_jitsi)
            ->where('id_student_enrollment', (int) $user->id)
            ->where('status_enrollment', 'ativa')
            ->countAllResults() > 0;

        if (! $isEnrolled) {
            return redirect()->to('/student/dashboard/aulas_ao_vivo')->with('error', 'Sem permissao para esta aula.');
        }

        $displayName = trim((string) ($user->username ?? ('Aluno ' . $user->id)));
        $email = trim((string) ($user->email ?? ''));
        $avatar = ! empty($user->img) ? base_url((string) $user->img) : '';

        $token = null;
        try {
            $token = $jitsiJwt->buildToken(
                (string) $aula->room_jitsi,
                [
                    'id' => (string) $user->id,
                    'name' => $displayName,
                    'email' => $email,
                    'avatar' => $avatar,
                ],
                false,
                [
                    'recording' => false,
                    'screen-sharing' => (bool) $aula->screenshare_jitsi,
                ]
            );
        } catch (\Throwable $e) {
            log_message('warning', 'Falha ao gerar token Jitsi para aluno: {message}', ['message' => $e->getMessage()]);
        }

        $recordings = $recordingModel
            ->where('id_jitsi_session', (int) $aula->id_jitsi)
            ->where('is_published', 1)
            ->where('status_recording', 'ready')
            ->orderBy('id_jitsi_recording', 'DESC')
            ->findAll();

        return view('pages/instructor/live_stream', [
            'aula' => $aula,
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
            'jitsiDomain' => $jitsiJwt->getDomain(),
            'jitsiExternalApiScript' => $jitsiJwt->getExternalApiScriptUrl(),
            'jitsiRoomName' => $jitsiJwt->buildRoomName((string) $aula->room_jitsi),
            'jitsiToken' => $token,
            'jitsiRecordingMode' => $jitsiJwt->getDefaultRecordingMode(),
            'canModerate' => false,
            'canManageRecordings' => false,
            'backUrl' => site_url('student/dashboard/aulas_ao_vivo'),
            'endStreamUrl' => '',
            'saveRecordingUrl' => '',
            'publishToggleBaseUrl' => '',
            'recordings' => $recordings,
        ]);
    }

    public function profile()
    {
        $users = new ExtendedUserModel();
        $userModel = new UserModel();

        $user = auth()->user();

        if (! $user) {
            return redirect()->to(site_url('login'))
                ->with('swal', [
                    'icon'  => 'error',
                    'title' => 'SessÃ£o Expirada',
                    'text'  => 'Por favor, faÃ§a login novamente.'
                ]);
        }

        $profileUrl = current_url(false);

        // GET â†’ exibe o perfil
        if ($this->request->getMethod() !== 'POST') {
            return view('pages/student/profile', [
                'user'         => $user,
                'sidebarLinks' => $this->sidebarLinks(),
                'currentUrl'   => $profileUrl,
            ]);
        }

        // ValidaÃ§Ã£o bÃ¡sica
        $rules = [
            'nome'      => 'permit_empty|min_length[2]',
            'email'     => 'permit_empty|valid_email',
            'pais'      => 'permit_empty|max_length[100]',
            'provincia' => 'permit_empty|max_length[100]',
            'cidade'    => 'permit_empty|max_length[100]',
            'telefone'  => 'permit_empty|max_length[20]',
            'imagem'    => 'if_exist|is_image[imagem]|mime_in[imagem,image/jpg,image/jpeg,image/png,image/webp]|max_size[imagem,4096]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to($profileUrl)
                ->withInput()
                ->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Erro!',
                    'text'  => implode(', ', $this->validator->getErrors())
                ]);
        }

        $post = $this->request->getPost();

        // Verificar duplicidade do nome
        $userName = trim($post['nome'] ?? '');
        $email    = trim($post['email'] ?? '');

        if (! empty($userName)) {
            $existingUser = $userModel
                ->where('username', $userName)
                ->where('id !=', $user->id)
                ->first();

            if ($existingUser) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Erro!',
                    'text'  => 'JÃ¡ existe um usuÃ¡rio com esse nome!'
                ]);
            }
        }

        if (! empty($email) && $email !== ($user->email ?? '')) {
            $existingEmail = db_connect()
                ->table('auth_identities')
                ->where('type', 'email_password')
                ->where('secret', $email)
                ->where('user_id !=', $user->id)
                ->get()
                ->getRow();

            if ($existingEmail) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Erro!',
                    'text'  => 'Ja existe um usuario com esse email!'
                ]);
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
                return redirect()->to($profileUrl)->with('swal', [
                    'icon' => 'error',
                    'title' => 'Erro!',
                    'text' => 'Falha ao mover a imagem.'
                ]);
            }

            // Remover imagem antiga se existir
            if (! empty($user->img)) {
                $old = FCPATH . $user->img;
                if (is_file($old)) {
                    @unlink($old);
                }
            }

            $filePath = 'assets/img/' . $newName;
        }

        // Criar payload de atualizaÃ§Ã£o
        $dataProfile = [
            'username' => $userName ?: $user->username,
            'img'      => $filePath,
            'country'  => $post['pais']      ?? $user->country,
            'province' => $post['provincia'] ?? $user->province,
            'city'     => $post['cidade']    ?? $user->city,
            'phone'    => $post['telefone']  ?? $user->phone,
        ];

        // ---------------------------------------
        //    >>> ALTERAÃ‡ÃƒO DE SENHA (SHIELD) <<<
        // ---------------------------------------
        $currentPassword = $post['password_actual'] ?? '';
        $newPassword     = $post['new_password'] ?? '';
        $confirmPassword = $post['confirm_password'] ?? '';
        $emailChanged    = ! empty($email) && $email !== ($user->email ?? '');

        if ($emailChanged) {
            $identity = db_connect()
                ->table('auth_identities')
                ->where('user_id', $user->id)
                ->where('type', 'email_password')
                ->get()
                ->getRow();

            if (! $identity) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Erro interno',
                    'text'  => 'Identidade de senha não encontrada.'
                ]);
            }

            db_connect()
                ->table('auth_identities')
                ->where('id', $identity->id)
                ->update([
                    'secret'     => $email,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        if ($currentPassword || $newPassword || $confirmPassword) {

            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon' => 'error',
                    'title' => 'Erro!',
                    'text' => 'Para alterar a senha, preencha todos os campos.'
                ]);
            }

            // Buscar identidade do Shield
            $identity = db_connect()
                ->table('auth_identities')
                ->where('user_id', $user->id)
                ->where('type', 'email_password')
                ->get()
                ->getRow();

            if (! $identity) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon' => 'error',
                    'title' => 'Erro interno',
                    'text' => 'Identidade de senha nÃ£o encontrada.'
                ]);
            }

            // Verificar senha atual
            if (! password_verify($currentPassword, $identity->secret2)) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon' => 'error',
                    'title' => 'Senha incorreta',
                    'text' => 'A senha atual fornecida estÃ¡ incorreta.'
                ]);
            }

            // Nova senha
            if (strlen($newPassword) < 6) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon' => 'error',
                    'title' => 'Erro!',
                    'text' => 'A nova senha deve ter no mÃ­nimo 6 caracteres.'
                ]);
            }

            if ($newPassword !== $confirmPassword) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon' => 'error',
                    'title' => 'Erro!',
                    'text' => 'A confirmaÃ§Ã£o da senha nÃ£o coincide.'
                ]);
            }

            // Atualizar senha no Shield
            db_connect()
                ->table('auth_identities')
                ->where('id', $identity->id)
                ->update([
                    'secret2'    => password_hash($newPassword, PASSWORD_DEFAULT),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        // Atualizar dados do usuÃ¡rio
        if (! $users->update($user->id, $dataProfile)) {
            return redirect()->to($profileUrl)->with('swal', [
                'icon' => 'error',
                'title' => 'Erro!',
                'text' => implode(', ', $users->errors())
            ]);
        }

        // Atualizar sessÃ£o
        $updated = $users->find($user->id);
        $db = db_connect();
        $studentEmail = strtolower(trim((string) ($emailChanged ? $email : ($user->email ?? ''))));
        $student = $db->table('students')
            ->select('id_student')
            ->where('id_user_student', $user->id)
            ->get()
            ->getRow();

        $studentPayload = [
            'name_student'  => $dataProfile['username'],
            'email_student' => $studentEmail,
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        if ($student) {
            $db->table('students')
                ->where('id_student', $student->id_student)
                ->update($studentPayload);
        } else {
            $db->table('students')->insert($studentPayload + [
                'id_user_student' => $user->id,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);
        }

        auth()->setUser($updated);

        return redirect()->to($profileUrl)->with('swal', [
            'icon'  => 'success',
            'title' => 'Sucesso!',
            'text'  => 'Perfil atualizado com sucesso.'
        ]);
    }

    public function certificate()
    {
        $certificateModel = new CertificateModel();

        $user = service('auth')->user();

        // Garante que certificados de cursos concluÃ­dos fiquem disponÃ­veis imediatamente.
        // Isso cobre casos em que o aluno concluiu o curso mas nÃ£o disparou a geraÃ§Ã£o do PDF.
        $db = db_connect();
        $pending = $db->table('enrollments e')
            ->select('e.id_enrollment')
            ->join('certificates cert', 'cert.id_user_certificate = e.id_student_enrollment AND cert.id_course_certificate = e.id_course_enrollment', 'left')
            ->where('e.id_student_enrollment', (int) $user->id)
            ->where('e.progress_enrollment >=', 100)
            ->groupStart()
                ->where('cert.id_certificate IS NULL', null, false)
                ->orWhere('cert.pdf_path_certificate IS NULL', null, false)
                ->orWhere('cert.pdf_path_certificate', '')
            ->groupEnd()
            ->get()
            ->getResultArray();

        if (!empty($pending)) {
            $svc = new \App\Services\CertificateService($db);
            foreach ($pending as $row) {
                $svc->ensureForEnrollment((int) ($row['id_enrollment'] ?? 0), (int) $user->id);
            }
        }

        $certificates = $certificateModel->getForStudent($user->id);

        return view('pages/student/certificates', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
            'certificates' => $certificates,
        ]);
    }

}


