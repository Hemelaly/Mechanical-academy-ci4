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

    private function getCourseContentStats(int $courseId): array
    {
        $lessonModel = new LessonModel();
        $moduleModel = new ModuleModel();
        $projectModel = new ProjectModel();

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

        $projectCount = $projectModel
            ->where('id_course_project', $courseId)
            ->countAllResults();

        return [
            'moduleCount' => $moduleCount,
            'lessonCount' => $lessonCount,
            'projectCount' => $projectCount,
            'totalMinutes' => $totalMinutes,
            'totalHoursLabel' => $this->formatCourseHours($totalMinutes),
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

        $checkoutStats = $this->getCourseContentStats((int) $course->id_course);

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
            'checkoutStats' => $checkoutStats
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
        $courseHours = $totalMinutes / 60;

        $isEnrolled = false;
        if ($userId) {
            $isEnrolled = $enrollmentModel
                ->where('id_student_enrollment', $userId)
                ->where('id_course_enrollment', $course->id_course)
                ->where('status_enrollment', 'ativa')
                ->first();
        }

        $projects = $projectModel->where('id_course_project', $id_course)->findAll();
        $projectCount = count($projects);
        $studentCount = $enrollmentModel
            ->where('id_course_enrollment', $course->id_course)
            ->where('status_enrollment !=', 'Cancelada')
            ->countAllResults();

        return view('courses/course', [
            'course' => $course,
            'modules' => $module,
            'projects' => $projects,
            'moduleCount' => $moduleCount,
            'lessonCount' => $lessonCount,
            'projectCount' => $projectCount,
            'studentCount' => $studentCount,
            'courseHours' => $courseHours,
        ]);
    }
}
