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
        $courseModel = new \App\Models\CourseModel(); // Modelo do curso

        // Busca o curso
        $course = $courseModel->find($idCourse);
        if (!$course) {
            return redirect()->back()->with('error', 'Curso não encontrado.');
        }

        // Verifica duplicidade
        $existing = $enrollmentModel->where('id_student_enrollment', $user->id)
            ->where('id_course_enrollment', $idCourse)
            ->first();
        if ($existing) {
            return redirect()->back()->with('message', 'Você já está inscrito neste curso.');
        }

        // Salva course_id na session para usar no callback
        session()->set('course_id_payment', $idCourse);

        // Redireciona para o checkout do Flutterwave com o valor correto
        return redirect()->to(base_url("payment/checkout?course_id={$idCourse}&amount={$course->price_course}"));
    }
}
