<?php

namespace App\Models;

use CodeIgniter\Model;

class JitsiModel extends Model
{
    protected $table            = 'jitsi';
    protected $primaryKey       = 'id_jitsi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'title_jitsi',
        'description_jitsi',
        'id_course_jitsi',
        'class_type_jitsi',
        'meeting_date_jitsi',
        'start_time_jitsi',
        'end_time_jitsi',
        'status_jitsi',
        'privacy_jitsi',
        'password_jitsi',
        'recording_jitsi',
        'chat_jitsi',
        'screenshare_jitsi',
        'room_jitsi',
        'id_user_jitsi',
    ];

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
    protected $validationRules      = [];
    protected $validationMessages   = [];
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
