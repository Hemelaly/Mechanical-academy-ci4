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
        'id_student_enrollment' => 'required|integer|is_not_unique[students.id_student]',
        'id_course_enrollment'  => 'required|integer|is_not_unique[courses.id_course]',
        'enrolled_at_enrollment'       => 'required|valid_date',
        'status_enrollment'     => 'required|in_list[Ativo,Pendente,Cancelado]',
    ];
    protected $validationMessages   = [
        'id_student_enrollment' => [
            'required'      => 'O campo ID do estudante é obrigatório.',
            'integer'       => 'O campo ID do estudante deve ser um número inteiro.',
            'is_not_unique' => 'O estudante com este ID não existe.',
        ],
        'id_course_enrollment' => [
            'required'      => 'O campo ID do curso é obrigatório.',
            'integer'       => 'O campo ID do curso deve ser um número inteiro.',
            'is_not_unique' => 'O curso com este ID não existe.',
        ],
        'enrolled_at_enrollment' => [
            'required'   => 'O campo data de inscrição é obrigatório.',
            'valid_date' => 'Por favor, insira uma data válida.',
        ],
        'status_enrollment' => [
            'required'   => 'O campo status da inscrição é obrigatório.',
            'in_list'   => 'O status da inscrição deve ser um dos seguintes: active, completed, cancelled.',
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
                    
                    instructors.id_instructor,
                    instructors.name_instructor,
                    instructors.email_instructor
                ')
            ->join('students', 'students.id_student = enrollments.id_student_enrollment')
            ->join('courses', 'courses.id_course = enrollments.id_course_enrollment')
            ->join('instructors', 'instructors.id_instructor = courses.id_instructor_course') // join com instrutor
            ->where('enrollments.id_student_enrollment', $studentId)
            ->findAll();
    }

    public function getInstructorEnrollments($instructorId)
    {
        return $this->select('
                enrollments.id_enrollment,
                enrollments.enrolled_at_enrollment,
                enrollments.status_enrollment,
                students.id_student,
                students.name_student,
                students.email_student,
                courses.id_course,
                courses.title_course
            ')
            ->join('courses', 'courses.id_course = enrollments.id_course_enrollment')
            ->join('students', 'students.id_user_student = enrollments.id_student_enrollment')
            ->where('courses.id_instructor_course', $instructorId)
            ->findAll();
    }
}
