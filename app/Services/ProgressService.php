<?php

// app/Services/ProgressService.php
namespace App\Services;

use CodeIgniter\Database\BaseConnection;

class ProgressService
{
    public function __construct(private BaseConnection $db) {}

    /** Recalcula (0–100) e atualiza enrollments.progress_enrollment */
    public function recalcAndSave(int $idEnrollment): int
    {
        // curso da matrícula
        $row = $this->db->table('enrollments')
            ->select('id_course_enrollment')
            ->where('id_enrollment', $idEnrollment)
            ->get()->getRowArray();

        if (!$row) return 0;

        $idCourse = (int)$row['id_course_enrollment'];

        // TOTAL de aulas do curso (via modules)
        $total = (int)$this->db->table('lessons l')
            ->join('modules m', 'm.id_module = l.id_module_lesson', 'inner')
            ->where('m.id_course_module', $idCourse)
            // ->where('l.is_published_lesson', 1) // se existir esse campo
            ->countAllResults();

        if ($total <= 0) {
            $this->updatePercent($idEnrollment, 0);
            return 0;
        }

        // CONCLUÍDAS desta matrícula no curso (via modules)
        $completed = (int)$this->db->table('progress p')
            ->join('lessons l', 'l.id_lesson = p.id_lesson_progress', 'inner')
            ->join('modules m', 'm.id_module = l.id_module_lesson', 'inner')
            ->where('p.id_enrollment_progress', $idEnrollment)
            ->where('m.id_course_module', $idCourse)
            ->where('p.completed_at_progress IS NOT NULL', null, false)
            ->countAllResults();

        $percent = (int) floor(($completed / $total) * 100);
        $this->updatePercent($idEnrollment, $percent);

        return $percent;
    }

    private function updatePercent(int $idEnrollment, int $percent): void
    {
        $this->db->query(
            'UPDATE enrollments
               SET progress_enrollment = LEAST(100, ?), updated_at = NOW()
             WHERE id_enrollment = ?',
            [$percent, $idEnrollment]
        );
    }
}
