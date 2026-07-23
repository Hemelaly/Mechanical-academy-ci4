<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CourseModel;
use App\Models\LessonModel;
use App\Models\ModuleModel;
use App\Models\ProjectModel;

class PageController extends BaseController
{
    private function formatCourseHours(int $totalMinutes): string
    {
        if ($totalMinutes <= 0) {
            return '0 Horas';
        }

        $hours = $totalMinutes / 60;
        $formattedHours = fmod($hours, 1.0) === 0.0
            ? number_format($hours, 0, ',', '.')
            : number_format($hours, 1, ',', '.');

        $unit = abs($hours - 1.0) < 0.00001 ? 'Hora' : 'Horas';

        return $formattedHours . ' ' . $unit;
    }

    private function getCourseContentStats(int $courseId, $course = null): array
    {
        $lessonModel = new LessonModel();
        $moduleModel = new ModuleModel();
        $projectModel = new ProjectModel();
        $commerce = new \App\Services\CourseCommerceService();

        $moduleCount = $moduleModel
            ->where('id_course_module', $courseId)
            ->countAllResults();

        $lessonCount = $lessonModel
            ->join('modules m', 'm.id_module = lessons.id_module_lesson', 'inner')
            ->where('m.id_course_module', $courseId)
            ->countAllResults();

        $totalMinutesRow = \Config\Database::connect()
            ->table('modules m')
            ->select('COALESCE(SUM(l.duration_lesson), 0) AS total_minutes', false)
            ->join('lessons l', 'l.id_module_lesson = m.id_module', 'left')
            ->where('m.id_course_module', $courseId)
            ->get()
            ->getRow();

        $totalMinutes = (int) ($totalMinutesRow->total_minutes ?? 0);
        if ($course === null) {
            $course = (new CourseModel())->find($courseId);
        }

        $projectCount = $projectModel
            ->where('id_course_project', $courseId)
            ->countAllResults();

        return [
            'moduleCount' => $moduleCount,
            'lessonCount' => $lessonCount,
            'projectCount' => $projectCount,
            'totalMinutes' => $totalMinutes,
            'totalHoursLabel' => $commerce->resolveHoursLabel($course, $totalMinutes),
            'totalHoursValue' => $commerce->resolveHoursValue($course, $totalMinutes),
            'listPrice' => $commerce->getListPrice($course),
            'promoPrice' => $commerce->getPromoPrice($course),
            'effectivePrice' => $commerce->getEffectivePrice($course),
            'hasPromo' => $commerce->hasPromo($course),
            'discountPercent' => $commerce->getDiscountPercent($course),
            'promoEndsAt' => $commerce->getPromoEndsAt($course),
            'promoRemainingSeconds' => $commerce->getPromoRemainingSeconds($course),
            'freeLessonsCount' => $commerce->getFreeLessonsCount($course),
            'whatsappUrl' => $commerce->buildWhatsappUrl(
                $course,
                'Olá! Tenho interesse no curso "' . trim((string) ($course->title_course ?? '')) . '" e gostaria de saber as opções de pagamento.'
            ),
        ];
    }

    public function index($id_course)
    {
        $courseModel = new CourseModel();
        $user = service('auth')->user();

        $course = $courseModel
            ->where('status_course', 'Ativo')
            ->find($id_course);
        if (! $course) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Curso não encontrado');
        }

        $checkoutStats = $this->getCourseContentStats((int) $course->id_course, $course);

        $moduleModel = new ModuleModel();
        $lessonModel = new LessonModel();
        $modules = $moduleModel
            ->select('id_module, title_module, position_module')
            ->where('id_course_module', (int) $course->id_course)
            ->orderBy('position_module', 'ASC')
            ->findAll();
        $moduleIds = array_map(static fn ($m) => (int) $m->id_module, $modules);
        $lessonsByModule = [];
        if ($moduleIds !== []) {
            $lessons = $lessonModel
                ->select('id_lesson, id_module_lesson, title_lesson, duration_lesson, type_lesson, position_lesson')
                ->whereIn('id_module_lesson', $moduleIds)
                ->orderBy('position_lesson', 'ASC')
                ->findAll();
            foreach ($lessons as $lesson) {
                $lessonsByModule[(int) $lesson->id_module_lesson][] = $lesson;
            }
        }
        foreach ($modules as $module) {
            $module->lessons = $lessonsByModule[(int) $module->id_module] ?? [];
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $userId = $user ? $user->id : null;

        $isEnrolled = false;
        if ($userId) {
            $isEnrolled = $enrollmentModel
                ->where('id_student_enrollment', $userId)
                ->where('id_course_enrollment', $course->id_course)
                ->where('status_enrollment', 'ativa')
                ->first();
        }

        return view('checkout', [
            'course' => $course,
            'user' => $user,
            'isEnrolled' => $isEnrolled,
            'checkoutStats' => $checkoutStats,
            'modules' => $modules,
            'commerce' => new \App\Services\CourseCommerceService(),
        ]);
    }

    public function coursePage($id_course)
    {

        $lessonModel     = new LessonModel();
        $moduleModel     = new ModuleModel();
        $courseModel     = new CourseModel();
        $projectModel    = new ProjectModel();

        $user = service('auth')->user();

        $course = $courseModel
            ->where('status_course', 'Ativo')
            ->find($id_course);
        if (! $course) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Curso não encontrado');
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $userId = $user ? $user->id : null;

        $module = $moduleModel
            ->select('id_module, title_module, position_module, description_module') // opcional: só as colunas que precisa
            ->where('id_course_module', $id_course)
            ->orderBy('position_module', 'ASC')
            ->findAll();

        $moduleIds = array_map(static fn($m) => (int) $m->id_module, $module);
        $lessonsByModule = [];
        if (!empty($moduleIds)) {
            $lessons = $lessonModel
                ->select('id_lesson, id_module_lesson, title_lesson, type_lesson, duration_lesson, video_url_lesson, is_preview_lesson, position_lesson')
                ->whereIn('id_module_lesson', $moduleIds)
                ->orderBy('id_module_lesson', 'ASC')
                ->orderBy('position_lesson', 'ASC')
                ->findAll();

            foreach ($lessons as $lesson) {
                $modId = (int) $lesson->id_module_lesson;
                if (!isset($lessonsByModule[$modId])) {
                    $lessonsByModule[$modId] = [];
                }
                $lessonsByModule[$modId][] = $lesson;
            }
        }

        foreach ($module as $m) {
            $m->lessons = $lessonsByModule[(int) $m->id_module] ?? [];
        }

        $moduleCount = count($module);
        $lessonCount = $lessonModel
            ->join('modules m', 'm.id_module = lessons.id_module_lesson', 'inner')
            ->where('m.id_course_module', $id_course)
            ->countAllResults();

        $totalMinutesRow = $lessonModel
            ->select('COALESCE(SUM(duration_lesson), 0) AS total_minutes')
            ->join('modules m', 'm.id_module = lessons.id_module_lesson', 'inner')
            ->where('m.id_course_module', $id_course)
            ->get()
            ->getRow();
        $totalMinutes = (int) ($totalMinutesRow->total_minutes ?? 0);

        $commerce = new \App\Services\CourseCommerceService();
        $courseHours = $commerce->resolveHoursValue($course, $totalMinutes);
        $hoursLabel = $commerce->resolveHoursLabel($course, $totalMinutes);

        $isEnrolled = false;
        $trialEnrollment = null;
        if ($userId) {
            $isEnrolled = $enrollmentModel
                ->where('id_student_enrollment', $userId)
                ->where('id_course_enrollment', $course->id_course)
                ->where('status_enrollment', 'ativa')
                ->first();
            $trialEnrollment = $enrollmentModel
                ->where('id_student_enrollment', $userId)
                ->where('id_course_enrollment', $course->id_course)
                ->where('status_enrollment', 'pendente')
                ->first();
        }

        $projects = $projectModel->where('id_course_project', $id_course)->findAll();
        $projectCount = count($projects);
        $studentCount = $enrollmentModel
            ->where('id_course_enrollment', $course->id_course)
            ->where('status_enrollment !=', 'Cancelada')
            ->countAllResults();

        $ratingModel = new \App\Models\CourseRatingModel();
        $ratingSummary = ['average' => 0, 'total' => 0];
        $ratingList = [];
        try {
            $ratingSummary = $ratingModel->getCourseSummary((int) $course->id_course);
            $ratingList = $ratingModel->getForCourse((int) $course->id_course, 8);
        } catch (\Throwable $e) {
            // Tabela pode ainda não existir antes da migration.
        }

        return view('courses/course', [
            'course' => $course,
            'modules' => $module,
            'projects' => $projects,
            'moduleCount' => $moduleCount,
            'lessonCount' => $lessonCount,
            'projectCount' => $projectCount,
            'studentCount' => $studentCount,
            'courseHours' => $courseHours,
            'hoursLabel' => $hoursLabel,
            'commerce' => $commerce,
            'effectivePrice' => $commerce->getEffectivePrice($course),
            'listPrice' => $commerce->getListPrice($course),
            'promoPrice' => $commerce->getPromoPrice($course),
            'hasPromo' => $commerce->hasPromo($course),
            'discountPercent' => $commerce->getDiscountPercent($course),
            'promoEndsAt' => $commerce->getPromoEndsAt($course),
            'promoRemainingSeconds' => $commerce->getPromoRemainingSeconds($course),
            'freeLessonsCount' => $commerce->getFreeLessonsCount($course),
            'whatsappUrl' => $commerce->buildWhatsappUrl(
                $course,
                'Olá! Vi o curso "' . trim((string) ($course->title_course ?? '')) . '" e quero falar sobre pagamento/inscrição.'
            ),
            'isEnrolled' => $isEnrolled,
            'trialEnrollment' => $trialEnrollment,
            'ratingSummary' => $ratingSummary,
            'ratingList' => $ratingList,
            'user' => $user,
        ]);
    }

    /**
     * Inicia acesso de teste (N aulas grátis) para aluno autenticado.
     */
    public function startTrial(int $id_course)
    {
        $user = service('auth')->user();
        $lessonsPath = 'student/dashboard/ver_aulas/' . $id_course;
        $trialPath   = 'courses/' . $id_course . '/trial';

        if (! $user) {
            session()->setTempdata('beforeLoginUrl', $trialPath, 600);

            return redirect()->to(site_url('login'))
                ->with('error', 'Faça login como aluno para experimentar o curso.');
        }

        $role = strtolower(trim((string) ($user->role ?? '')));
        if ($role !== 'student') {
            return redirect()->to(site_url('courses/' . $id_course))
                ->with('error', 'Apenas contas de aluno podem experimentar aulas grátis.');
        }

        $courseModel = new CourseModel();
        $course = $courseModel->where('status_course', 'Ativo')->find($id_course);
        if (! $course) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Curso não encontrado');
        }

        $commerce = new \App\Services\CourseCommerceService();
        $freeCount = $commerce->getFreeLessonsCount($course);
        if ($freeCount < 1) {
            return redirect()->to(site_url('checkout/' . $id_course))
                ->with('warning', 'Este curso não tem aulas grátis de teste.');
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $existing = $enrollmentModel
            ->where('id_student_enrollment', (int) $user->id)
            ->where('id_course_enrollment', (int) $course->id_course)
            ->first();

        if ($existing && strtolower((string) $existing->status_enrollment) === 'ativa') {
            return redirect()->to(site_url($lessonsPath));
        }

        if (! $existing) {
            $inserted = $enrollmentModel->insert([
                'id_student_enrollment' => (int) $user->id,
                'id_course_enrollment'  => (int) $course->id_course,
                'status_enrollment'     => 'pendente',
                'progress_enrollment'   => 0,
                'enrolled_at_enrollment'=> date('Y-m-d'),
                'is_manual_enrollment'  => 0,
            ]);

            if ($inserted === false) {
                return redirect()->to(site_url('courses/' . $id_course))
                    ->with('error', 'Não foi possível iniciar o acesso de teste. Tente novamente.');
            }
        }

        return redirect()->to(site_url($lessonsPath))
            ->with('info', 'Pode assistir as primeiras ' . $freeCount . ' aulas. Depois será necessário concluir o pagamento.');
    }
}
