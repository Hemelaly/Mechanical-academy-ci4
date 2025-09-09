<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuleModel extends Model
{
    protected $table            = 'modules';
    protected $primaryKey       = 'id_module';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_course_module', 'title_module', 'description_module', 'position_module', 'created_at', 'updated_at'];

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
        'id_course_module'  => 'required|integer',
        'title_module'       => 'required|string|max_length[255]',
        'description_module' => 'permit_empty|string',
        'position_module' => 'required|integer',
    ];
    protected $validationMessages   = [
        'id_course_module'  => [
            'required' => 'Course ID is required.',
            'integer'  => 'Course ID must be an integer.',
        ],
        'title_module'       => [
            'required'   => 'Module title is required.',
            'string'     => 'Module title must be a string.',
            'max_length' => 'Module title cannot exceed 255 characters.',
        ],
        'description_module' => [
            'string' => 'Module description must be a string.',
        ],
        'position_module'       => [
            'required' => 'Module order is required.',
            'integer'  => 'Module order must be an integer.',
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
