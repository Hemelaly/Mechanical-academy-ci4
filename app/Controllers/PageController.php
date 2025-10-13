<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CourseModel;

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
}
