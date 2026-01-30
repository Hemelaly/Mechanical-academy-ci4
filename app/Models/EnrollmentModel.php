<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table            = 'enrollments';
    protected $primaryKey       = 'id_enrollment';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_student_enrollment', 'id_course_enrollment', 'enrolled_at_enrollment', 'status_enrollment', 'id_course_enrollment'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'id_student_enrollment'   => 'required|integer|is_not_unique[users.id]',
        'id_course_enrollment'    => 'required|integer|is_not_unique[courses.id_course]',
        'enrolled_at_enrollment'  => 'required|valid_date',
        'status_enrollment'       => 'required|in_list[ativa,pendente,cancelada]',
    ];
    protected $validationMessages   = [
        'id_student_enrollment' => [
            'required'      => 'O campo ID do estudante Ã© obrigatÃ³rio.',
            'integer'       => 'O campo ID do estudante deve ser um nÃºmero inteiro.',
            'is_not_unique' => 'O estudante com este ID nÃ£o existe.',
        ],
        'id_course_enrollment' => [
            'required'      => 'O campo ID do curso Ã© obrigatÃ³rio.',
            'integer'       => 'O campo ID do curso deve ser um nÃºmero inteiro.',
            'is_not_unique' => 'O curso com este ID nÃ£o existe.',
        ],
        'enrolled_at_enrollment' => [
            'required'   => 'O campo data de inscriÃ§Ã£o Ã© obrigatÃ³rio.',
            'valid_date' => 'Por favor, insira uma data vÃ¡lida.',
        ],
        'status_enrollment' => [
            'required'   => 'O campo status da inscriÃ§Ã£o Ã© obrigatÃ³rio.',
            'in_list'   => 'O status da inscriÃ§Ã£o deve ser um dos seguintes: ativa, pendente, cancelada.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    public function getStudentEnrolledCourses($studentId)
    {
        return $this->select('
                    enrollments.id_enrollment,
                    enrollments.enrolled_at_enrollment,
                    enrollments.status_enrollment,
                    
                    students.id_student,
                    students.name_student,
                    students.email_student,
                    
                    courses.id_course,
                    courses.title_course,
                    courses.image_course,
                    courses.description_course,
                    courses.price_course,
                    
                    users.id,
                    users.username,
                ')
            ->join('students', 'students.id_user_student = enrollments.id_student_enrollment')
            ->join('courses', 'courses.id_course = enrollments.id_course_enrollment')
            ->join('users', 'users.id = courses.id_instructor_course') // join com instrutor
            ->where('enrollments.id_student_enrollment', $studentId)
            ->findAll();
    }

    public function getInstructorEnrollments($instructorId)
    {
        return $this->select([
            'enrollments.id_enrollment',
            'enrollments.enrolled_at_enrollment',
            'enrollments.status_enrollment',
            'enrollments.progress_enrollment',
            'enrollments.updated_at AS last_enrollment_update',

            'students.id_student AS student_id',
            'students.name_student AS name_student',
            'students.email_student AS email_student',

            'courses.id_course AS course_id',
            'courses.title_course AS title_course',

            'payments.id_payment AS payment_id',
            'payments.status_payment AS status_payment',
            'payments.proof_file_payment AS proof_file_payment',
        ])
        ->select('(SELECT MAX(COALESCE(p.updated_at, p.created_at, p.completed_at_progress)) FROM progress p WHERE p.id_enrollment_progress = enrollments.id_enrollment) AS last_activity', false)
        ->join('courses', 'courses.id_course = enrollments.id_course_enrollment')
        ->join('students', 'students.id_user_student = enrollments.id_student_enrollment')
        ->join('payments', 'payments.id_enrollment_payment = enrollments.id_enrollment', 'left')
        ->where('courses.id_instructor_course', $instructorId);
    }
}

