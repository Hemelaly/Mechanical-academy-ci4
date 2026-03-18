<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id_payment';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user_payment',
        'id_course_payment',
        'amount_payment',
        'status_payment',
        'reference_payment',
        'guest_email_payment',
        'guest_name_payment',
        'id_enrollment_payment',
        'proof_file_payment',
        'approved_by_payment',
        'created_at',
        'updated_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules      = [
        'id_user_payment'     => 'required|integer',
        'id_course_payment'   => 'required|integer',
        'amount_payment'      => 'required|decimal',
        'status_payment'      => 'required|in_list[Pendente,Aprovado,Rejeitado]',
        'reference_payment'   => 'permit_empty|string|max_length[50]',
        'guest_email_payment' => 'permit_empty|valid_email|max_length[150]',
        'guest_name_payment'  => 'permit_empty|string|max_length[100]',
        'proof_file_payment'  => 'permit_empty|string|max_length[255]',
        'approved_by_payment' => 'permit_empty|integer',
    ];
    protected $validationMessages   = [
        'id_user_payment' => [
            'required' => 'O ID do usuario e obrigatorio.',
            'integer'  => 'O ID do usuario deve ser um numero inteiro.',
        ],
        'id_course_payment' => [
            'required' => 'O ID do curso e obrigatorio.',
            'integer'  => 'O ID do curso deve ser um numero inteiro.',
        ],
        'amount_payment' => [
            'required' => 'O valor do pagamento e obrigatorio.',
            'decimal'  => 'O valor do pagamento deve ser um numero decimal.',
        ],
        'status_payment' => [
            'required' => 'O status do pagamento e obrigatorio.',
            'in_list'  => 'O status do pagamento deve ser Pendente, Aprovado ou Rejeitado.',
        ],
        'reference_payment' => [
            'string'     => 'A referencia do pagamento deve ser uma string.',
            'max_length' => 'A referencia do pagamento nao pode exceder 50 caracteres.',
        ],
        'guest_email_payment' => [
            'valid_email' => 'O email do convidado deve ser valido.',
            'max_length'  => 'O email do convidado nao pode exceder 150 caracteres.',
        ],
        'guest_name_payment' => [
            'string'     => 'O nome do convidado deve ser uma string.',
            'max_length' => 'O nome do convidado nao pode exceder 100 caracteres.',
        ],
        'proof_file_payment' => [
            'string'     => 'O arquivo de comprovante deve ser uma string.',
            'max_length' => 'O arquivo de comprovante nao pode exceder 255 caracteres.',
        ],
        'approved_by_payment' => [
            'integer' => 'O ID do aprovador deve ser um numero inteiro.',
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

    public function getInstructorPendingPayments($instructorId)
    {
        return $this->select('
            payments.id_payment,
            payments.amount_payment,
            payments.status_payment,
            payments.proof_file_payment,
            payments.created_at,

            pending_users.id as id_user_payment,
            pending_users.username,
            pending_users.email,

            courses.id_course,
            courses.title_course
        ')
            ->join('pending_users', 'pending_users.id = payments.id_user_payment')
            ->join('courses', 'courses.id_course = payments.id_course_payment')
            ->where('courses.id_instructor_course', $instructorId)
            ->where('payments.status_payment', 'Pendente')
            ->findAll();
    }
}
