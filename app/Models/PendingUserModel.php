<?php

namespace App\Models;

use CodeIgniter\Model;

class PendingUserModel extends Model
{
    protected $table            = 'pending_users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['username', 'email', 'course_id', 'status'];

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
    protected $validationRules = [
        'username'  => 'required|min_length[3]|max_length[50]',
        'email'     => 'required|valid_email|is_unique[pending_users.email]',
        'course_id' => 'required|integer',
        'status'    => 'permit_empty',
    ];

    protected $validationMessages = [
        'username' => [
            'required'   => 'O nome de usuário é obrigatório.',
            'min_length' => 'O nome de usuário deve ter pelo menos 3 caracteres.',
            'max_length' => 'O nome de usuário não pode ter mais que 50 caracteres.',
        ],
        'email' => [
            'required'    => 'O email é obrigatório.',
            'valid_email' => 'Forneça um email válido.',
            'is_unique'   => 'Este email já está registado como pendente.',
        ],
        'course_id' => [
            'required' => 'O curso é obrigatório.',
            'integer'  => 'O ID do curso deve ser um número inteiro.',
        ],
        'status' => [
            'in_list' => 'O status deve ser waiting_payment.',
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
