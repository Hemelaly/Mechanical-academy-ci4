<?php

namespace App\Models;

use CodeIgniter\Model;

class LessonModel extends Model
{
    protected $table            = 'lessons';
    protected $primaryKey       = 'id_lesson';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_module_lesson', 'title_lesson', 'content_lesson', 'type_lesson', 'duration_lesson', 'position_lesson', 'created_at', 'video_url_lesson'];

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
        'id_module_lesson'  => 'required|integer',
        'title_lesson'       => 'required|string|max_length[255]',
        'content_lesson'     => 'permit_empty|string',
        'position_lesson'       => 'required|integer',
        'video_url_lesson'   => 'permit_empty|valid_url',
    ];
    protected $validationMessages   = [
        'id_module_lesson'  => [
            'required' => 'Module ID is required.',
            'integer'  => 'Module ID must be an integer.',
        ],
        'title_lesson'       => [
            'required'   => 'Lesson title is required.',
            'string'     => 'Lesson title must be a string.',
            'max_length' => 'Lesson title cannot exceed 255 characters.',
        ],
        'position_lesson'       => [
            'required' => 'Lesson order is required.',
            'integer'  => 'Lesson order must be an integer.',
        ],
        'video_url_lesson'   => [
            'valid_url' => 'Lesson video URL must be a valid URL.',
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

    public function getLessonsByModule($moduleId)
    {
        return $this->where('id_module_lesson', $moduleId)->orderBy('position_lesson', 'ASC')->findAll();
    }

}
