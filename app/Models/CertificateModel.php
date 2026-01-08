<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificateModel extends Model
{
    protected $table      = 'certificates';
    protected $primaryKey = 'id_certificate';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_user_certificate',
        'id_course_certificate',
        'uuid_certificate',
        'number_certificate',
        'issued_at_certificate',
        'pdf_path_certificate',
        'hash_certificate',
        'revoked_at_certificate',
        'created_at',
        'updated_at',
        'avaiable_at_certificate',
        'uploaded_by_certificate'
    ];

    /**
     * =========================
     * ALUNO: meus certificados
     * certificates + courses
     * =========================
     */
    public function getForStudent(int $userId): array
    {
        return $this->db->table('certificates cert')
            ->select('cert.*, c.title_course, c.image_course, e.id_enrollment AS enrollment_id')
            ->join('courses c', 'c.id_course = cert.id_course_certificate', 'inner')
            ->join('enrollments e', 'e.id_student_enrollment = cert.id_user_certificate AND e.id_course_enrollment = cert.id_course_certificate', 'left')
            ->where('cert.id_user_certificate', $userId)
            ->orderBy('cert.issued_at_certificate', 'DESC')
            ->get()
            ->getResult();
    }

    /**
     * =========================
     * INSTRUTOR: certificados dos meus cursos
     * courses + instructors + certificates
     * =========================
     *
     * Ajuste estes nomes se forem diferentes no seu BD:
     * - instructors.id_instructor
     * - courses.id_instructor_course
     */
    public function getForInstructorDashboard(int $instructorUserId): array
    {
        return $this->db->table('certificates cert')
            ->select('cert.*, c.title_course, c.image_course, u.username AS student_name, e.id_enrollment AS enrollment_id')
            ->join('courses c', 'c.id_course = cert.id_course_certificate', 'inner')
            ->join('users u', 'u.id = cert.id_user_certificate', 'inner')
            ->join('enrollments e', 'e.id_student_enrollment = cert.id_user_certificate AND e.id_course_enrollment = cert.id_course_certificate', 'left')
            ->where('c.id_instructor_course', $instructorUserId)
            ->orderBy('cert.created_at', 'DESC')
            ->get()
            ->getResult();
    }

    /**
     * (Opcional) INSTRUTOR: somente pendentes (sem PDF)
     */
    public function getPendingForInstructor(int $instructorId): array
    {
        return $this->db->table('courses c')
            ->select('c.id_course, c.title_course, cert.*')
            ->join('instructors i', 'i.id_instructor = c.id_instructor_course', 'inner')
            ->join('certificates cert', 'cert.id_course_certificate = c.id_course', 'inner')
            ->where('i.id_instructor', $instructorId)
            ->where('cert.pdf_path_certificate IS NULL', null, false)
            ->orderBy('cert.created_at', 'DESC')
            ->get()
            ->getResult();
    }
}


