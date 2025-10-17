<?php

// app/Controllers/Student/LessonsController.php
namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Services\ProgressService;

class LessonsController extends BaseController
{
    public function complete()
    {
        $payload      = $this->request->getJSON(true);
        $idLesson     = (int)($payload['lesson_id'] ?? 0);
        $idEnrollment = (int)($payload['enrollment_id'] ?? 0);
        if (!$idLesson || !$idEnrollment) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Parâmetros inválidos']);
        }

        $db = db_connect();
        // upsert progresso
        $db->query(
            'INSERT INTO progress (id_enrollment_progress, id_lesson_progress, completed_at_progress, created_at, updated_at)
         VALUES (?, ?, NOW(), NOW(), NOW())
         ON DUPLICATE KEY UPDATE completed_at_progress = VALUES(completed_at_progress), updated_at = NOW()',
            [$idEnrollment, $idLesson]
        );

        $percent = (new \App\Services\ProgressService($db))->recalcAndSave($idEnrollment);

        return $this->response->setHeader('X-CSRF-Hash', csrf_hash())
            ->setJSON(['ok' => true, 'progress' => $percent]);
    }

    public function uncomplete()
    {
        $payload      = $this->request->getJSON(true);
        $idLesson     = (int)($payload['lesson_id'] ?? 0);
        $idEnrollment = (int)($payload['enrollment_id'] ?? 0);

        if (!$idLesson || !$idEnrollment) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Parâmetros inválidos']);
        }

        $db = db_connect();

        // opção A: manter linha e zerar data
        $db->table('progress')
            ->where('id_enrollment_progress', $idEnrollment)
            ->where('id_lesson_progress', $idLesson)
            ->update(['completed_at_progress' => null, 'updated_at' => date('Y-m-d H:i:s')]);

        // (ou opção B: deletar a linha)

        $percent = (new ProgressService($db))->recalcAndSave($idEnrollment);

        return $this->response->setHeader('X-CSRF-Hash', csrf_hash())
            ->setJSON(['ok' => true, 'progress' => $percent]);
    }
}
