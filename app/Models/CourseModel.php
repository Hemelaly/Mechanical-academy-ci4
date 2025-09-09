<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table            = 'courses';
    protected $primaryKey       = 'id_course';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['title_course', 'subtitle_course', 'id_instructor_course', 'description_course', 'image_course', 'status_course', 'price_course', 'created_at', 'updated_at'];

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
        'title_course'       => 'required|string|max_length[150]',
        'id_instructor_course' => 'required|integer|is_not_unique[instructors.id_instructor]',
        'description_course' => 'required|string',
        'price_course'       => 'required|decimal',
        'status_course'      => 'required|in_list[Ativo,Rascunho,Arquivado]',
    ];
    protected $validationMessages   = [
        'title_course' => [
            'required'   => 'O campo título é obrigatório.',
            'string'     => 'O campo título deve ser texto.',
            'max_length' => 'O título não pode conter mais do que 150 caracteres.',
        ],
        'id_instructor_course' => [
            'required'      => 'O campo ID do instrutor é obrigatório.',
            'integer'       => 'O campo ID do instrutor deve ser um número inteiro.',
            'is_not_unique' => 'O instrutor com este ID não existe.',
        ],
        'description_course' => [
            'required'   => 'O campo descrição é obrigatório.',
            'string'     => 'O campo descrição deve ser texto.',
        ],
        'price_course' => [
            'required'   => 'O campo preço é obrigatório.',
            'decimal'    => 'O campo preço deve ser um número decimal.',
        ],
        'status_course' => [
            'required'   => 'O campo status é obrigatório.',
            'in_list'    => 'O status deve ser um dos seguintes: Ativo, Rascunho, Arquivado.',
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

    public function getCoursesByInstructor($instructorId)
    {
        return $this->where('id_instructor_course', $instructorId)->findAll();
    }

    public function getRelInstructor()
    {
        return $this->select('
            courses.id_course,
            courses.title_course,
            courses.image_course,
            courses.description_course,
            courses.price_course,
            instructors.id_instructor,
            instructors.name_instructor,
            instructors.email_instructor
        ')
            ->join('instructors', 'instructors.id_instructor = courses.id_instructor_course')
            ->findAll();
    }
}
