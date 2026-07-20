<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\CourseRatingModel;
use App\Models\EnrollmentModel;

class CourseRatings extends BaseController
{
    public function store(int $courseId)
    {
        $user = auth()->user();
        if (! $user) {
            return $this->response->setStatusCode(401)->setJSON([
                'ok' => false,
                'message' => 'Faça login para avaliar o curso.',
            ]);
        }

        $course = (new CourseModel())->find($courseId);
        if (! $course) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok' => false,
                'message' => 'Curso não encontrado.',
            ]);
        }

        $enrollment = (new EnrollmentModel())
            ->where('id_student_enrollment', (int) $user->id)
            ->where('id_course_enrollment', $courseId)
            ->whereIn('status_enrollment', ['ativa', 'pendente'])
            ->first();

        if (! $enrollment) {
            return $this->response->setStatusCode(403)->setJSON([
                'ok' => false,
                'message' => 'Só alunos inscritos podem avaliar o curso.',
            ]);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $score = (int) ($payload['score'] ?? $payload['score_rating'] ?? 0);
        $comment = trim((string) ($payload['comment'] ?? $payload['comment_rating'] ?? ''));

        if ($score < 1 || $score > 5) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'message' => 'A avaliação deve ser entre 1 e 5 estrelas.',
            ]);
        }

        $model = new CourseRatingModel();
        $existing = $model
            ->where('id_course_rating', $courseId)
            ->where('id_user_rating', (int) $user->id)
            ->first();

        $data = [
            'id_course_rating'     => $courseId,
            'id_user_rating'       => (int) $user->id,
            'id_enrollment_rating' => (int) ($enrollment->id_enrollment ?? $enrollment['id_enrollment'] ?? 0),
            'score_rating'         => $score,
            'comment_rating'       => $comment !== '' ? $comment : null,
        ];

        try {
            if ($existing) {
                $model->update((int) $existing['id_rating'], $data);
            } else {
                $model->insert($data);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Falha ao guardar avaliação: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'ok' => false,
                'message' => 'Não foi possível guardar a avaliação. Verifique se a migration foi aplicada.',
            ]);
        }

        return $this->response
            ->setHeader('X-CSRF-Hash', csrf_hash())
            ->setJSON([
                'ok' => true,
                'message' => 'Obrigado pela sua avaliação!',
                'summary' => $model->getCourseSummary($courseId),
            ]);
    }
}
