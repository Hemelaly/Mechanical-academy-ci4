<?php

namespace App\Models;

use CodeIgniter\Model;

class InstructorModel extends Model
{
    protected $table            = 'instructors';
    protected $primaryKey       = 'id_instructor';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name_instructor', 'email_instructor', 'id_user_instructor', 'created_at', 'updated_at'];

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
        'name_instructor'     => 'required|string|max_length[100]',
        'email_instructor'    => 'required|valid_email|is_unique[instructors.email_instructor,id_instructor,{id_instructor}]',
        'id_user_instructor' => 'required|integer',
    ];
    protected $validationMessages   = [
        'name_instructor' => [
            'required'   => 'O campo nome é obrigatório.',
            'string'     => 'O campo nome deve ser texto.',
            'max_length' => 'O nome não pode conter mais do que 100 caracteres.',
        ],
        'email_instructor' => [
            'required'   => 'O campo email é obrigatório.',
            'valid_email'=> 'Por favor, escreva um email válido.',
            'is_unique'  => 'Este email já foi registrado! Tente outro.',
        ],
        'id_user_instructor' => [
            'required'   => 'O campo senha é obrigatório.',
            'string'     => 'O campo id_user_instructor deve ser do tipo INT.',
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
