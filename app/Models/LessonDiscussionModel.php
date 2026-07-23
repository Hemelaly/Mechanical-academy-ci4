<?php

namespace App\Models;

use CodeIgniter\Model;

class LessonDiscussionModel extends Model
{
    protected $table            = 'lesson_discussions';
    protected $primaryKey       = 'id_discussion';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_lesson_discussion',
        'id_course_discussion',
        'id_user_discussion',
        'id_parent_discussion',
        'body_discussion',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false;

    /**
     * @return list<array<string, mixed>>
     */
    public function forLesson(int $lessonId, int $limit = 100): array
    {
        $rows = $this->db->table('lesson_discussions d')
            ->select('d.*, u.username, u.img')
            ->join('users u', 'u.id = d.id_user_discussion', 'left')
            ->where('d.id_lesson_discussion', $lessonId)
            ->orderBy('d.created_at', 'ASC')
            ->orderBy('d.id_discussion', 'ASC')
            ->limit(max(1, min(200, $limit)))
            ->get()
            ->getResultArray();

        $byId = [];
        foreach ($rows as $row) {
            $row['replies'] = [];
            $byId[(int) $row['id_discussion']] = $row;
        }

        $roots = [];
        foreach ($byId as $id => &$row) {
            $parentId = isset($row['id_parent_discussion']) ? (int) $row['id_parent_discussion'] : 0;
            if ($parentId > 0 && isset($byId[$parentId])) {
                $byId[$parentId]['replies'][] = &$row;
            } else {
                $roots[] = &$row;
            }
        }
        unset($row);

        return $roots;
    }
}
