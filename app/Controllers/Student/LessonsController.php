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

        $completedAt = null;
        $availableAt = null;

        if ($percent >= 100) {
            $row = $db->table('enrollments')
                ->select('completed_enrollment')
                ->where('id_enrollment', $idEnrollment)
                ->get()
                ->getRowArray();

            $completedAt = $row['completed_enrollment'] ?? null;
            if (empty($completedAt)) {
                $completedAt = date('Y-m-d H:i:s');
                $db->table('enrollments')
                    ->where('id_enrollment', $idEnrollment)
                    ->update(['completed_enrollment' => $completedAt]);
            }

            $availableAt = date('Y-m-d H:i:s', strtotime($completedAt . ' +48 hours'));
        }

        $completedAtIso = $completedAt ? date('c', strtotime($completedAt)) : null;
        $availableAtIso = $availableAt ? date('c', strtotime($availableAt)) : null;

        return $this->response->setHeader('X-CSRF-Hash', csrf_hash())
            ->setJSON([
                'ok'           => true,
                'progress'     => $percent,
                'completed_at' => $completedAtIso,
                'available_at' => $availableAtIso,
            ]);
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

        if ($percent < 100) {
            $db->table('enrollments')
                ->where('id_enrollment', $idEnrollment)
                ->update(['completed_enrollment' => null]);
        }

        return $this->response->setHeader('X-CSRF-Hash', csrf_hash())
            ->setJSON(['ok' => true, 'progress' => $percent]);
    }
}
