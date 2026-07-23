<?php

// app/Controllers/Student/LessonsController.php
namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Services\CertificateService;
use App\Services\ProgressService;

class LessonsController extends BaseController
{
    public function complete()
    {
        $payload      = $this->request->getJSON(true);
        $idLesson     = (int)($payload['lesson_id'] ?? 0);
        $idEnrollment = (int)($payload['enrollment_id'] ?? 0);
        $score        = isset($payload['score']) ? (float) $payload['score'] : null;
        if (!$idLesson || !$idEnrollment) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Parâmetros inválidos']);
        }

        $db = db_connect();

        $lessonMinScoreQuery = $db->table('lessons l')
            ->select('l.id_module_lesson, m.min_score_module')
            ->join('modules m', 'm.id_module = l.id_module_lesson', 'left')
            ->where('l.id_lesson', $idLesson)
            ->get()
            ->getRowArray();

        $minScore = 75;
        if (!empty($lessonMinScoreQuery['min_score_module'])) {
            $minScore = (int) $lessonMinScoreQuery['min_score_module'];
        }

        $shouldMarkComplete = ($score === null) || ($score >= $minScore);
        $completedAtValue = $shouldMarkComplete ? date('Y-m-d H:i:s') : null;

        // upsert progresso
        $db->query(
            'INSERT INTO progress (id_enrollment_progress, id_lesson_progress, completed_at_progress, score_progress, created_at, updated_at)
         VALUES (?, ?, ?, ?, NOW(), NOW())
         ON DUPLICATE KEY UPDATE completed_at_progress = VALUES(completed_at_progress), score_progress = IFNULL(VALUES(score_progress), score_progress), updated_at = NOW()',
            [$idEnrollment, $idLesson, $completedAtValue, $score]
        );

        $percent = (new \App\Services\ProgressService($db))->recalcAndSave($idEnrollment);

        $completedAtIso = null;
        $availableAtIso = null;
        $pdfReady = false;
        $certificateOk = true;
        $certificateMessage = null;

        if ($percent >= 100) {
            try {
                $certificateService = new CertificateService($db);
                $result = $certificateService->ensureForEnrollment($idEnrollment, (int) auth()->id());
                $completedAtIso = $result['completed_at'] ?? null;
                $availableAtIso = $result['available_at'] ?? null;
                $pdfReady = !empty($result['pdf_ready']);
                $certificateOk = !empty($result['ok']);
                $certificateMessage = $result['message'] ?? null;
            } catch (\Throwable $e) {
                // A conclusão da aula não pode falhar por causa do PDF/certificado.
                log_message('error', 'Falha ao preparar certificado no complete da aula: {message}', [
                    'message' => $e->getMessage(),
                ]);
                $certificateOk = false;
                $certificateMessage = 'Curso concluído, mas o certificado ainda não pôde ser gerado.';
            }
        }

        return $this->response->setHeader('X-CSRF-Hash', csrf_hash())
            ->setJSON([
                'ok'           => true,
                'progress'     => $percent,
                'completed_at' => $completedAtIso,
                'available_at' => $availableAtIso,
                'pdf_ready'    => $pdfReady,
                'certificate_ok' => $certificateOk,
                'certificate_message' => $certificateMessage,
            ]);
    }

    public function uncomplete()
    {
        $payload      = $this->request->getJSON(true);
        $idLesson     = (int)($payload['lesson_id'] ?? 0);
        $idEnrollment = (int)($payload['enrollment_id'] ?? 0);

        if (!$idLesson || !$idEnrollment) {
            return $this->response->setJSON(['ok' => false, 'message' => 'Parâmetros inválidos']);
        }

        $db = db_connect();

        // opção A: manter linha e zerar data
        $db->table('progress')
            ->where('id_enrollment_progress', $idEnrollment)
            ->where('id_lesson_progress', $idLesson)
            ->update(['completed_at_progress' => null, 'updated_at' => date('Y-m-d H:i:s')]);

        // (ou opção B: deletar a linha)

        $percent = (new ProgressService($db))->recalcAndSave($idEnrollment);

        if ($percent < 100) {
            $db->table('enrollments')
                ->where('id_enrollment', $idEnrollment)
                ->update(['completed_enrollment' => null]);
        }

        return $this->response->setHeader('X-CSRF-Hash', csrf_hash())
            ->setJSON(['ok' => true, 'progress' => $percent]);
    }

    /**
     * Download seguro do material da aula (MIME correcto; evita 404→JSON).
     */
    public function download(int $lessonId)
    {
        $user = auth()->user();
        if (! $user) {
            return redirect()->to('/login');
        }

        $db = db_connect();
        $row = $db->table('lessons l')
            ->select('l.id_lesson, l.attachment_path_lesson, l.attachment_name_lesson, l.title_lesson, c.id_course, c.id_instructor_course, c.title_course')
            ->join('modules m', 'm.id_module = l.id_module_lesson', 'inner')
            ->join('courses c', 'c.id_course = m.id_course_module', 'inner')
            ->where('l.id_lesson', $lessonId)
            ->get()
            ->getRowArray();

        if (! $row || empty($row['attachment_path_lesson'])) {
            return $this->attachmentNotFound('Material de apoio não encontrado para esta aula.');
        }

        $role = (string) ($user->role ?? '');
        $allowed = false;

        if ($role === 'admin') {
            $allowed = true;
        } elseif ($role === 'instructor' && (int) $row['id_instructor_course'] === (int) $user->id) {
            $allowed = true;
        } elseif ($role === 'student') {
            $enrollment = $db->table('enrollments')
                ->where('id_student_enrollment', (int) $user->id)
                ->where('id_course_enrollment', (int) $row['id_course'])
                ->get()
                ->getRowArray();
            $allowed = ! empty($enrollment);
        }

        if (! $allowed) {
            return $this->attachmentNotFound('Sem permissão para descarregar este ficheiro.', 403);
        }

        $relative = str_replace(['../', '..\\'], '', (string) $row['attachment_path_lesson']);
        $relative = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative), DIRECTORY_SEPARATOR);
        $fullPath = $this->resolveLessonAttachmentPath($relative);

        if ($fullPath === null || ! is_file($fullPath)) {
            log_message('error', 'Anexo de aula em falta: {path}', ['path' => $relative]);

            return $this->attachmentNotFound('O ficheiro já não está disponível no servidor. Peça ao instrutor para voltar a enviar o material.');
        }

        $downloadName = trim((string) ($row['attachment_name_lesson'] ?? ''));
        if ($downloadName === '') {
            $downloadName = basename($fullPath);
        }

        // Garante extensão no nome de download (evita browser gravar como .json)
        $realExt = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $nameExt = strtolower(pathinfo($downloadName, PATHINFO_EXTENSION));
        if ($realExt !== '' && $nameExt === '') {
            $downloadName .= '.' . $realExt;
        }

        $mime = $this->mimeForExtension($realExt);

        return $this->response
            ->download($fullPath, null)
            ->setFileName($downloadName)
            ->setHeader('Content-Type', $mime)
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Cache-Control', 'private, no-store');
    }

    /**
     * Resolve o caminho físico do anexo (writable actual + pasta pública legado).
     */
    private function resolveLessonAttachmentPath(string $relative): ?string
    {
        if ($relative === '') {
            return null;
        }

        $candidates = [];

        // Novo local (uploads protegidos)
        $candidates[] = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'lesson_files' . DIRECTORY_SEPARATOR . basename($relative);

        // Legado: public/assets/instructor/lesson_files
        $candidates[] = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'instructor' . DIRECTORY_SEPARATOR . 'lesson_files' . DIRECTORY_SEPARATOR . basename($relative);

        // Se a BD já tiver um caminho relativo completo
        if (str_contains($relative, DIRECTORY_SEPARATOR) || str_contains($relative, '/') || str_contains($relative, '\\')) {
            $candidates[] = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . $relative;
            $candidates[] = rtrim(ROOTPATH, '/\\') . DIRECTORY_SEPARATOR . $relative;
            $candidates[] = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . $relative;
        }

        foreach ($candidates as $path) {
            $real = realpath($path);
            if ($real !== false && is_file($real)) {
                return $real;
            }
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    private function mimeForExtension(string $ext): string
    {
        return match ($ext) {
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            'rar' => 'application/vnd.rar',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            default => 'application/octet-stream',
        };
    }

    private function attachmentNotFound(string $message, int $status = 404)
    {
        if ($this->request->isAJAX() || str_contains((string) $this->request->getHeaderLine('Accept'), 'application/json')) {
            return $this->response
                ->setStatusCode($status)
                ->setJSON(['ok' => false, 'message' => $message]);
        }

        return redirect()->to(previous_url() ?: site_url('student/dashboard'))->with('error', $message);
    }
}
