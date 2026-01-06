<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificateModel extends Model
{
    protected $table            = 'certificates';
    protected $primaryKey       = 'id_certificate';
    protected $returnType       = 'object';
    protected $useAutoIncrement = true;

    // created_at e updated_at existem na tua DB
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'id_user_cerificate',
        'id_course_cerificate',
        'uuid_certificate',
        'number_certificate',
        'issued_at_cerificate',
        'pdf_path_cerificate',
        'hash_certificate',
        'revoked_at_cerificate',
        // created_at e updated_at são geridos automaticamente pelo CI4 quando useTimestamps=true
    ];

    // Helpers úteis
    public function findActiveByUuid(string $uuid): ?array
    {
        return $this->where('uuid_certificate', $uuid)
            ->where('revoked_at_cerificate', null)
            ->first();
    }

    public function findActiveByUserCourse(int $userId, int $courseId): ?array
    {
        return $this->where('id_user_cerificate', $userId)
            ->where('id_course_cerificate', $courseId)
            ->where('revoked_at_cerificate', null)
            ->first();
    }
}
