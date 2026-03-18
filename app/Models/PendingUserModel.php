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
    protected $allowedFields    = [
        'username',
        'email',
        'course_id',
        'payment_id',
        'status',
        'setup_token',
        'setup_expires_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'username'         => 'required|min_length[3]|max_length[100]',
        'email'            => 'required|valid_email',
        'course_id'        => 'required|integer',
        'payment_id'       => 'permit_empty|integer',
        'status'           => 'permit_empty|in_list[waiting_payment,paid]',
        'setup_token'      => 'permit_empty|max_length[64]',
        'setup_expires_at' => 'permit_empty|valid_date',
    ];

    protected $validationMessages = [
        'username' => [
            'required'   => 'O nome de usuario e obrigatorio.',
            'min_length' => 'O nome de usuario deve ter pelo menos 3 caracteres.',
            'max_length' => 'O nome de usuario nao pode ter mais que 100 caracteres.',
        ],
        'email' => [
            'required'    => 'O email e obrigatorio.',
            'valid_email' => 'Forneca um email valido.',
        ],
        'course_id' => [
            'required' => 'O curso e obrigatorio.',
            'integer'  => 'O ID do curso deve ser um numero inteiro.',
        ],
        'payment_id' => [
            'integer' => 'O ID do pagamento deve ser um numero inteiro.',
        ],
        'status' => [
            'in_list' => 'O status deve ser waiting_payment ou paid.',
        ],
        'setup_token' => [
            'max_length' => 'O token de configuracao nao pode exceder 64 caracteres.',
        ],
        'setup_expires_at' => [
            'valid_date' => 'A expiracao do token deve ser uma data valida.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

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
