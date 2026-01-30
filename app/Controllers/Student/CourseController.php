<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\CourseModel;

class CourseController extends BaseController
{
    public function enroll($idCourse)
    {
        $user = service('auth')->user();
        if (!$user) {
            return redirect()->to('/login')->with('error', 'Você precisa estar logado para se inscrever em um curso.');
        }

        $courseModel = new CourseModel();
        $enrollmentModel = new EnrollmentModel();

        // Busca o curso
        $course = $courseModel->find($idCourse);
        if (!$course || $course->status_course !== 'Ativo') {
            return redirect()->back()->with('error', 'Curso não encontrado.');
        }

        // Verifica se o estudante já está inscrito no curso
        $existingEnrollment = $enrollmentModel
            ->where('id_student_enrollment', $user->id)
            ->where('id_course_enrollment', $idCourse)
            ->first();

        if ($existingEnrollment) {
            return redirect()->back()->with('message', 'Você já está inscrito neste curso.');
        }

        // Salva course_id na sessão para usar no checkout
        session()->set('course_id_payment', $idCourse);

        // Redireciona para a página de checkout (Flutterwave) com parâmetros seguros
        $checkoutUrl = route_to('payment.checkout') . '?course_id=' . $course->id_course . '&amount=' . $course->price_course;

        return redirect()->to($checkoutUrl)->with('success', 'Pronto! Complete o pagamento para confirmar sua inscrição.');
    }
}
