<?php

namespace App\Models;

use CodeIgniter\Model;

class JitsiRecordingModel extends Model
{
    protected $table = 'jitsi_recordings';
    protected $primaryKey = 'id_jitsi_recording';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_jitsi_session',
        'recording_url',
        'provider_recording_id',
        'recording_mode',
        'status_recording',
        'duration_seconds',
        'is_published',
        'published_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'id_jitsi_session' => 'required|integer',
        'recording_url' => 'required|max_length[2048]',
        'provider_recording_id' => 'permit_empty|max_length[255]',
        'recording_mode' => 'required|in_list[file,stream,local,manual]',
        'status_recording' => 'required|in_list[pending,processing,ready,failed]',
        'duration_seconds' => 'permit_empty|integer',
        'is_published' => 'permit_empty|in_list[0,1]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];
}
