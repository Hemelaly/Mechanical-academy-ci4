<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\LessonDiscussionModel;

class LessonDiscussions extends BaseController
{
    public function index(int $lessonId)
    {
        $access = $this->authorizeLessonAccess($lessonId);
        if ($access instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $access;
        }

        $items = (new LessonDiscussionModel())->forLesson($lessonId, 150);

        return $this->response->setJSON([
            'ok'    => true,
            'items' => array_map([$this, 'formatThread'], $items),
            'total' => $this->countPosts($items),
        ]);
    }

    public function store(int $lessonId)
    {
        $access = $this->authorizeLessonAccess($lessonId);
        if ($access instanceof \CodeIgniter\HTTP\ResponseInterface) {
            return $access;
        }

        [$user, $lesson] = $access;
        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost();
        }

        $body = trim((string) ($payload['body'] ?? ''));
        $parentId = (int) ($payload['parent_id'] ?? 0);

        if (mb_strlen($body) < 2) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'message' => 'Escreva uma mensagem com pelo menos 2 caracteres.',
            ]);
        }

        if (mb_strlen($body) > 4000) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'message' => 'A mensagem é demasiado longa (máx. 4000 caracteres).',
            ]);
        }

        $model = new LessonDiscussionModel();

        if ($parentId > 0) {
            $parent = $model->find($parentId);
            if (! $parent || (int) ($parent['id_lesson_discussion'] ?? 0) !== $lessonId) {
                return $this->response->setStatusCode(422)->setJSON([
                    'ok' => false,
                    'message' => 'Comentário pai inválido.',
                ]);
            }
            // Apenas 1 nível de resposta
            if (! empty($parent['id_parent_discussion'])) {
                $parentId = (int) $parent['id_parent_discussion'];
            }
        } else {
            $parentId = 0;
        }

        $now = date('Y-m-d H:i:s');
        $id = $model->insert([
            'id_lesson_discussion'  => $lessonId,
            'id_course_discussion'  => (int) $lesson['id_course'],
            'id_user_discussion'    => (int) $user->id,
            'id_parent_discussion'  => $parentId > 0 ? $parentId : null,
            'body_discussion'       => $body,
            'created_at'            => $now,
            'updated_at'            => $now,
        ], true);

        if (! $id) {
            return $this->response->setStatusCode(500)->setJSON([
                'ok' => false,
                'message' => 'Não foi possível publicar a mensagem.',
            ]);
        }

        $items = $model->forLesson($lessonId, 150);

        return $this->response
            ->setHeader('X-CSRF-Hash', csrf_hash())
            ->setJSON([
                'ok'      => true,
                'message' => 'Mensagem publicada.',
                'id'      => (int) $id,
                'items'   => array_map([$this, 'formatThread'], $items),
                'total'   => $this->countPosts($items),
            ]);
    }

    /**
     * @return array{0: object, 1: array}|ResponseInterface
     */
    private function authorizeLessonAccess(int $lessonId)
    {
        $user = auth()->user();
        if (! $user) {
            return $this->response->setStatusCode(401)->setJSON([
                'ok' => false,
                'message' => 'Faça login para participar na discussão.',
            ]);
        }

        $db = db_connect();
        $lesson = $db->table('lessons l')
            ->select('l.id_lesson, l.title_lesson, m.id_course_module AS id_course, c.id_instructor_course')
            ->join('modules m', 'm.id_module = l.id_module_lesson', 'inner')
            ->join('courses c', 'c.id_course = m.id_course_module', 'inner')
            ->where('l.id_lesson', $lessonId)
            ->get()
            ->getRowArray();

        if (! $lesson) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok' => false,
                'message' => 'Aula não encontrada.',
            ]);
        }

        $role = strtolower((string) ($user->role ?? ''));
        $allowed = false;

        if ($role === 'admin') {
            $allowed = true;
        } elseif ($role === 'instructor' && (int) $lesson['id_instructor_course'] === (int) $user->id) {
            $allowed = true;
        } elseif ($role === 'student') {
            $enrollment = (new EnrollmentModel())
                ->where('id_student_enrollment', (int) $user->id)
                ->where('id_course_enrollment', (int) $lesson['id_course'])
                ->whereIn('status_enrollment', ['ativa', 'pendente'])
                ->first();
            $allowed = ! empty($enrollment);
        }

        if (! $allowed) {
            return $this->response->setStatusCode(403)->setJSON([
                'ok' => false,
                'message' => 'Sem permissão para aceder a esta discussão.',
            ]);
        }

        return [$user, $lesson];
    }

    /**
     * @param array<string, mixed> $thread
     * @return array<string, mixed>
     */
    private function formatThread(array $thread): array
    {
        $replies = [];
        foreach ($thread['replies'] ?? [] as $reply) {
            if (is_array($reply)) {
                $replies[] = $this->formatPost($reply);
            }
        }

        $post = $this->formatPost($thread);
        $post['replies'] = $replies;

        return $post;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function formatPost(array $row): array
    {
        $username = trim((string) ($row['username'] ?? 'Utilizador'));
        $created = (string) ($row['created_at'] ?? '');
        $when = $created !== '' ? date('d/m/Y H:i', strtotime($created)) : '';

        return [
            'id'         => (int) ($row['id_discussion'] ?? 0),
            'parent_id'  => isset($row['id_parent_discussion']) ? (int) $row['id_parent_discussion'] : null,
            'body'       => (string) ($row['body_discussion'] ?? ''),
            'username'   => $username,
            'initials'   => $this->initials($username),
            'avatar'     => ! empty($row['img']) ? base_url(ltrim((string) $row['img'], '/')) : null,
            'created_at' => $when,
        ];
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $letters = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $letters .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $letters !== '' ? $letters : 'U';
    }

    /**
     * @param list<array<string, mixed>> $items
     */
    private function countPosts(array $items): int
    {
        $total = 0;
        foreach ($items as $item) {
            $total++;
            $total += count($item['replies'] ?? []);
        }

        return $total;
    }
}
