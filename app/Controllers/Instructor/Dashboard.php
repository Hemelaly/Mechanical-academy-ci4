<?php

namespace App\Controllers\Instructor;

use App\Controllers\BaseController;
use App\Models\CertificateModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CourseModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;
use App\Models\CourseSettingModel;
use App\Models\ExtendedUserModel;
use App\Models\JitsiModel;
use App\Models\JitsiRecordingModel;
use App\Models\PendingUserModel;
use App\Services\EnrollmentNotificationService;
use App\Models\UserNotificationModel;
use App\Libraries\JitsiJwtService;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Authentication\Passwords;
use CodeIgniter\Shield\Models\PasswordResetModel;

class Dashboard extends BaseController
{
    private function makeSlug(string $value): string
    {
        helper(['text', 'url']);

        return url_title(convert_accented_characters($value), '-', true);
    }

    private function normalizeCourseCompat(?object $course): ?object
    {
        if (! $course) {
            return null;
        }

        $learning = trim((string) ($course->learning_course ?? ''));
        $legacyLearning = trim((string) ($course->what_learn_course ?? ''));
        if ($learning === '' && $legacyLearning !== '') {
            $course->learning_course = $legacyLearning;
        }

        return $course;
    }

    private function wantsJson(): bool
    {
        return $this->request->isAJAX()
            || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    private function jsonMessage(string $message, int $statusCode = 200, array $extra = [])
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON(array_merge([
                'message' => $message,
                'csrf' => csrf_hash(),
            ], $extra));
    }

    private function sidebarLinks()
    {
        return [
            ['label' => 'Iní­cio', 'icon' => 'bi-house-door', 'url' => '/instructor/dashboard'],
            ['label' => 'Meus Cursos', 'icon' => 'bi-book', 'url' => '/instructor/dashboard/meus_cursos'],
            ['label' => 'Aula ao Vivo', 'icon' => 'bi-camera-reels', 'url' => '/instructor/dashboard/jitsi'],
            ['label' => 'Estudantes', 'icon' => 'bi-people', 'url' => '/instructor/dashboard/meus_estudantes'],
            ['label' => 'Financas', 'icon' => 'bi-cash-coin', 'url' => '/instructor/dashboard/financas'],
            ['label' => 'Logs', 'icon' => 'bi-journal-text', 'url' => '/instructor/dashboard/logs'],
            ['label' => 'Certificados', 'icon' => 'bi-folder', 'url' => '/instructor/dashboard/certificados'],
            ['label' => 'Perfil', 'icon' => 'bi-person-circle', 'url' => '/instructor/dashboard/perfil'],
        ];
    }

    private function sanitizePreviewReturnUrl(?string $returnUrl, int $courseId): string
    {
        $fallback = site_url('instructor/dashboard/meus_cursos/editar/' . $courseId);
        $returnUrl = trim((string) $returnUrl);

        if ($returnUrl === '') {
            return $fallback;
        }

        $siteBase = rtrim(site_url('/'), '/');
        $publicBase = rtrim(base_url('/'), '/');

        if (
            str_starts_with($returnUrl, $siteBase)
            || str_starts_with($returnUrl, $publicBase)
        ) {
            return $returnUrl;
        }

        if (str_starts_with($returnUrl, '/')) {
            return site_url(ltrim($returnUrl, '/'));
        }

        return $fallback;
    }

    private function buildCoursePreviewUrl(int $courseId, int $lessonId, string $returnUrl): string
    {
        return site_url('instructor/dashboard/cursos/preview/' . $courseId . '?' . http_build_query([
            'lesson' => $lessonId,
            'return_url' => $returnUrl,
        ]));
    }

    private function makeJitsiRoom(string $title): string
    {
        $cfg = config('Jitsi');
        $prefix = trim((string) ($cfg->roomPrefix ?? 'academy'));
        $prefix = $prefix !== '' ? $prefix : 'academy';

        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($title)));
        $slug = trim((string) $slug, '-');
        $slug = $slug !== '' ? substr($slug, 0, 32) : 'live';

        try {
            $suffix = substr(bin2hex(random_bytes(6)), 0, 10);
        } catch (\Throwable $e) {
            $suffix = substr(sha1(uniqid('', true)), 0, 10);
        }

        return $prefix . '-' . $slug . '-' . $suffix;
    }

    private function normalizePaymentMethod(?string $value): string
    {
        $normalized = strtolower(trim((string) $value));

        return match ($normalized) {
            '', 'nao informado' => 'Nao informado',
            'mpesa', 'm-pesa' => 'M-Pesa',
            'comprovativo', 'comprovativo manual', 'manual', 'transferencia', 'transferência' => 'Comprovativo',
            default => trim((string) $value),
        };
    }

    private function formatDashboardTimestamp(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '--';
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return '--';
        }

        $day = date('Y-m-d', $timestamp);
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $time = date('H:i', $timestamp);

        if ($day === $today) {
            return 'Hoje ' . $time;
        }

        if ($day === $yesterday) {
            return 'Ontem ' . $time;
        }

        return date('d/m/Y H:i', $timestamp);
    }

    public function index()
    {
        $user = service('auth')->user();
        $db = db_connect();

        $monthStart = date('Y-m-01 00:00:00');
        $nextMonthStart = date('Y-m-01 00:00:00', strtotime('+1 month'));
        $previousMonthStart = date('Y-m-01 00:00:00', strtotime('-1 month'));
        $weekStart = date('Y-m-d H:i:s', strtotime('-7 days'));

        $totalCourses = (int) $db->table('courses')
            ->where('id_instructor_course', $user->id)
            ->countAllResults();

        $newCoursesThisMonth = (int) $db->table('courses')
            ->where('id_instructor_course', $user->id)
            ->where('created_at >=', $monthStart)
            ->where('created_at <', $nextMonthStart)
            ->countAllResults();

        $activeCourses = (int) $db->table('courses')
            ->where('id_instructor_course', $user->id)
            ->where('status_course', 'Ativo')
            ->countAllResults();

        $totalStudentsRow = $db->table('enrollments e')
            ->select('COUNT(DISTINCT e.id_student_enrollment) AS total', false)
            ->join('courses c', 'c.id_course = e.id_course_enrollment')
            ->where('c.id_instructor_course', $user->id)
            ->get()
            ->getRow();

        $newStudentsThisMonthRow = $db->table('enrollments e')
            ->select('COUNT(DISTINCT e.id_student_enrollment) AS total', false)
            ->join('courses c', 'c.id_course = e.id_course_enrollment')
            ->where('c.id_instructor_course', $user->id)
            ->where('e.created_at >=', $monthStart)
            ->where('e.created_at <', $nextMonthStart)
            ->get()
            ->getRow();

        $monthRevenueRow = $db->table('payments p')
            ->selectSum('p.amount_payment', 'total')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Aprovado')
            ->where('p.created_at >=', $monthStart)
            ->where('p.created_at <', $nextMonthStart)
            ->get()
            ->getRow();

        $previousMonthRevenueRow = $db->table('payments p')
            ->selectSum('p.amount_payment', 'total')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Aprovado')
            ->where('p.created_at >=', $previousMonthStart)
            ->where('p.created_at <', $monthStart)
            ->get()
            ->getRow();

        $pendingRequestsCount = (int) $db->table('payments p')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Pendente')
            ->countAllResults();

        $pendingRequestsThisWeek = (int) $db->table('payments p')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Pendente')
            ->where('p.created_at >=', $weekStart)
            ->countAllResults();

        $totalEnrollments = (int) $db->table('enrollments e')
            ->join('courses c', 'c.id_course = e.id_course_enrollment')
            ->where('c.id_instructor_course', $user->id)
            ->countAllResults();

        $activeEnrollments = (int) $db->table('enrollments e')
            ->join('courses c', 'c.id_course = e.id_course_enrollment')
            ->where('c.id_instructor_course', $user->id)
            ->where('e.status_enrollment', 'ativa')
            ->countAllResults();

        $completedEnrollments = (int) $db->table('enrollments e')
            ->join('courses c', 'c.id_course = e.id_course_enrollment')
            ->where('c.id_instructor_course', $user->id)
            ->where('e.progress_enrollment >=', 100)
            ->countAllResults();

        $featuredCourses = $db->table('courses c')
            ->select([
                'c.id_course',
                'c.title_course',
                'c.status_course',
            ])
            ->select('(SELECT COUNT(*) FROM enrollments e WHERE e.id_course_enrollment = c.id_course) AS student_count', false)
            ->select('(SELECT ROUND(AVG(COALESCE(e.progress_enrollment, 0)), 0) FROM enrollments e WHERE e.id_course_enrollment = c.id_course) AS avg_progress', false)
            ->select('(SELECT COUNT(*) FROM enrollments e WHERE e.id_course_enrollment = c.id_course AND COALESCE(e.progress_enrollment, 0) >= 100) AS completed_count', false)
            ->select('(SELECT COALESCE(SUM(p.amount_payment), 0) FROM payments p WHERE p.id_course_payment = c.id_course AND p.status_payment = "Aprovado") AS revenue_total', false)
            ->where('c.id_instructor_course', $user->id)
            ->orderBy('student_count', 'DESC')
            ->orderBy('revenue_total', 'DESC')
            ->limit(3)
            ->get()
            ->getResult();

        $recentEnrollmentRows = $db->table('enrollments e')
            ->select([
                'e.created_at',
                's.name_student',
                'c.title_course',
            ])
            ->join('courses c', 'c.id_course = e.id_course_enrollment')
            ->join('students s', 's.id_user_student = e.id_student_enrollment', 'left')
            ->where('c.id_instructor_course', $user->id)
            ->orderBy('e.created_at', 'DESC')
            ->limit(4)
            ->get()
            ->getResultArray();

        $recentPaymentRows = $db->table('payments p')
            ->select([
                'p.created_at',
                'p.amount_payment',
                'p.status_payment',
                'p.method_payment',
                'c.title_course',
            ])
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->orderBy('p.created_at', 'DESC')
            ->limit(4)
            ->get()
            ->getResultArray();

        $recentActivities = [];

        foreach ($recentEnrollmentRows as $row) {
            $studentName = trim((string) ($row['name_student'] ?? 'Aluno'));
            $courseTitle = trim((string) ($row['title_course'] ?? 'um curso'));
            $recentActivities[] = [
                'sort_at' => $row['created_at'] ?? null,
                'icon' => 'bi-person-plus',
                'icon_bg' => 'bg-blue-100 dark:bg-blue-900',
                'icon_color' => 'text-blue-600 dark:text-blue-400',
                'message' => $studentName . ' inscreveu-se em ' . $courseTitle,
                'time_label' => $this->formatDashboardTimestamp($row['created_at'] ?? null),
            ];
        }

        foreach ($recentPaymentRows as $row) {
            $status = strtolower(trim((string) ($row['status_payment'] ?? '')));
            $method = $this->normalizePaymentMethod($row['method_payment'] ?? null);
            $amount = number_format((float) ($row['amount_payment'] ?? 0), 2, ',', '.');
            $courseTitle = trim((string) ($row['title_course'] ?? 'um curso'));

            $recentActivities[] = [
                'sort_at' => $row['created_at'] ?? null,
                'icon' => $status === 'aprovado' ? 'bi-cash-coin' : ($status === 'pendente' ? 'bi-hourglass-split' : 'bi-x-circle'),
                'icon_bg' => $status === 'aprovado'
                    ? 'bg-green-100 dark:bg-green-900'
                    : ($status === 'pendente' ? 'bg-amber-100 dark:bg-amber-900' : 'bg-red-100 dark:bg-red-900'),
                'icon_color' => $status === 'aprovado'
                    ? 'text-green-600 dark:text-green-400'
                    : ($status === 'pendente' ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400'),
                'message' => sprintf(
                    'Pagamento %s em %s via %s (%s MZN)',
                    $status !== '' ? $status : 'registado',
                    $courseTitle,
                    $method,
                    $amount
                ),
                'time_label' => $this->formatDashboardTimestamp($row['created_at'] ?? null),
            ];
        }

        usort($recentActivities, static function (array $a, array $b): int {
            return strtotime((string) ($b['sort_at'] ?? '')) <=> strtotime((string) ($a['sort_at'] ?? ''));
        });
        $recentActivities = array_slice($recentActivities, 0, 6);

        $totalStudents = (int) ($totalStudentsRow->total ?? 0);
        $newStudentsThisMonth = (int) ($newStudentsThisMonthRow->total ?? 0);
        $monthRevenue = (float) ($monthRevenueRow->total ?? 0);
        $previousMonthRevenue = (float) ($previousMonthRevenueRow->total ?? 0);
        $monthRevenueDelta = $monthRevenue - $previousMonthRevenue;
        $operationalSummary = [
            [
                'title' => 'Cursos ativos',
                'value' => $activeCourses . ' de ' . $totalCourses,
                'percent' => $totalCourses > 0 ? (int) round(($activeCourses / $totalCourses) * 100) : 0,
                'bar_class' => 'bg-gradient-to-r from-blue-500 to-cyan-500',
            ],
            [
                'title' => 'Matriculas ativas',
                'value' => $activeEnrollments . ' de ' . $totalEnrollments,
                'percent' => $totalEnrollments > 0 ? (int) round(($activeEnrollments / $totalEnrollments) * 100) : 0,
                'bar_class' => 'bg-gradient-to-r from-green-500 to-emerald-500',
            ],
            [
                'title' => 'Cursos concluidos',
                'value' => $completedEnrollments . ' de ' . $totalEnrollments,
                'percent' => $totalEnrollments > 0 ? (int) round(($completedEnrollments / $totalEnrollments) * 100) : 0,
                'bar_class' => 'bg-gradient-to-r from-amber-500 to-orange-500',
            ],
        ];

        return view('pages/instructor/home', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
            'totalCourses' => $totalCourses,
            'newCoursesThisMonth' => $newCoursesThisMonth,
            'totalStudents' => $totalStudents,
            'newStudentsThisMonth' => $newStudentsThisMonth,
            'monthRevenue' => $monthRevenue,
            'monthRevenueDelta' => $monthRevenueDelta,
            'pendingRequestsCount' => $pendingRequestsCount,
            'pendingRequestsThisWeek' => $pendingRequestsThisWeek,
            'featuredCourses' => $featuredCourses,
            'recentActivities' => $recentActivities,
            'operationalSummary' => $operationalSummary,
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
        $courseModel = new CourseModel();
        $moduleModel = new ModuleModel();
        $lessonModel = new LessonModel();
        $projectModel = new \App\Models\ProjectModel();

        $savedDraft = $courseModel
            ->where('id_instructor_course', $user->id)
            ->where('status_course', 'Rascunho')
            ->orderBy('updated_at', 'DESC')
            ->first();
        $loadDraft = $this->request->getGet('load_draft') === '1';
        $draft = $loadDraft ? $savedDraft : null;
        $savedDraft = $this->normalizeCourseCompat($savedDraft);
        $draft = $this->normalizeCourseCompat($draft);

        $draftModules = [];
        $draftProjects = [];
        if ($draft) {
            $draftModules = $moduleModel
                ->where('id_course_module', $draft->id_course)
                ->orderBy('position_module')
                ->findAll();

            foreach ($draftModules as &$m) {
                $m->lessons = $lessonModel
                    ->where('id_module_lesson', $m->id_module)
                    ->orderBy('position_lesson')
                    ->findAll();
            }
            unset($m);

            $draftProjects = $projectModel
                ->where('id_course_project', $draft->id_course)
                ->findAll();
        }

        return view('pages/instructor/add_course', [
            'user' => $user,
            'draft' => $draft,
            'savedDraft' => $savedDraft,
            'draftModules' => $draftModules,
            'draftProjects' => $draftProjects,
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
        $projectModel = new \App\Models\ProjectModel();

        if (!$id) {
            return redirect()->to('instructor/dashboard/meus_cursos')
                ->with('error', 'ID do curso nÃƒÂ£o fornecido');
        }

        $course = $courseModel->find($id);
        $course = $this->normalizeCourseCompat($course);

        if (!$course) {
            return redirect()->to('instructor/dashboard/meus_cursos')
                ->with('error', 'Curso nÃƒÂ£o encontrado');
        }

        if ($course->id_instructor_course != auth()->id()) {
            return redirect()->to('instructor/dashboard/meus_cursos')
                ->with('error', 'Acesso negado');
        }

        // Ã°Å¸â€Â¹ Carregar mÃƒÂ³dulos e aulas
        $modules = $moduleModel
            ->where('id_course_module', $id)
            ->orderBy('position_module')
            ->findAll();

        foreach ($modules as &$m) {
            $m->lessons = $lessonModel
                ->where('id_module_lesson', $m->id_module)
                ->orderBy('position_lesson')
                ->findAll();
        }

        $projects = $projectModel
            ->where('id_course_project', $id)
            ->findAll();

        $db = db_connect();
        $enrolledCount = (int) $db->table('enrollments')
            ->where('id_course_enrollment', (int) $id)
            ->where('status_enrollment', 'ativa')
            ->countAllResults();

        // Ã¢Å“â€¦ APENAS retorna a view
        return view('pages/instructor/edit_course', [
            'user'         => $user,
            'course'       => $course,
            'modules'      => $modules,
            'projects'     => $projects,
            'enrolledCount' => $enrolledCount,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl'   => current_url()
        ]);
    }

    public function course_preview($id)
    {
        $user = service('auth')->user();
        $courseModel = new CourseModel();
        $moduleModel = new ModuleModel();
        $lessonModel = new LessonModel();

        $courseId = (int) $id;
        $course = $this->normalizeCourseCompat($courseModel->find($courseId));

        if (! $course) {
            return redirect()->to('instructor/dashboard/meus_cursos')
                ->with('error', 'Curso nao encontrado.');
        }

        if ((int) ($course->id_instructor_course ?? 0) !== (int) auth()->id()) {
            return redirect()->to('instructor/dashboard/meus_cursos')
                ->with('error', 'Acesso negado.');
        }

        $modules = $moduleModel
            ->where('id_course_module', $courseId)
            ->orderBy('position_module')
            ->findAll();

        $orderedLessonIds = [];
        $orderedLessons = [];
        $lessonSlugById = [];
        $moduleIndexByLessonId = [];

        foreach ($modules as $moduleIndex => &$module) {
            $module->lessons = $lessonModel
                ->where('id_module_lesson', $module->id_module)
                ->orderBy('position_lesson')
                ->findAll();

            foreach ($module->lessons as $moduleLesson) {
                $lessonId = (int) $moduleLesson->id_lesson;
                $orderedLessonIds[] = $lessonId;
                $orderedLessons[$lessonId] = $moduleLesson;
                $lessonSlugById[$lessonId] = $this->makeSlug((string) $moduleLesson->title_lesson);
                $moduleIndexByLessonId[$lessonId] = $moduleIndex;
            }
        }
        unset($module);

        $returnUrl = $this->sanitizePreviewReturnUrl($this->request->getGet('return_url'), $courseId);

        if (empty($orderedLessonIds)) {
            return redirect()->to($returnUrl)
                ->with('error', 'Adicione pelo menos uma aula para abrir a pre-visualizacao.');
        }

        $selectedLessonId = (int) ($this->request->getGet('lesson') ?? 0);
        if (! isset($orderedLessons[$selectedLessonId])) {
            $selectedLessonId = $orderedLessonIds[0];
        }

        $lesson = $orderedLessons[$selectedLessonId];
        $currentIndex = array_search($selectedLessonId, $orderedLessonIds, true);
        $prevLesson = ($currentIndex !== false && $currentIndex > 0)
            ? $orderedLessonIds[$currentIndex - 1]
            : null;
        $nextLesson = ($currentIndex !== false && $currentIndex < count($orderedLessonIds) - 1)
            ? $orderedLessonIds[$currentIndex + 1]
            : null;

        $nextModuleLessonId = null;
        $currentModuleIndex = $moduleIndexByLessonId[$selectedLessonId] ?? null;
        if ($currentModuleIndex !== null) {
            $nextModule = $modules[$currentModuleIndex + 1] ?? null;
            if ($nextModule && ! empty($nextModule->lessons)) {
                $nextModuleLessonId = (int) $nextModule->lessons[0]->id_lesson;
            }
        }

        $previewUrlsByLessonId = [];
        foreach ($orderedLessonIds as $lessonId) {
            $previewUrlsByLessonId[$lessonId] = $this->buildCoursePreviewUrl($courseId, $lessonId, $returnUrl);
        }

        $enrollment = (object) [
            'id_enrollment' => 0,
            'status_enrollment' => 'ativa',
            'progress_enrollment' => 0,
            'completed_enrollment' => null,
        ];

        return view('pages/student/lessons', [
            'course' => $course,
            'enrollment' => $enrollment,
            'accessBlocked' => false,
            'modules' => $modules,
            'lesson' => $lesson,
            'prevLesson' => $prevLesson,
            'nextLesson' => $nextLesson,
            'courseSlug' => $this->makeSlug((string) $course->title_course),
            'prevLessonSlug' => $prevLesson ? ($lessonSlugById[$prevLesson] ?? null) : null,
            'nextLessonSlug' => $nextLesson ? ($lessonSlugById[$nextLesson] ?? null) : null,
            'nextModuleLessonId' => $nextModuleLessonId,
            'nextModuleLessonSlug' => $nextModuleLessonId ? ($lessonSlugById[$nextModuleLessonId] ?? null) : null,
            'lessonSlugById' => $lessonSlugById,
            'completedLessonIds' => [],
            'quizScore' => null,
            'certificateInfo' => [
                'completedAt' => null,
                'availableAt' => null,
                'pdfReady' => false,
            ],
            'previewMode' => true,
            'previewBackUrl' => $returnUrl,
            'previewUrlsByLessonId' => $previewUrlsByLessonId,
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
            'playerBackUrl' => $returnUrl,
            'playerTitle' => (string) ($lesson->title_lesson ?? 'Pré-visualização'),
            'playerSubtitle' => (string) ($course->title_course ?? ''),
        ]);
    }

    public function live($id = null)
    {
        $user  = service('auth')->user();
        $req   = $this->request;
        $model = new JitsiModel();
        $courseModel = new CourseModel();
        $recordingModel = new JitsiRecordingModel();
        if ($req->getMethod() === 'GET') {
            $aulas = $model->where('id_user_jitsi', $user->id)
                ->orderBy('id_jitsi', 'DESC')
                ->findAll();
            $recordingStats = [];
            $aulaIds = array_map(static fn($a) => (int) $a->id_jitsi, $aulas);
            if (! empty($aulaIds)) {
                $stats = $recordingModel
                    ->select('id_jitsi_session, COUNT(*) as total_recordings, SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published_recordings', false)
                    ->whereIn('id_jitsi_session', $aulaIds)
                    ->groupBy('id_jitsi_session')
                    ->findAll();
                foreach ($stats as $s) {
                    $recordingStats[(int) $s->id_jitsi_session] = [
                        'total' => (int) ($s->total_recordings ?? 0),
                        'published' => (int) ($s->published_recordings ?? 0),
                    ];
                }
            }
            $courses = $courseModel->getCoursesByInstructor($user->id);
            return view('pages/instructor/live_class', [
                'user'         => $user,
                'sidebarLinks' => $this->sidebarLinks(),
                'currentUrl'   => current_url(),
                'aulas'        => $aulas,
                'courses'      => $courses,
                'recordingStats' => $recordingStats,
            ]);
        }
        $editId = (int) ($req->getPost('id_jitsi') ?: $id ?: 0);
        $isEdit = $editId > 0;
        $existingAula = null;
        if ($isEdit) {
            $existingAula = $model->find($editId);
            if (! $existingAula || (int) $existingAula->id_user_jitsi !== (int) $user->id) {
                return redirect()->to('/instructor/dashboard/jitsi')->with('error', 'Aula nao encontrada ou sem permissao.');
            }
        }
        $rules = [
            'classTitle' => [
                'label'  => 'Titulo da Aula',
                'rules'  => 'required|min_length[3]|max_length[255]',
            ],
            'classDescription' => [
                'label'  => 'Descricao',
                'rules'  => 'permit_empty|max_length[65535]',
            ],
            'associatedCourse' => [
                'label'  => 'Curso Associado',
                'rules'  => 'permit_empty|integer',
            ],
            'classType' => [
                'label' => 'Tipo de Aula',
                'rules' => 'required|in_list[instant,scheduled]',
            ],
            'roomStatus' => [
                'label' => 'Estado',
                'rules' => 'required|in_list[Pendente,Ao vivo,Expirado]',
            ],
            'roomPrivacy' => [
                'label' => 'Privacidade',
                'rules' => 'required|in_list[public,private,password]',
            ],
            'roomPassword' => [
                'label' => 'Senha da Sala',
                'rules' => 'permit_empty|min_length[4]|max_length[100]',
            ],
        ];
        if ($req->getPost('classType') === 'scheduled') {
            $rules['classDate'] = [
                'label' => 'Data da Aula',
                'rules' => 'required|valid_date[Y-m-d]',
            ];
            $rules['startTime'] = [
                'label' => 'Hora de Inicio',
                'rules' => 'required',
            ];
            $rules['endTime'] = [
                'label' => 'Hora de Termino',
                'rules' => 'required',
            ];
        }
        $roomPassword = trim((string) $req->getPost('roomPassword'));
        if ($req->getPost('roomPrivacy') === 'password' && (! $isEdit || $roomPassword !== '')) {
            $rules['roomPassword']['rules'] = 'required|min_length[4]|max_length[100]';
        }
        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        $courseId = (int) ($req->getPost('associatedCourse') ?: 0);
        if ($courseId > 0) {
            $ownedCourse = $courseModel
                ->where('id_course', $courseId)
                ->where('id_instructor_course', $user->id)
                ->first();
            if (! $ownedCourse) {
                return redirect()->back()->withInput()->with('error', 'Curso associado invalido.');
            }
        }
        $data = [
            'title_jitsi'        => trim((string) $req->getPost('classTitle')),
            'description_jitsi'  => trim((string) $req->getPost('classDescription')),
            'id_course_jitsi'    => $courseId > 0 ? $courseId : null,
            'class_type_jitsi'   => (string) $req->getPost('classType'),
            'meeting_date_jitsi' => $req->getPost('classDate') ?: null,
            'start_time_jitsi'   => $req->getPost('startTime') ?: null,
            'end_time_jitsi'     => $req->getPost('endTime') ?: null,
            'status_jitsi'       => (string) $req->getPost('roomStatus'),
            'privacy_jitsi'      => (string) $req->getPost('roomPrivacy'),
            'recording_jitsi'    => $req->getPost('enableRecording') ? 1 : 0,
            'chat_jitsi'         => $req->getPost('enableChat') ? 1 : 0,
            'screenshare_jitsi'  => $req->getPost('enableScreenShare') ? 1 : 0,
            'id_user_jitsi'      => $user->id,
        ];
        if ((string) $req->getPost('roomPrivacy') === 'password') {
            if ($roomPassword !== '') {
                $data['password_jitsi'] = password_hash($roomPassword, PASSWORD_DEFAULT);
            } elseif (! $isEdit) {
                $data['password_jitsi'] = null;
            }
        } else {
            $data['password_jitsi'] = null;
        }
        if (! $isEdit) {
            $data['room_jitsi'] = $this->makeJitsiRoom((string) $req->getPost('classTitle'));
        }
        if ($isEdit) {
            $model->update($editId, $data);
            return redirect()
                ->to('/instructor/dashboard/jitsi')
                ->with('swal', [
                    'icon'  => 'success',
                    'title' => 'Aula atualizada!',
                    'text'  => 'A aula foi editada com sucesso.'
                ]);
        }
        $model->insert($data);
        return redirect()
            ->to('/instructor/dashboard/jitsi')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Aula criada!',
                'text'  => 'A nova aula virtual foi criada com sucesso.'
            ]);
    }

    public function deleteJitsi($id)
    {
        $id = (int) $id;
        $user  = service('auth')->user();
        $model = new JitsiModel();

        if ($id <= 0) {
            return redirect()->back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Aula invalida',
                'text'  => 'Identificador da aula invalido.'
            ]);
        }

        // Verifica se a aula existe
        $aula = $model->find($id);
        if (!$aula) {
            $this->auditLogger->write(
                'instructor.jitsi.delete_not_found',
                'warning',
                'Tentativa de excluir aula inexistente.',
                [
                    'jitsi_id' => (int) $id,
                ]
            );
            return redirect()->back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Aula nao encontrada',
                'text'  => 'A aula que tentou excluir nao existe.'
            ]);
        }
        // Impede que um instrutor exclua aula de outro instrutor
        if ($aula->id_user_jitsi != $user->id) {
            $this->auditLogger->write(
                'instructor.jitsi.delete_denied',
                'warning',
                'Tentativa de excluir aula de outro instrutor.',
                [
                    'jitsi_id' => (int) $id,
                    'owner_user_id' => (int) $aula->id_user_jitsi,
                ]
            );
            return redirect()->back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Acesso negado',
                'text'  => 'Voce nao tem permissao para excluir esta aula.'
            ]);
        }
        // Delete
        if ($model->where('id_jitsi', $id)->delete()) {
            $this->auditLogger->write(
                'instructor.jitsi.deleted',
                'info',
                'Aula ao vivo removida.',
                [
                    'jitsi_id' => (int) $id,
                    'owner_user_id' => (int) $aula->id_user_jitsi,
                ]
            );
            return redirect()
                ->to('/instructor/dashboard/jitsi')
                ->with('swal', [
                    'icon'  => 'success',
                    'title' => 'Aula excluida!',
                    'text'  => 'A aula foi removida com sucesso.'
                ]);
        }
        // Caso ocorra algum erro inesperado
        $this->auditLogger->write(
            'instructor.jitsi.delete_failed',
            'error',
            'Falha ao excluir aula ao vivo.',
            [
                'jitsi_id' => (int) $id,
                'owner_user_id' => (int) $aula->id_user_jitsi,
            ]
        );
        return redirect()
            ->back()
            ->with('swal', [
                'icon'  => 'error',
                'title' => 'Erro ao excluir',
                'text'  => 'Nao foi possivel excluir esta aula.'
            ]);
    }

    public function stream($id)
    {
        $user  = service('auth')->user();
        $model = new JitsiModel();
        $recordingModel = new JitsiRecordingModel();
        /** @var JitsiJwtService $jitsiJwt */
        $jitsiJwt = service('jitsiJwt');
        $aula = $model->find($id);
        if (! $aula) {
            return redirect()->back()->with('swal', [
                'icon' => 'error',
                'title' => 'Aula nao encontrada',
                'text' => 'A aula que tentou acessar nao existe.'
            ]);
        }
        if ((int) $aula->id_user_jitsi !== (int) $user->id) {
            return redirect()->back()->with('swal', [
                'icon' => 'error',
                'title' => 'Sem permissao',
                'text' => 'Voce nao pode acessar esta sala.'
            ]);
        }
        $model->update((int) $aula->id_jitsi, ['status_jitsi' => 'Ao vivo']);
        $aula = $model->find($id);
        $displayName = trim((string) ($user->username ?? $user->name ?? ('Instrutor ' . $user->id)));
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
                true,
                [
                    'recording' => (bool) $aula->recording_jitsi,
                    'screen-sharing' => (bool) $aula->screenshare_jitsi,
                ]
            );
        } catch (\Throwable $e) {
            log_message('warning', 'Falha ao gerar JWT do Jitsi: {message}', ['message' => $e->getMessage()]);
        }
        $recordings = $recordingModel
            ->where('id_jitsi_session', (int) $aula->id_jitsi)
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
            'canModerate' => true,
            'canManageRecordings' => true,
            'backUrl' => site_url('instructor/dashboard/jitsi'),
            'endStreamUrl' => site_url('instructor/dashboard/jitsi/stream/' . (int) $aula->id_jitsi . '/end'),
            'saveRecordingUrl' => site_url('instructor/dashboard/jitsi/stream/' . (int) $aula->id_jitsi . '/recording'),
            'publishToggleBaseUrl' => site_url('instructor/dashboard/jitsi/recordings'),
            'recordings' => $recordings,
        ]);
    }

    public function endStream($id)
    {
        $user  = service('auth')->user();
        $model = new JitsiModel();
        $aula = $model->find($id);
        if (! $aula || (int) $aula->id_user_jitsi !== (int) $user->id) {
            return redirect()->to('/instructor/dashboard/jitsi')->with('error', 'Aula nao encontrada ou sem permissao.');
        }
        $model->update((int) $aula->id_jitsi, ['status_jitsi' => 'Expirado']);
        return redirect()->to('/instructor/dashboard/jitsi')->with('swal', [
            'icon' => 'success',
            'title' => 'Aula encerrada',
            'text' => 'A transmissao foi encerrada com sucesso.',
        ]);
    }

    public function storeRecording($id)
    {
        $user  = service('auth')->user();
        $jitsiModel = new JitsiModel();
        $recordingModel = new JitsiRecordingModel();
        $aula = $jitsiModel->find($id);
        if (! $aula || (int) $aula->id_user_jitsi !== (int) $user->id) {
            if ($this->wantsJson()) {
                return $this->jsonMessage('Aula nao encontrada ou sem permissao.', 403);
            }
            return redirect()->to('/instructor/dashboard/jitsi')->with('error', 'Aula nao encontrada ou sem permissao.');
        }
        $rules = [
            'recording_url' => 'required|valid_url|max_length[2048]',
            'provider_recording_id' => 'permit_empty|max_length[255]',
            'recording_mode' => 'required|in_list[file,stream,local,manual]',
            'status_recording' => 'required|in_list[pending,processing,ready,failed]',
            'duration_seconds' => 'permit_empty|integer',
            'publish_now' => 'permit_empty|in_list[0,1,on]',
        ];
        if (! $this->validate($rules)) {
            if ($this->wantsJson()) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'message' => 'Dados invalidos para gravacao.',
                        'errors' => $this->validator->getErrors(),
                        'csrf' => csrf_hash(),
                    ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $publishNow = in_array((string) $this->request->getPost('publish_now'), ['1', 'on'], true);
        $providerId = trim((string) $this->request->getPost('provider_recording_id'));
        $data = [
            'id_jitsi_session' => (int) $aula->id_jitsi,
            'recording_url' => trim((string) $this->request->getPost('recording_url')),
            'provider_recording_id' => $providerId !== '' ? $providerId : null,
            'recording_mode' => (string) $this->request->getPost('recording_mode'),
            'status_recording' => (string) $this->request->getPost('status_recording'),
            'duration_seconds' => $this->request->getPost('duration_seconds') !== null && $this->request->getPost('duration_seconds') !== ''
                ? (int) $this->request->getPost('duration_seconds')
                : null,
            'is_published' => $publishNow ? 1 : 0,
            'published_at' => $publishNow ? date('Y-m-d H:i:s') : null,
        ];
        if ($providerId !== '') {
            $existing = $recordingModel
                ->where('id_jitsi_session', (int) $aula->id_jitsi)
                ->where('provider_recording_id', $providerId)
                ->first();
            if ($existing) {
                $recordingModel->update((int) $existing->id_jitsi_recording, $data);
                $recordingId = (int) $existing->id_jitsi_recording;
            } else {
                $recordingModel->insert($data);
                $recordingId = (int) $recordingModel->getInsertID();
            }
        } else {
            $recordingModel->insert($data);
            $recordingId = (int) $recordingModel->getInsertID();
        }
        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'message' => 'Gravacao salva.',
                'recording_id' => $recordingId,
                'csrf' => csrf_hash(),
            ]);
        }
        return redirect()->back()->with('swal', [
            'icon' => 'success',
            'title' => 'Gravacao salva',
            'text' => 'O link da gravacao foi guardado.',
        ]);
    }

    public function toggleRecordingPublish($recordingId)
    {
        $user  = service('auth')->user();
        $db = db_connect();
        $row = $db->table('jitsi_recordings r')
            ->select('r.id_jitsi_recording, r.is_published, r.id_jitsi_session, j.id_user_jitsi')
            ->join('jitsi j', 'j.id_jitsi = r.id_jitsi_session')
            ->where('r.id_jitsi_recording', (int) $recordingId)
            ->get()
            ->getRow();
        if (! $row || (int) $row->id_user_jitsi !== (int) $user->id) {
            return redirect()->back()->with('error', 'Gravacao nao encontrada ou sem permissao.');
        }
        $publish = ! (bool) $row->is_published;
        $recordingModel = new JitsiRecordingModel();
        $recordingModel->update((int) $row->id_jitsi_recording, [
            'is_published' => $publish ? 1 : 0,
            'published_at' => $publish ? date('Y-m-d H:i:s') : null,
        ]);
        return redirect()->back()->with('swal', [
            'icon' => 'success',
            'title' => $publish ? 'Gravacao publicada' : 'Gravacao despublicada',
            'text' => $publish
                ? 'Os alunos ja podem ver esta gravacao.'
                : 'A gravacao foi ocultada dos alunos.',
        ]);
    }

    public function students()
    {
        $user = service('auth')->user();

        $db = db_connect();
        $courses = $db->table('courses')
            ->select('id_course, title_course, price_course')
            ->where('id_instructor_course', $user->id)
            ->orderBy('title_course', 'ASC')
            ->get()
            ->getResultArray();

        $enrollmentModel = new \App\Models\EnrollmentModel();

        $enrollments = $enrollmentModel
            ->getInstructorEnrollments($user->id)
            ->paginate(5, 'enrollments');

        $paymentModel = new \App\Models\PaymentModel();
        $payments = $paymentModel->getInstructorPendingPayments($user->id);

        return view('pages/instructor/students', [
            'user' => $user,
            'payments' => $payments,
            'enrollments' => $enrollments,
            'pager' => $enrollmentModel->pager,
            'courses' => $courses,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
        ]);
    }

    public function manualEnroll()
    {
        $actualUser = service('auth')->user();
        $db = db_connect();

        $courseId = (int) $this->request->getPost('course_id');
        $studentLookup = trim((string) $this->request->getPost('student'));

        if ($courseId <= 0 || $studentLookup === '') {
            return $this->jsonMessage('Informe o curso e o aluno.', 422);
        }

        $course = $db->table('courses')
            ->select('id_course, id_instructor_course')
            ->where('id_course', $courseId)
            ->get()
            ->getRow();

        if (! $course || (int) ($course->id_instructor_course ?? 0) !== (int) $actualUser->id) {
            $this->auditLogger->write(
                'instructor.enrollment.manual_denied',
                'warning',
                'Tentativa de matricular aluno em curso fora do instrutor.',
                ['course_id' => $courseId, 'student_lookup' => $studentLookup]
            );
            return $this->jsonMessage('Curso invalido ou sem permissao.', 403);
        }

        $userId = null;
        if (ctype_digit($studentLookup)) {
            $userId = (int) $studentLookup;
        } else {
            $studentRow = $db->table('students')
                ->select('id_user_student')
                ->where('email_student', $studentLookup)
                ->get()
                ->getRow();

            if ($studentRow) {
                $userId = (int) $studentRow->id_user_student;
            } else {
                $identity = $db->table('auth_identities')
                    ->select('user_id')
                    ->where('type', 'email_password')
                    ->where('secret', $studentLookup)
                    ->get()
                    ->getRow();

                if ($identity) {
                    $userId = (int) $identity->user_id;
                }
            }
        }

        if (! $userId) {
            return $this->jsonMessage('Aluno nao encontrado (use email ou ID do aluno).', 404);
        }

        $userRow = $db->table('users')->select('id, role')->where('id', $userId)->get()->getRow();
        if (! $userRow || strtolower((string) ($userRow->role ?? '')) !== 'student') {
            return $this->jsonMessage('O usuario informado nao e um estudante.', 422);
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $existing = $enrollmentModel
            ->where('id_student_enrollment', $userId)
            ->where('id_course_enrollment', $courseId)
            ->first();

        if ($existing) {
            $updates = array_merge([
                'status_enrollment'    => 'ativa',
                'is_manual_enrollment' => 1,
            ], (new \App\Services\DemoEnrollmentService())->clearedDemoPayload());
            if (empty($existing->enrolled_at_enrollment)) {
                $updates['enrolled_at_enrollment'] = date('Y-m-d');
            }

            $enrollmentModel->update((int) $existing->id_enrollment, $updates);

            $this->auditLogger->write(
                'instructor.enrollment.manual_exists',
                'info',
                'Matricula manual solicitada para aluno ja matriculado.',
                ['course_id' => $courseId, 'student_id' => $userId, 'enrollment_id' => (int) $existing->id_enrollment]
            );

            return $this->jsonMessage('Aluno ja estava matriculado (matricula reativada se necessario).');
        }

        $inserted = $enrollmentModel->insert(array_merge([
            'id_course_enrollment'   => $courseId,
            'id_student_enrollment'  => $userId,
            'status_enrollment'      => 'ativa',
            'progress_enrollment'    => 0,
            'enrolled_at_enrollment' => date('Y-m-d'),
            'is_manual_enrollment'   => 1,
        ], (new \App\Services\DemoEnrollmentService())->clearedDemoPayload()), true);

        if ($inserted === false) {
            return $this->jsonMessage(implode(', ', $enrollmentModel->errors() ?: ['Falha ao criar matricula.']), 422);
        }

        $enrollmentId = (int) $enrollmentModel->getInsertID();

        $this->auditLogger->write(
            'instructor.enrollment.manual_created',
            'info',
            'Matricula manual criada pelo instrutor.',
            ['course_id' => $courseId, 'student_id' => $userId, 'enrollment_id' => $enrollmentId]
        );

        (new EnrollmentNotificationService())->notifyStudentAboutEnrollment($enrollmentId, 'manual');

        return $this->jsonMessage('Aluno matriculado com sucesso.');
    }

    /**
     * Concede acesso demo (não pago) que expira 2h após o primeiro acesso às aulas.
     */
    public function grantDemoAccess()
    {
        try {
            $actualUser = service('auth')->user();
            $db = db_connect();

            $courseId = (int) $this->request->getPost('course_id');
            $studentLookup = trim((string) $this->request->getPost('student'));
            $enrollmentId = (int) $this->request->getPost('enrollment_id');

            $demo = new \App\Services\DemoEnrollmentService();
            if (! $demo->demoFieldsReady()) {
                return $this->jsonMessage(
                    'Campos de demo em falta na base de dados. No servidor execute: php spark migrate',
                    503
                );
            }

            if ($enrollmentId > 0) {
                $row = $db->table('enrollments e')
                    ->select('e.id_enrollment, e.id_student_enrollment, e.id_course_enrollment, c.id_instructor_course, c.price_course')
                    ->join('courses c', 'c.id_course = e.id_course_enrollment')
                    ->where('e.id_enrollment', $enrollmentId)
                    ->get()
                    ->getRow();

                if (! $row || (int) ($row->id_instructor_course ?? 0) !== (int) $actualUser->id) {
                    return $this->jsonMessage('Matrícula inválida ou sem permissão.', 403);
                }

                if ((float) ($row->price_course ?? 0) <= 0) {
                    return $this->jsonMessage('Acesso demo é para cursos pagos (não pagos usam inscrição normal).', 422);
                }

                $result = $demo->grant((int) $row->id_student_enrollment, (int) $row->id_course_enrollment);

                $this->auditLogger->write(
                    'instructor.enrollment.demo_granted',
                    $result['ok'] ? 'info' : 'warning',
                    $result['message'],
                    ['enrollment_id' => $enrollmentId, 'course_id' => (int) $row->id_course_enrollment]
                );

                return $this->jsonMessage($result['message'], $result['ok'] ? 200 : 422);
            }

            if ($courseId <= 0 || $studentLookup === '') {
                return $this->jsonMessage('Informe o curso e o aluno.', 422);
            }

            $course = $db->table('courses')
                ->select('id_course, id_instructor_course, price_course')
                ->where('id_course', $courseId)
                ->get()
                ->getRow();

            if (! $course || (int) ($course->id_instructor_course ?? 0) !== (int) $actualUser->id) {
                return $this->jsonMessage('Curso inválido ou sem permissão.', 403);
            }

            if ((float) ($course->price_course ?? 0) <= 0) {
                return $this->jsonMessage('Acesso demo é para cursos pagos.', 422);
            }

            $userId = null;
            if (ctype_digit($studentLookup)) {
                $userId = (int) $studentLookup;
            } else {
                $studentRow = $db->table('students')
                    ->select('id_user_student')
                    ->where('email_student', $studentLookup)
                    ->get()
                    ->getRow();

                if ($studentRow) {
                    $userId = (int) $studentRow->id_user_student;
                } else {
                    $identity = $db->table('auth_identities')
                        ->select('user_id')
                        ->where('type', 'email_password')
                        ->where('secret', $studentLookup)
                        ->get()
                        ->getRow();

                    if ($identity) {
                        $userId = (int) $identity->user_id;
                    }
                }
            }

            if (! $userId) {
                return $this->jsonMessage('Aluno não encontrado (use email ou ID).', 404);
            }

            $userRow = $db->table('users')->select('id, role')->where('id', $userId)->get()->getRow();
            if (! $userRow || strtolower((string) ($userRow->role ?? '')) !== 'student') {
                return $this->jsonMessage('O utilizador informado não é um estudante.', 422);
            }

            $result = $demo->grant($userId, $courseId);

            $this->auditLogger->write(
                'instructor.enrollment.demo_granted',
                $result['ok'] ? 'info' : 'warning',
                $result['message'],
                ['course_id' => $courseId, 'student_id' => $userId]
            );

            return $this->jsonMessage($result['message'], $result['ok'] ? 200 : 422);
        } catch (\Throwable $e) {
            log_message('error', 'grantDemoAccess failed: ' . $e->getMessage());

            return $this->jsonMessage('Falha ao conceder demo: ' . $e->getMessage(), 500);
        }
    }

    public function studentsData()
    {
        $user = service('auth')->user();
        $search = trim((string) $this->request->getGet('q'));
        $status = strtolower((string) $this->request->getGet('status'));
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = (int) $this->request->getGet('per_page');
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min(max($perPage, 5), 50);
        $offset = ($page - 1) * $perPage;

        $db = db_connect();
        $demoReady = (new \App\Services\DemoEnrollmentService())->demoFieldsReady();

        $select = [
            'e.id_enrollment',
            'e.status_enrollment',
            'e.progress_enrollment',
            'e.enrolled_at_enrollment',
            'e.updated_at AS last_enrollment_update',
            's.name_student',
            's.email_student',
            'c.title_course',
            'c.price_course',
        ];

        if ($demoReady) {
            $select[] = 'e.is_demo_enrollment';
            $select[] = 'e.demo_started_at';
            $select[] = 'e.demo_expires_at';
        }

        try {
            $builder = $db->table('enrollments e')
                ->select($select)
                ->select('(SELECT MAX(COALESCE(p.updated_at, p.created_at, p.completed_at_progress)) FROM progress p WHERE p.id_enrollment_progress = e.id_enrollment) AS last_activity', false)
                ->join('courses c', 'c.id_course = e.id_course_enrollment')
                ->join('students s', 's.id_user_student = e.id_student_enrollment')
                ->where('c.id_instructor_course', $user->id);

            if ($search !== '') {
                $builder->groupStart()
                    ->like('s.name_student', $search)
                    ->orLike('s.email_student', $search)
                    ->orLike('c.title_course', $search)
                    ->groupEnd();
            }

            if ($status !== '') {
                $builder->where('e.status_enrollment', $status);
            }

            $countBuilder = clone $builder;
            $total = (int) $countBuilder->countAllResults();

            $rows = $builder
                ->orderBy('e.updated_at', 'DESC')
                ->limit($perPage, $offset)
                ->get()
                ->getResultArray();

            if (! $demoReady) {
                foreach ($rows as &$row) {
                    $row['is_demo_enrollment'] = 0;
                    $row['demo_started_at'] = null;
                    $row['demo_expires_at'] = null;
                }
                unset($row);
            }

            $totalPages = (int) ceil($total / $perPage);

            return $this->response->setJSON([
                'items' => $rows,
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => $totalPages,
                ],
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'studentsData failed: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'items' => [],
                    'pagination' => [
                        'page' => $page,
                        'per_page' => $perPage,
                        'total' => 0,
                        'total_pages' => 0,
                    ],
                    'message' => 'Erro ao carregar alunos: ' . $e->getMessage(),
                ]);
        }
    }

    public function pendingPaymentsData()
    {
        $user = service('auth')->user();
        $search = trim((string) $this->request->getGet('q'));
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = (int) $this->request->getGet('per_page');
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min(max($perPage, 5), 50);
        $offset = ($page - 1) * $perPage;

        $db = db_connect();
        $builder = $db->table('payments p')
            ->select([
                'p.id_payment',
                'p.status_payment',
                'p.proof_file_payment',
                'p.created_at',
                'pu.id AS id_user_payment',
                'pu.username',
                'pu.email',
                'c.id_course',
                'c.title_course',
            ])
            ->join('pending_users pu', 'pu.id = p.id_user_payment')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Pendente');

        if ($search !== '') {
            $builder->groupStart()
                ->like('pu.username', $search)
                ->orLike('pu.email', $search)
                ->orLike('c.title_course', $search)
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $total = (int) $countBuilder->countAllResults();

        $rows = $builder
            ->orderBy('p.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $totalPages = (int) ceil($total / $perPage);

        return $this->response->setJSON([
            'items' => $rows,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ]);
    }

    public function toggleEnrollment($enrollmentId)
    {
        $user = service('auth')->user();
        $enrollmentId = (int) $enrollmentId;

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $row = $enrollmentModel
            ->select('enrollments.id_enrollment, enrollments.status_enrollment, courses.id_instructor_course')
            ->join('courses', 'courses.id_course = enrollments.id_course_enrollment')
            ->where('enrollments.id_enrollment', $enrollmentId)
            ->get()
            ->getRow();

        if (! $row || (int) $row->id_instructor_course !== (int) $user->id) {
            $this->auditLogger->write(
                'instructor.enrollment.toggle_denied',
                'warning',
                'Tentativa de alterar matricula sem permissao.',
                [
                    'enrollment_id' => $enrollmentId,
                ]
            );

            if ($this->wantsJson()) {
                return $this->jsonMessage('Acesso negado.', 403);
            }
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $currentStatus = strtolower((string) $row->status_enrollment);
        $newStatus = $currentStatus === 'ativa' ? 'cancelada' : 'ativa';
        $updated = $enrollmentModel->update($enrollmentId, ['status_enrollment' => $newStatus]);

        $this->auditLogger->write(
            'instructor.enrollment.toggled',
            $updated ? 'info' : 'error',
            $updated
                ? 'Status de matricula alterado.'
                : 'Falha ao alterar status de matricula.',
            [
                'enrollment_id' => $enrollmentId,
                'from_status' => $currentStatus,
                'to_status' => $newStatus,
            ]
        );

        $msg = $newStatus === 'ativa'
            ? 'Acesso do aluno liberado.'
            : 'Acesso do aluno bloqueado.';

        if ($this->wantsJson()) {
            return $this->jsonMessage($msg);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function financial()
    {
        $user = service('auth')->user();
        $db = db_connect();

        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        // Receita total aprovada
        $totalRevenueRow = $db->table('payments p')
            ->selectSum('p.amount_payment', 'total')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Aprovado')
            ->get()
            ->getRow();

        $totalRevenue = (float) ($totalRevenueRow->total ?? 0);

        // Receita do mês atual
        $monthRevenueRow = $db->table('payments p')
            ->selectSum('p.amount_payment', 'total')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Aprovado')
            ->where('YEAR(p.created_at)', $currentYear)
            ->where('MONTH(p.created_at)', $currentMonth)
            ->get()
            ->getRow();

        $monthRevenue = (float) ($monthRevenueRow->total ?? 0);

        // Média mensal do ano atual
        $monthlyRows = $db->table('payments p')
            ->select('MONTH(p.created_at) as month, SUM(p.amount_payment) as total')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Aprovado')
            ->where('YEAR(p.created_at)', $currentYear)
            ->groupBy('MONTH(p.created_at)')
            ->get()
            ->getResult();

        $monthsWithSales = count($monthlyRows);
        $yearTotal = 0;

        foreach ($monthlyRows as $row) {
            $yearTotal += (float) ($row->total ?? 0);
        }

        $averageMonth = $monthsWithSales > 0 ? $yearTotal / $monthsWithSales : 0;

        // Próximo pagamento
        // Aqui vou assumir que "Próximo Pagamento" é o valor pendente a liberar.
        $nextPaymentRow = $db->table('payments p')
            ->selectSum('p.amount_payment', 'total')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Pendente')
            ->get()
            ->getRow();

        $nextPayment = (float) ($nextPaymentRow->total ?? 0);

        // Últimas transações
        $latestTransactions = $db->table('payments p')
            ->select('
            p.id_payment,
            p.amount_payment,
            p.status_payment,
            p.method_payment,
            p.reference_payment,
            p.created_at,
            c.title_course
        ')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->orderBy('p.created_at', 'DESC')
            ->limit(8)
            ->get()
            ->getResult();

        foreach ($latestTransactions as $transaction) {
            $transaction->method_payment_label = $this->normalizePaymentMethod($transaction->method_payment ?? null);
        }

        $paymentMethods = $db->table('payments p')
            ->select('COALESCE(NULLIF(p.method_payment, ""), "Nao informado") AS method_payment', false)
            ->select('COUNT(*) AS total_transactions, SUM(p.amount_payment) AS total_amount', false)
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->whereIn('p.status_payment', ['Aprovado', 'Pendente'])
            ->groupBy('method_payment')
            ->orderBy('total_amount', 'DESC')
            ->get()
            ->getResult();

        foreach ($paymentMethods as $method) {
            $method->method_payment_label = $this->normalizePaymentMethod($method->method_payment ?? null);
        }

        // Cursos mais rentáveis
        $topCourses = $db->table('payments p')
            ->select('c.title_course, SUM(p.amount_payment) as total')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Aprovado')
            ->groupBy('c.id_course, c.title_course')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get()
            ->getResult();

        // Estatísticas do gráfico do ano atual
        $chartRows = $db->table('payments p')
            ->select('MONTH(p.created_at) as month, SUM(p.amount_payment) as total')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Aprovado')
            ->where('YEAR(p.created_at)', $currentYear)
            ->groupBy('MONTH(p.created_at)')
            ->orderBy('MONTH(p.created_at)')
            ->get()
            ->getResultArray();

        $totals = array_fill(1, 12, 0.0);
        foreach ($chartRows as $row) {
            $month = (int) ($row['month'] ?? 0);
            if ($month >= 1 && $month <= 12) {
                $totals[$month] = (float) ($row['total'] ?? 0);
            }
        }

        $chartSeries = array_values($totals);
        $chartMax = !empty($chartSeries) ? max($chartSeries) : 0;
        $chartAvg = array_sum($chartSeries) / 12;
        $chartGrowth = $currentMonth > 1
            ? ($chartSeries[$currentMonth - 1] - $chartSeries[$currentMonth - 2])
            : 0;

        return view('pages/instructor/financial', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),

            'currentYear' => $currentYear,
            'totalRevenue' => $totalRevenue,
            'monthRevenue' => $monthRevenue,
            'averageMonth' => $averageMonth,
            'yearTotal' => $yearTotal,
            'nextPayment' => $nextPayment,
            'latestTransactions' => $latestTransactions,
            'paymentMethods' => $paymentMethods,
            'topCourses' => $topCourses,
            'chartMax' => $chartMax,
            'chartAvg' => $chartAvg,
            'chartGrowth' => $chartGrowth,
        ]);
    }

    public function financialData()
    {
        $user = service('auth')->user();
        $year = (int) $this->request->getGet('year');
        if ($year <= 0) {
            $year = (int) date('Y');
        }

        $db = db_connect();
        $rows = $db->table('payments p')
            ->select('MONTH(p.created_at) as month, SUM(p.amount_payment) as total')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Aprovado')
            ->where('YEAR(p.created_at)', $year)
            ->groupBy('MONTH(p.created_at)')
            ->orderBy('MONTH(p.created_at)', 'ASC')
            ->get()
            ->getResultArray();

        $totals = array_fill(1, 12, 0.0);
        foreach ($rows as $row) {
            $month = (int) ($row['month'] ?? 0);
            if ($month >= 1 && $month <= 12) {
                $totals[$month] = (float) ($row['total'] ?? 0);
            }
        }

        $labels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $series = array_values($totals);

        return $this->response->setJSON([
            'labels' => $labels,
            'data'   => $series,
            'max'    => !empty($series) ? max($series) : 0,
            'avg'    => count($series) ? array_sum($series) / count($series) : 0,
        ]);
    }

    public function logs()
    {
        $user = service('auth')->user();

        return view('pages/instructor/logs', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
        ]);
    }

    public function logsData()
    {
        $user = service('auth')->user();
        $search = trim((string) $this->request->getGet('q'));
        $level = strtolower(trim((string) $this->request->getGet('level')));
        $dateFrom = $this->normalizeDateFilter((string) $this->request->getGet('date_from'));
        $dateTo = $this->normalizeDateFilter((string) $this->request->getGet('date_to'));
        if ($dateFrom !== null && $dateTo !== null && $dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = (int) $this->request->getGet('per_page');
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min(max($perPage, 5), 50);
        $offset = ($page - 1) * $perPage;

        $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        if ($level !== '' && ! in_array($level, $levels, true)) {
            $level = '';
        }

        $builder = $this->buildAuditLogQuery((int) $user->id, $search, $level, $dateFrom, $dateTo);

        $countBuilder = clone $builder;
        $total = (int) $countBuilder->countAllResults();

        $rows = $builder
            ->orderBy('created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $items = array_map(fn(array $row): array => $this->mapAuditRow($row), $rows);

        $totalPages = (int) ceil($total / $perPage);

        return $this->response->setJSON([
            'items' => $items,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ]);
    }

    public function logsExportCsv()
    {
        $user = service('auth')->user();
        $search = trim((string) $this->request->getGet('q'));
        $level = strtolower(trim((string) $this->request->getGet('level')));
        $dateFrom = $this->normalizeDateFilter((string) $this->request->getGet('date_from'));
        $dateTo = $this->normalizeDateFilter((string) $this->request->getGet('date_to'));
        if ($dateFrom !== null && $dateTo !== null && $dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        if ($level !== '' && ! in_array($level, $levels, true)) {
            $level = '';
        }

        $rows = $this->buildAuditLogQuery((int) $user->id, $search, $level, $dateFrom, $dateTo)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $filename = 'logs-' . (int) $user->id . '-' . date('Ymd-His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        if ($handle === false) {
            return $this->response->setStatusCode(500)->setBody('Falha ao gerar CSV.');
        }

        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, ['Data', 'Nivel', 'Evento', 'Mensagem', 'Metodo', 'Rota', 'IP', 'Contexto']);

        foreach ($rows as $row) {
            $mapped = $this->mapAuditRow($row);
            fputcsv($handle, [
                (string) ($mapped['created_at'] ?? ''),
                (string) ($mapped['level_audit_log'] ?? ''),
                (string) ($mapped['event_audit_log'] ?? ''),
                (string) ($mapped['message_audit_log'] ?? ''),
                (string) ($mapped['method_audit_log'] ?? ''),
                (string) ($mapped['uri_audit_log'] ?? ''),
                (string) ($mapped['ip_address_audit_log'] ?? ''),
                (string) ($mapped['context_pretty'] ?? ''),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv === false ? '' : $csv);
    }

    private function buildAuditLogQuery(int $userId, string $search, string $level, ?string $dateFrom, ?string $dateTo)
    {
        $builder = db_connect()->table('audit_logs')
            ->select([
                'id_audit_log',
                'event_audit_log',
                'level_audit_log',
                'message_audit_log',
                'method_audit_log',
                'uri_audit_log',
                'ip_address_audit_log',
                'context_audit_log',
                'created_at',
            ])
            ->where('actor_user_id', $userId);

        if ($search !== '') {
            $builder->groupStart()
                ->like('event_audit_log', $search)
                ->orLike('message_audit_log', $search)
                ->orLike('uri_audit_log', $search)
                ->orLike('context_audit_log', $search)
                ->groupEnd();
        }

        if ($level !== '') {
            $builder->where('level_audit_log', $level);
        }

        if ($dateFrom !== null) {
            $builder->where('created_at >=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo !== null) {
            $builder->where('created_at <=', $dateTo . ' 23:59:59');
        }

        return $builder;
    }

    private function normalizeDateFilter(string $date): ?string
    {
        $date = trim($date);
        if ($date === '') {
            return null;
        }

        $parsed = \DateTime::createFromFormat('Y-m-d', $date);
        if (! $parsed) {
            return null;
        }

        return $parsed->format('Y-m-d') === $date ? $date : null;
    }

    private function mapAuditRow(array $row): array
    {
        $context = trim((string) ($row['context_audit_log'] ?? ''));
        $decoded = null;
        if ($context !== '') {
            $decoded = json_decode($context, true);
        }

        if (is_array($decoded)) {
            $pretty = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $row['context_pretty'] = $pretty === false ? $context : $pretty;
        } else {
            $row['context_pretty'] = $context;
        }

        return $row;
    }

    public function notificationsData()
    {
        $user = service('auth')->user();
        $userId = (int) ($user->id ?? 0);
        $model = new UserNotificationModel();

        $limit = (int) $this->request->getGet('limit');
        if ($limit <= 0) {
            $limit = 12;
        }
        $limit = min($limit, 30);

        $rows = $model->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->orderBy('id_notification', 'DESC')
            ->findAll($limit);

        $unread = $model->unreadCount($userId);
        $items = [];

        foreach ($rows as $row) {
            $createdAt = (string) ($row['created_at'] ?? '');
            $ts = strtotime($createdAt);
            $type = (string) ($row['type_notification'] ?? '');
            $isPayment = str_starts_with($type, 'payment.');
            $isEnrollment = str_starts_with($type, 'enrollment.');

            $items[] = [
                'id'             => (int) ($row['id_notification'] ?? 0),
                'type'           => $type,
                'title'          => (string) ($row['title_notification'] ?? 'Notificação'),
                'body'           => (string) ($row['body_notification'] ?? ''),
                'link'           => (string) ($row['link_notification'] ?? ''),
                'unread'         => empty($row['read_at']),
                'created_at'     => $ts ? date('d/m/Y H:i', $ts) : $createdAt,
                'created_at_iso' => $ts ? date(DATE_ATOM, $ts) : '',
                'tone'           => $isPayment ? 'emerald' : ($isEnrollment ? 'blue' : 'indigo'),
                'icon'           => $isPayment ? 'bi-cash-coin' : ($isEnrollment ? 'bi-person-plus' : 'bi-bell'),
            ];
        }

        return $this->response->setJSON([
            'ok'     => true,
            'unread' => $unread,
            'items'  => $items,
        ]);
    }

    public function notificationsMarkRead()
    {
        $user = service('auth')->user();
        $userId = (int) ($user->id ?? 0);
        $model = new UserNotificationModel();
        $id = (int) ($this->request->getPost('id') ?? 0);

        if ($id > 0) {
            $model->markRead($userId, $id);
        } else {
            $model->markAllRead($userId);
        }

        return $this->response->setJSON([
            'ok'     => true,
            'unread' => $model->unreadCount($userId),
            'csrf'   => csrf_hash(),
        ]);
    }

    public function profile()
    {
        $users = new ExtendedUserModel();
        $userModel = new UserModel();
        $courseModel = new CourseModel();

        $user = auth()->user();

        if (! $user) {
            return redirect()->to(site_url('login'))
                ->with('error', 'SessÃƒÂ£o expirada. FaÃƒÂ§a login novamente.');
        }

        $profileUrl = current_url(false);
        $db = db_connect();
        $courseCount = (int) $courseModel
            ->where('id_instructor_course', $user->id)
            ->countAllResults();
        $avgProgressRow = $db->table('enrollments e')
            ->selectAvg('e.progress_enrollment', 'avg_progress')
            ->join('courses c', 'c.id_course = e.id_course_enrollment')
            ->where('c.id_instructor_course', $user->id)
            ->where('e.status_enrollment', 'ativa')
            ->get()
            ->getRow();
        $avgProgress = (int) round($avgProgressRow->avg_progress ?? 0);

        if ($this->request->getMethod() !== 'POST') {
            return view('pages/instructor/profile', [
                'user'         => $user,
                'courseCount'  => $courseCount,
                'avgProgress'  => $avgProgress,
                'sidebarLinks' => $this->sidebarLinks(),
                'currentUrl'   => $profileUrl,
            ]);
        }

        // ValidaÃƒÂ§ÃƒÂ£o
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
                ->with('error', implode(', ', $this->validator->getErrors()));
        }

        $post = $this->request->getPost();

        // Se o nome foi enviado, verificar duplicidade
        $userName = trim($post['nome'] ?? '');
        $email    = trim($post['email'] ?? '');

        if (! empty($userName)) {
            $existingUser = $userModel
                ->where('username', $userName)
                ->where('id !=', $user->id)   // ignora o prÃƒÂ³prio usuÃƒÂ¡rio
                ->first();

            if ($existingUser) {
                return redirect()->to($profileUrl)->with('error', 'JÃƒÂ¡ existe um usuÃƒÂ¡rio com esse nome!');
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
                return redirect()->to($profileUrl)->with('error', 'Ja existe um usuario com esse email!');
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
                return redirect()->to($profileUrl)
                    ->withInput()
                    ->with('error', 'Falha ao mover a imagem.');
            }

            // Apagar antiga
            if (! empty($user->img)) {
                $old = FCPATH . $user->img;
                if (is_file($old)) {
                    @unlink($old);
                }
            }

            $filePath = 'assets/img/' . $newName;
        }

        // Dados para atualizar
        $dataProfile = [
            'username' => $userName ?: $user->username,
            'img'      => $filePath,
            'country'  => $post['pais']      ?? $user->country,
            'province' => $post['provincia'] ?? $user->province,
            'city'     => $post['cidade']    ?? $user->city,
            'phone'    => $post['telefone']  ?? $user->phone,
        ];

        // ---------------------------------------
        //    >>> ALTERACAO DE SENHA (SHIELD) <<<
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
                return redirect()->to($profileUrl)->with('error', 'Identidade de senha nao encontrada.');
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
                return redirect()->to($profileUrl)->with('error', 'Para alterar a senha, preencha todos os campos.');
            }

            $identity = db_connect()
                ->table('auth_identities')
                ->where('user_id', $user->id)
                ->where('type', 'email_password')
                ->get()
                ->getRow();

            if (! $identity) {
                return redirect()->to($profileUrl)->with('error', 'Identidade de senha nao encontrada.');
            }

            if (! password_verify($currentPassword, $identity->secret2)) {
                return redirect()->to($profileUrl)->with('error', 'A senha atual fornecida esta incorreta.');
            }

            if (strlen($newPassword) < 6) {
                return redirect()->to($profileUrl)->with('error', 'A nova senha deve ter no minimo 6 caracteres.');
            }

            if ($newPassword !== $confirmPassword) {
                return redirect()->to($profileUrl)->with('error', 'A confirmacao da senha nao coincide.');
            }

            db_connect()
                ->table('auth_identities')
                ->where('id', $identity->id)
                ->update([
                    'secret2'    => password_hash($newPassword, PASSWORD_DEFAULT),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        if (! $users->update($user->id, $dataProfile)) {
            return redirect()->to($profileUrl)
                ->with('error', implode(', ', $users->errors()));
        }

        // Atualiza usuÃƒÂ¡rio logado
        $updated = $users->find($user->id);
        auth()->setUser($updated);

        return redirect()->to($profileUrl)->with('success', 'Perfil atualizado com sucesso.');
    }

    public function approveEnrollment($courseId, $pendingId)
    {
        $actualUser = service('auth')->user();
        $courseId = (int) $courseId;
        $pendingId = (int) $pendingId;

        $db = db_connect();
        helper('text');

        $studentModel     = new \App\Models\StudentModel();
        $enrollmentModel  = new \App\Models\EnrollmentModel();
        $paymentModel     = new \App\Models\PaymentModel();
        $pendingUserModel = new \App\Models\PendingUserModel();
        $users            = new UserModel();
        $enrollmentNotificationService = new EnrollmentNotificationService();

        $isReject = $this->request->getPost('status_payment') === 'Rejeitado';
        if ($isReject) {
            $paymentModel
                ->where('id_user_payment', $pendingId)
                ->where('id_course_payment', $courseId)
                ->set([
                    'status_payment' => 'Rejeitado',
                    'approved_by_payment' => $actualUser->id,
                ])
                ->update();

            $this->auditLogger->write(
                'instructor.payment.rejected',
                'info',
                'Pagamento rejeitado pelo instrutor.',
                [
                    'course_id' => $courseId,
                    'pending_user_id' => $pendingId,
                ]
            );

            if ($this->wantsJson()) {
                return $this->jsonMessage('Pagamento rejeitado.');
            }

            return redirect()->back()->with('success', 'Pagamento rejeitado.');
        }

        // 1. Buscar dados do pending_user
        $pendingUser = $pendingUserModel->find($pendingId);
        if (!$pendingUser) {
            $this->auditLogger->write(
                'instructor.enrollment.pending_user_not_found',
                'warning',
                'Usuario pendente nao encontrado para aprovacao.',
                [
                    'course_id' => $courseId,
                    'pending_user_id' => $pendingId,
                ]
            );

            if ($this->wantsJson()) {
                return $this->jsonMessage('Usuario pendente nao encontrado.', 404);
            }
            return redirect()->back()->with('error', 'UsuÃƒÂ¡rio pendente nÃƒÂ£o encontrado.');
        }

        // 2. Verificar se jÃƒÂ¡ existe um user real com este email
        $existingUser = $users->findByCredentials(['email' => $pendingUser->email]);

        if ($existingUser !== null) {
            // UsuÃƒÂ¡rio jÃƒÂ¡ existe Ã¢â€ â€™ apenas inscrever no curso

            // Buscar o estudante vinculado a este user
            $student = $studentModel->where('id_user_student', $existingUser->id)->first();

            if (!$student) {
                // Se ainda nÃƒÂ£o existe estudante, cria
                $studentId = $studentModel->insert([
                    'id_user_student' => $existingUser->id,
                    'name_student'    => $existingUser->username,
                    'email_student'   => $existingUser->email,
                ]);
            } else {
                $studentId = $student->id_student;
            }

            // Verifica se jÃƒÂ¡ estÃƒÂ¡ inscrito no curso
            $alreadyEnrolled = $enrollmentModel
                ->where('id_student_enrollment', $studentId)
                ->where('id_course_enrollment', $courseId)
                ->first();

            if ($alreadyEnrolled) {
                $this->auditLogger->write(
                    'instructor.enrollment.already_enrolled',
                    'notice',
                    'Aprovacao ignorada porque usuario ja estava inscrito.',
                    [
                        'course_id' => $courseId,
                        'pending_user_id' => $pendingId,
                        'existing_user_id' => (int) $existingUser->id,
                    ]
                );

                if ($this->wantsJson()) {
                    return $this->jsonMessage('Usuario ja inscrito neste curso.', 409);
                }
                return redirect()->back()->with('error', 'UsuÃƒÂ¡rio jÃƒÂ¡ estÃƒÂ¡ inscrito neste curso.');
            }

            // Criar nova inscriÃƒÂ§ÃƒÂ£o
            $newEnrollmentId = $enrollmentModel->insert([
                'id_course_enrollment'   => $courseId,
                'id_student_enrollment'  => $existingUser->id,
                'status_enrollment'      => 'ativa',
                'progress_enrollment'    => 0.00,
                'enrolled_at_enrollment' => date('Y-m-d H:i:s'),
            ]);

            // Atualizar pagamento
            $paymentRow = $paymentModel
                ->where('id_user_payment', $pendingId)
                ->where('id_course_payment', $courseId)
                ->orderBy('id_payment', 'DESC')
                ->first();

            $paymentModel
                ->where('id_user_payment', $pendingId)
                ->set([
                    'id_user_payment'     => $existingUser->id,
                    'status_payment'      => 'Aprovado',
                    'approved_by_payment' => $actualUser->id,
                ])
                ->update();

            // Remover pending_user
            $pendingUserModel->where('id', $pendingId)->delete();

            if ($paymentRow) {
                $enrollmentNotificationService->notifyInstructorAboutNewPayment((int) $paymentRow->id_payment);
            } elseif ($newEnrollmentId !== false) {
                $enrollmentNotificationService->notifyInstructorAboutNewEnrollment((int) $newEnrollmentId);
            }
            if ($newEnrollmentId !== false) {
                $enrollmentNotificationService->notifyStudentAboutEnrollment((int) $newEnrollmentId, 'self');
            }

            $this->auditLogger->write(
                'instructor.enrollment.approved_existing_user',
                'info',
                'Inscricao aprovada para usuario existente.',
                [
                    'course_id' => $courseId,
                    'pending_user_id' => $pendingId,
                    'existing_user_id' => (int) $existingUser->id,
                ]
            );


            if ($this->wantsJson()) {
                return $this->jsonMessage('Inscricao aprovada para usuario existente.');
            }

            return redirect()->back()->with('success', 'InscriÃƒÂ§ÃƒÂ£o aprovada para usuÃƒÂ¡rio jÃƒÂ¡ existente!');
        }

        // 3. Se o usuÃƒÂ¡rio ainda nÃƒÂ£o existe Ã¢â€ â€™ criar um novo
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
        $email->setMessage(\App\Libraries\BrandEmail::render([
            'preheader' => 'A sua matrícula foi aprovada — crie a senha para aceder.',
            'eyebrow'   => 'Matrícula aprovada',
            'greeting'  => 'Olá ' . \App\Libraries\BrandEmail::strong((string) $user->username) . ',',
            'title'     => 'Crie a sua senha e aceda ao curso',
            'body'      => \App\Libraries\BrandEmail::p(
                'A sua matrícula foi aprovada. Clique no botão abaixo para criar a senha e aceder ao curso.'
            ),
            'cta' => [
                'url'   => $link,
                'label' => 'Criar minha senha',
            ],
        ]));
        $email->setMailType('html');
        $email->send();

        // 4. Criar estudante vinculado
        $studentId = $studentModel->insert([
            'id_user_student' => $userId,
            'name_student'    => $pendingUser->username,
            'email_student'   => $pendingUser->email,
        ]);

        // 5. Criar inscriÃƒÂ§ÃƒÂ£o
        $result = $enrollmentModel->insert([
            'id_course_enrollment'   => $courseId,
            'id_student_enrollment'  => $userId,
            'status_enrollment'      => 'ativa',
            'progress_enrollment'    => 0.00,
            'enrolled_at_enrollment' => date('Y-m-d H:i:s'),
        ]);

        // 6. Atualizar pagamento
        $paymentRow = $paymentModel
            ->where('id_user_payment', $pendingId)
            ->where('id_course_payment', $courseId)
            ->orderBy('id_payment', 'DESC')
            ->first();

        $paymentModel
            ->where('id_user_payment', $pendingId)
            ->set([
                'id_user_payment'     => $userId,
                'status_payment'      => 'Aprovado',
                'approved_by_payment' => $actualUser->id,
            ])
            ->update();

        // 7. Remover pending_user
        $pendingUserModel->where('id', $pendingId)->delete();

        if ($paymentRow) {
            $enrollmentNotificationService->notifyInstructorAboutNewPayment((int) $paymentRow->id_payment);
        } elseif ($result !== false) {
            $enrollmentNotificationService->notifyInstructorAboutNewEnrollment((int) $result);
        }
        if ($result !== false) {
            $enrollmentNotificationService->notifyStudentAboutEnrollment((int) $result, 'self');
        }

        $this->auditLogger->write(
            'instructor.enrollment.approved_new_user',
            'info',
            'Inscricao aprovada com criacao de usuario.',
            [
                'course_id' => $courseId,
                'pending_user_id' => $pendingId,
                'new_user_id' => (int) $userId,
            ]
        );

        if ($this->wantsJson()) {
            return $this->jsonMessage('Inscricao aprovada e usuario criado com sucesso.');
        }

        return redirect()->back()->with('success', 'InscriÃƒÂ§ÃƒÂ£o aprovada e usuÃƒÂ¡rio criado com sucesso!');
    }

    public function certificate()
    {
        $courseModel = new CourseModel();
        // $course = $courseModel->find($idCourse);
        $certificateModel = new CertificateModel();

        $user = service('auth')->user();

        $certificates = $certificateModel->getForInstructorDashboard($user->id);

        return view('pages/instructor/certificates', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
            'certificates' => $certificates
        ]);
    }
}
