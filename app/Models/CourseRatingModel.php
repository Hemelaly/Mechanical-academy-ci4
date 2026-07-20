<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseRatingModel extends Model
{
    protected $table         = 'course_ratings';
    protected $primaryKey    = 'id_rating';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id_course_rating',
        'id_user_rating',
        'id_enrollment_rating',
        'score_rating',
        'comment_rating',
        'created_at',
        'updated_at',
    ];

    protected $validationRules = [
        'id_course_rating' => 'required|integer',
        'id_user_rating'   => 'required|integer',
        'score_rating'     => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
        'comment_rating'   => 'permit_empty|string|max_length[2000]',
    ];

    public function getCourseSummary(int $courseId): array
    {
        $row = $this->selectAvg('score_rating', 'avg_score')
            ->selectCount('id_rating', 'total_ratings')
            ->where('id_course_rating', $courseId)
            ->first();

        return [
            'average' => round((float) ($row['avg_score'] ?? 0), 1),
            'total'   => (int) ($row['total_ratings'] ?? 0),
        ];
    }

    public function getForCourse(int $courseId, int $limit = 12): array
    {
        return $this->db->table('course_ratings r')
            ->select('r.*, COALESCE(s.name_student, u.username) AS student_name', false)
            ->join('users u', 'u.id = r.id_user_rating', 'left')
            ->join('students s', 's.id_user_student = u.id', 'left')
            ->where('r.id_course_rating', $courseId)
            ->orderBy('r.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
