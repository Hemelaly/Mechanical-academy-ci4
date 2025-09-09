<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class CourseController extends BaseController
{
    public function enroll($idCourse)
    {
        $user = service('auth')->user();
        $enrollmentModel = new \App\Models\EnrollmentModel();

        // Verifica se já está inscrito
        $existing = $enrollmentModel->where('id_student_enrollment', $user->id)
            ->where('id_course_enrollment', $idCourse)
            ->first();
        if ($existing) {
            return redirect()->back()->with('message', 'Você já está inscrito nesse curso.');
        }

        // Cria a matrícula
        $enrollmentModel->insert([
            'id_student_enrollment' => $user->id,
            'id_course_enrollment' => $idCourse,
            'status_enrollment' => 'active',
            'progress_enrollment' => 0,
            'enrolled_at_enrollment' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/student/dashboard/meus_cursos')
            ->with('success', 'Inscrição realizada com sucesso!');
    }
}
