<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table            = 'projects';
    protected $primaryKey       = 'id_project';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_course_project', 'img_project', 'title_project', 'description_project', 'created_at', 'updated_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'id_course_project' => 'required|integer|is_not_unique[courses.id_course]',
        'img_project' => 'permit_empty|string|max_length[255]',
        'title_project' => 'string|required|max_length[100]',
        'description_project' => 'string|required',
    ];
    protected $validationMessages   = [
         'id_course_project' => [
            'required'   => 'O campo id_course_project é obrigatório.',
            'integer'     => 'O campo id_course_project deve ser do tipo inteiro.',
            'is_not_unique' => 'O id_course_project é uma FK de [course.id_course].',
        ],
         'img_project' => [
            'string'     => 'O campo img_project deve ser do tipo texto.',
            'max_length' => 'O img_project nao pode ter mais do que 255 caracteres.',
        ],
         'title_project' => [
            'string'     => 'O campo title_project deve ser do tipo texto.',
            'required'     => 'O campo title_project é obrigatório.',
            'max_length' => 'O title_project nao pode ter mais do que 100 caracteres.',
        ],
         'description_project' => [
            'string'     => 'O campo description_project deve ser do tipo texto.',
            'required'     => 'O campo description_project é obrigatório.',
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
}
