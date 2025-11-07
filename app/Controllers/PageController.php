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
    public function index($id_course)
    {
        $courseModel = new CourseModel();
        $user = service('auth')->user();

        $course = $courseModel->find($id_course);

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $userId = $user ? $user->id : null;

        $isEnrolled = false;
        if ($userId) {
            $isEnrolled = $enrollmentModel
                ->where('id_student_enrollment', $userId)
                ->where('id_course_enrollment', $course->id_course)
                ->where('status_enrollment', 'Ativa')
                ->first();
        }

        return view('checkout', [
            'course' => $course,
            'user' => $user,
            'isEnrolled' => $isEnrolled
        ]);
    }

    public function excel($id_course)
    {

        $lessonModel     = new LessonModel();
        $moduleModel     = new ModuleModel();
        $courseModel     = new CourseModel();
        $projectModel    = new ProjectModel();

        $user = service('auth')->user();

        $course = $courseModel->find($id_course);

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $userId = $user ? $user->id : null;

        $module = $moduleModel
            ->select('id_module, title_module, position_module, description_module') // opcional: só as colunas que precisa
            ->where('id_course_module', $id_course)
            ->orderBy('position_module', 'ASC')
            ->findAll();

        $isEnrolled = false;
        if ($userId) {
            $isEnrolled = $enrollmentModel
                ->where('id_student_enrollment', $userId)
                ->where('id_course_enrollment', $course->id_course)
                ->where('status_enrollment', 'Ativa')
                ->first();
        }

        $projects = $projectModel->where('id_course_project', $id_course)->findAll();

        return view('courses/excel', [
            'course' => $course,
            'modules' => $module,
            'projects' => $projects,
        ]);
    }
}
