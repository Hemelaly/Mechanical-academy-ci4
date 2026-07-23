<?php

namespace App\Controllers\Instructor;

use App\Controllers\BaseController;

class CourseController extends BaseController
{
    private const DEFAULT_MIN_SCORE = 80;

    private ?array $courseColumnsCache = null;

    private function getCourseColumns(): array
    {
        if ($this->courseColumnsCache !== null) {
            return $this->courseColumnsCache;
        }

        $db = \Config\Database::connect();
        $this->courseColumnsCache = array_map('strtolower', $db->getFieldNames('courses'));

        return $this->courseColumnsCache;
    }

    private function courseColumnExists(string $column): bool
    {
        return in_array(strtolower($column), $this->getCourseColumns(), true);
    }

    /**
     * Limpa caches de ficheiros/páginas após publicar ou actualizar curso.
     */
    private function clearAppCaches(): void
    {
        try {
            cache()->clean();
        } catch (\Throwable $e) {
            log_message('warning', 'Falha ao limpar cache: {msg}', ['msg' => $e->getMessage()]);
        }

        $paths = [
            WRITEPATH . 'cache',
            WRITEPATH . 'debugbar',
        ];
        foreach ($paths as $dir) {
            if (! is_dir($dir)) {
                continue;
            }
            foreach (glob(rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }

    private function normalizeCoursePayload(array $payload): array
    {
        if (array_key_exists('learning_course', $payload)) {
            $learning = $payload['learning_course'];
            if ($this->courseColumnExists('learning_course')) {
                $payload['learning_course'] = $learning;
            } elseif ($this->courseColumnExists('what_learn_course')) {
                $payload['what_learn_course'] = $learning;
                unset($payload['learning_course']);
            } else {
                unset($payload['learning_course']);
            }
        }

        if (
            array_key_exists('description_course', $payload)
            && ! $this->courseColumnExists('description_course')
            && $this->courseColumnExists('long_description_course')
        ) {
            $payload['long_description_course'] = $payload['description_course'];
            unset($payload['description_course']);
        }

        $availableColumns = $this->getCourseColumns();
        foreach (array_keys($payload) as $field) {
            if (! in_array(strtolower($field), $availableColumns, true)) {
                unset($payload[$field]);
            }
        }

        return $payload;
    }

    /**
     * Campos comerciais / trial / carga horária.
     * Compatível com os dois schemas de migration:
     * - hours_mode_course + hours_manual_course (decimal)
     * - hours_course + hours_manual_course (flag 0/1)
     *
     * @return array<string, mixed>
     */
    private function extractCommerceFields(array $data, bool $isPaid): array
    {
        $promo = $isPaid ? ($data['promo_price_course'] ?? null) : null;
        $promo = ($promo === '' || $promo === null) ? null : (float) $promo;
        if ($promo !== null && $promo <= 0) {
            $promo = null;
        }

        $hoursMode = strtolower(trim((string) ($data['hours_mode_course'] ?? 'auto')));
        if (! in_array($hoursMode, ['auto', 'manual'], true)) {
            $hoursMode = 'auto';
        }

        $isManual = $hoursMode === 'manual';
        $manualHoursValue = max(0, (float) ($data['hours_manual_course'] ?? $data['hours_course'] ?? 0));

        $out = [
            'promo_price_course' => $isPaid ? $promo : null,
        ];

        if ($this->courseColumnExists('promo_ends_at_course')) {
            $endsRaw = $isPaid ? trim((string) ($data['promo_ends_at_course'] ?? '')) : '';
            if ($promo === null || $endsRaw === '') {
                $out['promo_ends_at_course'] = null;
            } else {
                $ts = strtotime(str_replace('T', ' ', $endsRaw));
                $out['promo_ends_at_course'] = $ts ? date('Y-m-d H:i:s', $ts) : null;
            }
        }

        if ($this->courseColumnExists('hours_mode_course')) {
            $out['hours_mode_course'] = $hoursMode;
        }

        // Schema legado: hours_course (valor) + hours_manual_course (flag NOT NULL)
        if ($this->courseColumnExists('hours_course')) {
            $out['hours_course'] = $isManual ? $manualHoursValue : null;
            if ($this->courseColumnExists('hours_manual_course')) {
                $out['hours_manual_course'] = $isManual ? 1 : 0;
            }
        } elseif ($this->courseColumnExists('hours_manual_course')) {
            // Schema actual: hours_manual_course guarda as horas (nunca null — DBs NOT NULL)
            $out['hours_manual_course'] = $isManual ? $manualHoursValue : 0;
        }

        $freeLessons = max(0, (int) ($data['free_lessons_count_course'] ?? $data['free_lessons_course'] ?? 0));
        if ($this->courseColumnExists('free_lessons_count_course')) {
            $out['free_lessons_count_course'] = $freeLessons;
        }
        if ($this->courseColumnExists('free_lessons_course')) {
            $out['free_lessons_course'] = $freeLessons;
        }

        $whatsapp = preg_replace('/\D+/', '', (string) ($data['whatsapp_contact_course'] ?? $data['whatsapp_course'] ?? '')) ?: null;
        if ($this->courseColumnExists('whatsapp_contact_course')) {
            $out['whatsapp_contact_course'] = $whatsapp;
        }
        if ($this->courseColumnExists('whatsapp_course')) {
            $out['whatsapp_course'] = $whatsapp;
        }

        return $out;
    }

    private function modelErrorMessage($model, string $fallback): string
    {
        $errors = method_exists($model, 'errors') ? (array) $model->errors() : [];
        if (! empty($errors)) {
            return $fallback . ' ' . implode(' ', array_values($errors));
        }

        if (isset($model->db)) {
            $dbError = (array) $model->db->error();
            if (! empty($dbError['message'])) {
                return $fallback . ' ' . $dbError['message'];
            }
        }

        return $fallback;
    }

    private function moveModuleZip($zipFile)
    {
        if (! $zipFile || ! $zipFile->isValid() || $zipFile->hasMoved()) {
            return null;
        }

        $ext = strtolower($zipFile->getClientExtension());
        if (! in_array($ext, ['zip', 'rar'], true)) {
            return null;
        }

        $targetDir = FCPATH . 'assets/instructor/module_zips';
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $newName = $zipFile->getRandomName();
        if (! $zipFile->move($targetDir, $newName)) {
            return null;
        }

        return $newName;
    }

    private function moveLessonAttachment($file)
    {
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $ext = strtolower($file->getClientExtension());
        $allowed = ['zip', 'rar', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'];
        if (! in_array($ext, $allowed, true)) {
            return null;
        }

        // Garante pasta de anexos (writable — sobrevive melhor a deploys)
        $targetDir = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'lesson_files';
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $originalName = $file->getClientName();
        $ext = strtolower($file->getClientExtension());
        // Nome aleatório SEM perder a extensão real do ficheiro
        $newName = bin2hex(random_bytes(8)) . '_' . time() . ($ext !== '' ? '.' . $ext : '');
        if (! $file->move($targetDir, $newName)) {
            log_message('error', 'Falha ao mover anexo de aula: {error}', [
                'error' => $file->getErrorString() . ' (' . $file->getError() . ')',
            ]);

            return null;
        }

        return [
            'path' => $newName,
            'name' => $originalName,
        ];
    }

    private function moveProjectImage($file)
    {
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $ext = strtolower($file->getClientExtension());
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (! in_array($ext, $allowed, true)) {
            return null;
        }

        $targetDir = FCPATH . 'assets/img';
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        if (! $file->move($targetDir, $newName)) {
            return null;
        }

        return $newName;
    }

    private function moveCourseIcon($file)
    {
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $ext = strtolower($file->getClientExtension());
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
        if (! in_array($ext, $allowed, true)) {
            return null;
        }

        $targetDir = FCPATH . 'assets/img';
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        if (! $file->move($targetDir, $newName)) {
            return null;
        }

        return $newName;
    }

    private function normalizeQuizQuestions(array $quizQuestions): array
    {
        $normalized = [];
        foreach ($quizQuestions as $q) {
            if (! is_array($q)) {
                continue;
            }
            $question = trim((string) ($q['question'] ?? ''));
            $options = array_values(array_map('strval', (array) ($q['options'] ?? [])));
            while (count($options) < 4) {
                $options[] = '';
            }
            $options = array_slice($options, 0, 4);
            $correct = (int) ($q['correct'] ?? 0);
            if ($correct < 0 || $correct > 3) {
                $correct = 0;
            }
            $points = (float) ($q['points'] ?? $q['score'] ?? 1);
            if ($points <= 0) {
                $points = 1;
            }
            if ($question === '' && ! array_filter($options)) {
                continue;
            }
            $normalized[] = [
                'question' => $question,
                'options'  => $options,
                'correct'  => $correct,
                'points'   => $points,
            ];
        }

        return $normalized;
    }

    private function lessonHasQuizQuestions(array $lesson): bool
    {
        $quizQuestions = $lesson['quiz_questions'] ?? ($lesson['quiz'] ?? []);
        if (! is_array($quizQuestions)) {
            return false;
        }

        foreach ($quizQuestions as $q) {
            if (! is_array($q)) {
                if (trim((string) $q) !== '') {
                    return true;
                }
                continue;
            }

            $question = trim((string) ($q['question'] ?? ''));
            $options = $q['options'] ?? [];
            $options = array_map('strval', (array) $options);
            if ($question !== '' || array_filter($options)) {
                return true;
            }
        }

        return false;
    }

    private function lessonPreviewFlag(array $lesson): int
    {
        $value = $lesson['is_preview'] ?? 0;

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'true', 'on', 'yes'], true) ? 1 : 0;
    }

    private function lessonIsEmpty(array $lesson): bool
    {
        $title = trim((string) ($lesson['title'] ?? ''));
        $duration = (int) ($lesson['duration'] ?? 0);
        $videoUrl = trim((string) ($lesson['video_url'] ?? ''));
        $fileExisting = trim((string) ($lesson['file_existing'] ?? ''));
        $hasQuiz = $this->lessonHasQuizQuestions($lesson);

        return $title === '' && $duration <= 0 && $videoUrl === '' && $fileExisting === '' && ! $hasQuiz;
    }

    public function criar()
    {
        $courseModel = new \App\Models\CourseModel();
        $moduleModel = new \App\Models\ModuleModel();
        $lessonModel = new \App\Models\LessonModel();
        $projectModel = new \App\Models\ProjectModel();

        // Receber formData (multipart/form-data)
        $data = $this->request->getPost();
        $draftId = (int) ($data['draft_id'] ?? 0);

        // Processar módulos
        $data['modules'] = [];
        $modulesRaw = $this->request->getPost('modules_json') ?? $this->request->getPost('modules');
        $modulesProvided = $modulesRaw !== null;
        if ($modulesProvided) {
            $data['modules'] = is_string($modulesRaw) ? json_decode($modulesRaw, true) : $modulesRaw;
        }

        // Processar tags (se houver)
        $data['tags'] = [];
        if ($this->request->getPost('tags')) {
            $tagsRaw = $this->request->getPost('tags');
            $data['tags'] = is_string($tagsRaw) ? json_decode($tagsRaw, true) : $tagsRaw;
        }

        // Processar projetos (se houver)
        $projects = [];
        $projectsProvided = $this->request->getPost('projects_present') !== null;
        $projectsRaw = $this->request->getPost('projects_json') ?? $this->request->getPost('projects');
        if ($projectsRaw !== null) {
            $projects = is_string($projectsRaw) ? json_decode($projectsRaw, true) : $projectsRaw;
        }

        // 1. Preparar dados do curso
        // Publica apenas quando o botao "Publicar Curso" for usado.
        $isPublish = ((string) $this->request->getPost('publish') === '1');
        $status = $isPublish ? 'Ativo' : 'Rascunho';

        $isPaid = ($data['courseType'] ?? 'free') === 'paid';
        $courseData = [
            'title_course' => $data['title_course'] ?? '',
            'subtitle_course' => $data['subtitle_course'] ?? '',
            'description_course' => $data['description_course'] ?? '',
            'learning_course' => $data['learning_course'] ?? '',
            'url_video_course' => $data['url_video_course'] ?? '',
            'id_instructor_course' => auth()->id(),
            'status_course' => $status,
            'price_course' => $isPaid ? ($data['price_course'] ?? 0) : 0,
            'color_course' => $data['color_course'] ?? '#3b82f6',
        ];
        $courseData = array_merge($courseData, $this->extractCommerceFields($data, $isPaid));
        $courseData = $this->normalizeCoursePayload($courseData);

        // Upload de imagem
        $file = $this->request->getFile('image_course');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/instructor/img/courses', $newName);
            $courseData['image_course'] = $newName;
        }

        $iconName = $this->moveCourseIcon($this->request->getFile('icon_course'));
        if ($iconName) {
            $courseData['icon_course'] = $iconName;
        }

        if ($draftId) {
            $course = $courseModel->find($draftId);
            if (!$course || $course->id_instructor_course != auth()->id()) {
                return redirect()->to('instructor/dashboard/meus_cursos')
                    ->with('error', 'Acesso inválido');
            }

            if (! $courseModel->validate($courseData)) {
                return redirect()->back()->withInput()
                    ->with('error', $this->modelErrorMessage($courseModel, 'Falha de validacao ao atualizar curso.'));
            }

            try {
                $updated = $courseModel->update($draftId, $courseData);
            } catch (\Throwable $e) {
                log_message('error', '[CourseController::criar] Falha ao atualizar curso #{id}: {msg}', [
                    'id' => $draftId,
                    'msg' => $e->getMessage(),
                ]);
                return redirect()->back()->withInput()
                    ->with('error', 'Erro ao atualizar curso. Verifique os dados e tente novamente.');
            }

            if (! $updated) {
                return redirect()->back()->withInput()
                    ->with('error', $this->modelErrorMessage($courseModel, 'Erro ao atualizar curso.'));
            }

            $courseId = $draftId;
        } else {
            // Validar e inserir curso
            if (! $courseModel->validate($courseData)) {
                return redirect()->back()->withInput()
                    ->with('error', $this->modelErrorMessage($courseModel, 'Falha de validacao ao criar curso.'));
            }

            try {
                $inserted = $courseModel->insert($courseData);
            } catch (\Throwable $e) {
                log_message('error', '[CourseController::criar] Falha ao inserir curso: {msg}', [
                    'msg' => $e->getMessage(),
                ]);
                return redirect()->back()->withInput()
                    ->with('error', 'Erro ao criar curso. Verifique os dados e tente novamente.');
            }

            if (! $inserted) {
                return redirect()->back()->withInput()
                    ->with('error', $this->modelErrorMessage($courseModel, 'Erro ao inserir curso.'));
            }

            $courseId = $courseModel->insertID();
        }

        // 2. Salvar módulos e aulas
        if ($modulesProvided && $draftId) {
                $oldModules = $moduleModel->where('id_course_module', $courseId)->findAll();
                foreach ($oldModules as $mod) {
                    $lessonModel->where('id_module_lesson', $mod->id_module)->delete();
                }
                $moduleModel->where('id_course_module', $courseId)->delete();
        }

        if (!empty($data['modules'])) {
            foreach ($data['modules'] as $mIndex => $module) {
                $zipIndex = $module['zip_input_index'] ?? $mIndex;
                $zipFile = $this->request->getFile('modules_zip.' . $zipIndex);
                $zipName = $this->moveModuleZip($zipFile);
                if (! $zipName && ! empty($module['zip_existing'])) {
                    $zipName = $module['zip_existing'];
                }

                $moduleInsert = [
                    'id_course_module' => $courseId,
                    'title_module' => $module['title'] ?? 'Módulo ' . ($mIndex + 1),
                    'description_module' => $module['description'] ?? '',
                    'content_zip_module' => $zipName,
                    'min_score_module' => (int) ($module['min_score'] ?? self::DEFAULT_MIN_SCORE),
                    'position_module' => $mIndex + 1,
                ];

                $moduleModel->insert($moduleInsert);
                $moduleId = $moduleModel->insertID();

                if (!empty($module['lessons'])) {
                    foreach ($module['lessons'] as $lIndex => $lesson) {
                        $fileIndex = $lesson['file_input_index'] ?? $lIndex;
                        $file = $this->request->getFile('lesson_files.' . $mIndex . '.' . $fileIndex);
                        $hasUpload = $file && $file->isValid() && ! $file->hasMoved();
                        if ($this->lessonIsEmpty($lesson) && ! $hasUpload) {
                            continue;
                        }

                        $quizQuestions = $lesson['quiz_questions'] ?? ($lesson['quiz'] ?? []);
                        $lessonContent = null;
                        if (($lesson['type'] ?? '') === 'quiz' && ! empty($quizQuestions)) {
                            $normalized = $this->normalizeQuizQuestions((array) $quizQuestions);
                            if (! empty($normalized)) {
                                $lessonContent = json_encode(['questions' => $normalized], JSON_UNESCAPED_UNICODE);
                            }
                        }

                        $fileIndex = $lesson['file_input_index'] ?? $lIndex;
                        $file = $this->request->getFile('lesson_files.' . $mIndex . '.' . $fileIndex);
                        $attachment = $this->moveLessonAttachment($file);
                        $attachmentPath = $attachment['path'] ?? ($lesson['file_existing'] ?? null);
                        $attachmentName = $attachment['name'] ?? ($lesson['file_existing_name'] ?? null);

                        $lessonInsert = [
                            'id_module_lesson' => $moduleId,
                            'title_lesson' => $lesson['title'] ?? 'Aula sem título',
                            'type_lesson' => $lesson['type'] ?? 'text',
                            'content_lesson' => $lessonContent,
                            'attachment_path_lesson' => $attachmentPath,
                            'attachment_name_lesson' => $attachmentName,
                            'duration_lesson' => $lesson['duration'] ?? 0,
                            'position_lesson' => $lIndex + 1,
                            'video_url_lesson' => $lesson['video_url'] ?? null,
                            'is_preview_lesson' => $this->lessonPreviewFlag($lesson),
                        ];
                        $lessonModel->insert($lessonInsert);
                    }
                }
            }
        }

        // 3. Salvar projetos (se enviados)
        if ($projectsProvided) {
            if ($draftId) {
                $projectModel->where('id_course_project', $courseId)->delete();
            }
            foreach ($projects as $pIndex => $project) {
                if (!is_array($project)) {
                    continue;
                }

                $title = trim((string) ($project['title'] ?? ''));
                $description = trim((string) ($project['description'] ?? ''));
                $file = $this->request->getFile('project_images.' . $pIndex);
                $imgName = $this->moveProjectImage($file);
                if (! $imgName && ! empty($project['img_existing'])) {
                    $imgName = $project['img_existing'];
                }

                if ($title === '' || $description === '') {
                    if ($title === '' && $description === '' && ! $imgName) {
                        continue;
                    }
                    continue;
                }

                $projectModel->insert([
                    'id_course_project' => $courseId,
                    'img_project' => $imgName,
                    'title_project' => $title,
                    'description_project' => $description,
                ]);
            }
        }

        if ($status === 'Ativo') {
            $this->clearAppCaches();
        }

        return redirect()->to('instructor/dashboard/meus_cursos')->with('success', 'Curso criado com sucesso!');
    }

    public function draftCreate()
    {
        $courseModel = new \App\Models\CourseModel();
        $projectModel = new \App\Models\ProjectModel();

        $data = $this->request->getPost();

        $isPaid = ($data['courseType'] ?? 'free') === 'paid';
        $courseData = [
            'id_instructor_course' => auth()->id(),
            'status_course' => 'Rascunho',
            'title_course' => $data['title_course'] ?? '',
            'subtitle_course' => $data['subtitle_course'] ?? '',
            'description_course' => $data['description_course'] ?? '',
            'learning_course' => $data['learning_course'] ?? '',
            'url_video_course' => $data['url_video_course'] ?? '',
            'price_course' => $isPaid ? ($data['price_course'] ?? 0) : 0,
            'color_course' => $data['color_course'] ?? '#3b82f6',
        ];
        $courseData = array_merge($courseData, $this->extractCommerceFields($data, $isPaid));
        $courseData = $this->normalizeCoursePayload($courseData);

        $file = $this->request->getFile('image_course');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/instructor/img/courses', $newName);
            $courseData['image_course'] = $newName;
        }

        $iconName = $this->moveCourseIcon($this->request->getFile('icon_course'));
        if ($iconName) {
            $courseData['icon_course'] = $iconName;
        }

        $courseModel->skipValidation(true);
        if (!$courseModel->insert($courseData)) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => false,
                'message' => 'Falha ao criar rascunho.'
            ]);
        }

        $courseId = $courseModel->insertID();

        $projects = [];
        $projectsProvided = $this->request->getPost('projects_present') !== null;
        $projectsRaw = $this->request->getPost('projects_json') ?? $this->request->getPost('projects');
        if ($projectsRaw !== null) {
            $projects = is_string($projectsRaw) ? json_decode($projectsRaw, true) : $projectsRaw;
        }

        if ($projectsProvided) {
            foreach ($projects as $pIndex => $project) {
                if (!is_array($project)) {
                    continue;
                }

                $title = trim((string) ($project['title'] ?? ''));
                $description = trim((string) ($project['description'] ?? ''));
                $file = $this->request->getFile('project_images.' . $pIndex);
                $imgName = $this->moveProjectImage($file);
                if (! $imgName && ! empty($project['img_existing'])) {
                    $imgName = $project['img_existing'];
                }

                if ($title === '' || $description === '') {
                    if ($title === '' && $description === '' && ! $imgName) {
                        continue;
                    }
                    continue;
                }

                $projectModel->insert([
                    'id_course_project' => $courseId,
                    'img_project' => $imgName,
                    'title_project' => $title,
                    'description_project' => $description,
                ]);
            }
        }

        return $this->response->setJSON([
            'ok' => true,
            'id_course' => $courseId
        ]);
    }

    public function draftSave($id)
    {
        $courseModel = new \App\Models\CourseModel();
        $moduleModel = new \App\Models\ModuleModel();
        $lessonModel = new \App\Models\LessonModel();
        $projectModel = new \App\Models\ProjectModel();

        $course = $courseModel->find($id);
        if (!$course || $course->id_instructor_course != auth()->id()) {
            return $this->response->setStatusCode(403)->setJSON([
                'ok' => false,
                'message' => 'Acesso negado.'
            ]);
        }

        $data = $this->request->getPost();

        // Autosave: preservar status se já estiver publicado
        $currentStatus = strtolower(trim((string) ($course->status_course ?? '')));
        $isPublished = in_array($currentStatus, ['ativo', 'published', 'publicado'], true);
        $status = $isPublished ? 'Ativo' : 'Rascunho';

        $isPaid = ($data['courseType'] ?? 'free') === 'paid';
        $updateData = [
            'title_course' => $data['title_course'] ?? $course->title_course,
            'subtitle_course' => $data['subtitle_course'] ?? $course->subtitle_course,
            'description_course' => $data['description_course'] ?? $course->description_course,
            'learning_course' => $data['learning_course'] ?? $course->learning_course,
            'url_video_course' => $data['url_video_course'] ?? $course->url_video_course,
            'status_course' => $status,
            'price_course' => $isPaid
                ? ($data['price_course'] ?? $course->price_course)
                : 0,
            'color_course' => $data['color_course'] ?? ($course->color_course ?? '#3b82f6'),
        ];
        $updateData = array_merge($updateData, $this->extractCommerceFields($data, $isPaid));
        $updateData = $this->normalizeCoursePayload($updateData);

        $file = $this->request->getFile('image_course');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/instructor/img/courses', $newName);
            $updateData['image_course'] = $newName;
        }

        $iconName = $this->moveCourseIcon($this->request->getFile('icon_course'));
        if ($iconName) {
            $updateData['icon_course'] = $iconName;
        }

        $courseModel->skipValidation(true);
        if (!$courseModel->update($id, $updateData)) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => false,
                'message' => 'Falha ao salvar rascunho.'
            ]);
        }

        // Módulos e aulas (se enviados)
        $modules = [];
        $raw = $this->request->getPost('modules_json') ?? $this->request->getPost('modules');
        $modulesProvided = $raw !== null;
        if ($modulesProvided) {
            $modules = is_string($raw) ? json_decode($raw, true) : $raw;
        }

        if ($modulesProvided) {
            // apagar e recriar
            $oldModules = $moduleModel->where('id_course_module', $id)->findAll();
            foreach ($oldModules as $mod) {
                $lessonModel->where('id_module_lesson', $mod->id_module)->delete();
            }
            $moduleModel->where('id_course_module', $id)->delete();

            if (!empty($modules)) {
                foreach ($modules as $mIndex => $module) {
                    $zipIndex = $module['zip_input_index'] ?? $mIndex;
                    $zipFile = $this->request->getFile('modules_zip.' . $zipIndex);
                    $zipName = $this->moveModuleZip($zipFile);
                    if (! $zipName && ! empty($module['zip_existing'])) {
                        $zipName = $module['zip_existing'];
                    }

                    $moduleModel->insert([
                        'id_course_module' => $id,
                        'title_module' => $module['title'] ?? 'Módulo ' . ($mIndex + 1),
                        'description_module' => $module['description'] ?? '',
                        'content_zip_module' => $zipName,
                        'min_score_module' => (int) ($module['min_score'] ?? self::DEFAULT_MIN_SCORE),
                        'position_module' => $mIndex + 1,
                    ]);

                    $moduleId = $moduleModel->insertID();

                    foreach (($module['lessons'] ?? []) as $lIndex => $lesson) {
                        $fileIndex = $lesson['file_input_index'] ?? $lIndex;
                        $file = $this->request->getFile('lesson_files.' . $mIndex . '.' . $fileIndex);
                        $hasUpload = $file && $file->isValid() && ! $file->hasMoved();
                        if ($this->lessonIsEmpty($lesson) && ! $hasUpload) {
                            continue;
                        }

                        $quizQuestions = $lesson['quiz_questions'] ?? ($lesson['quiz'] ?? []);
                        $lessonContent = null;
                        if (($lesson['type'] ?? '') === 'quiz' && ! empty($quizQuestions)) {
                            $normalized = $this->normalizeQuizQuestions((array) $quizQuestions);
                            if (! empty($normalized)) {
                                $lessonContent = json_encode(['questions' => $normalized], JSON_UNESCAPED_UNICODE);
                            }
                        }

                        $fileIndex = $lesson['file_input_index'] ?? $lIndex;
                        $file = $this->request->getFile('lesson_files.' . $mIndex . '.' . $fileIndex);
                        $attachment = $this->moveLessonAttachment($file);
                        $attachmentPath = $attachment['path'] ?? ($lesson['file_existing'] ?? null);
                        $attachmentName = $attachment['name'] ?? ($lesson['file_existing_name'] ?? null);

                        $lessonModel->insert([
                            'id_module_lesson' => $moduleId,
                            'title_lesson' => $lesson['title'] ?? 'Aula',
                            'type_lesson' => $lesson['type'] ?? 'text',
                            'content_lesson' => $lessonContent,
                            'attachment_path_lesson' => $attachmentPath,
                            'attachment_name_lesson' => $attachmentName,
                            'duration_lesson' => (int) ($lesson['duration'] ?? 0),
                            'video_url_lesson' => $lesson['video_url'] ?? null,
                            'is_preview_lesson' => $this->lessonPreviewFlag($lesson),
                            'position_lesson' => $lIndex + 1,
                        ]);
                    }
                }
            }
        }

        // Projetos (se enviados)
        $projects = [];
        $projectsProvided = $this->request->getPost('projects_present') !== null;
        $projectsRaw = $this->request->getPost('projects_json') ?? $this->request->getPost('projects');
        if ($projectsRaw !== null) {
            $projects = is_string($projectsRaw) ? json_decode($projectsRaw, true) : $projectsRaw;
        }

        if ($projectsProvided) {
            $projectModel->where('id_course_project', $id)->delete();
            foreach ($projects as $pIndex => $project) {
                if (!is_array($project)) {
                    continue;
                }

                $title = trim((string) ($project['title'] ?? ''));
                $description = trim((string) ($project['description'] ?? ''));
                $file = $this->request->getFile('project_images.' . $pIndex);
                $imgName = $this->moveProjectImage($file);
                if (! $imgName && ! empty($project['img_existing'])) {
                    $imgName = $project['img_existing'];
                }

                if ($title === '' || $description === '') {
                    if ($title === '' && $description === '' && ! $imgName) {
                        continue;
                    }
                    continue;
                }

                $projectModel->insert([
                    'id_course_project' => $id,
                    'img_project' => $imgName,
                    'title_project' => $title,
                    'description_project' => $description,
                ]);
            }
        }

        return $this->response->setJSON([
            'ok' => true,
            'id_course' => (int) $id
        ]);
    }

    public function editar($id)
    {
        $courseModel = new \App\Models\CourseModel();
        $moduleModel = new \App\Models\ModuleModel();
        $lessonModel = new \App\Models\LessonModel();
        $projectModel = new \App\Models\ProjectModel();
        $db = \Config\Database::connect();

        if (!$this->request->is('post')) {
            return redirect()->to('instructor/dashboard/meus_cursos');
        }

        $course = $courseModel->find($id);

        if (!$course || $course->id_instructor_course != auth()->id()) {
            return redirect()->to('instructor/dashboard/meus_cursos')
                ->with('error', 'Acesso inválido');
        }

        $data = $this->request->getPost();

        $modules = [];
        $raw = $this->request->getPost('modules_json') ?? $this->request->getPost('modules');
        if ($raw !== null) {
            $modules = is_string($raw) ? json_decode($raw, true) : $raw;
        }

        $db->transStart();

        $isDraft = $this->request->getPost('draft') ? true : false;

        $isPaid = ($data['courseType'] ?? 'free') === 'paid';
        $updateData = [
            'title_course' => $data['title_course'] ?? $course->title_course,
            'subtitle_course' => $data['subtitle_course'] ?? $course->subtitle_course,
            'description_course' => $data['description_course'] ?? $course->description_course,
            'learning_course' => $data['learning_course'] ?? $course->learning_course,
            'url_video_course' => $data['url_video_course'] ?? $course->url_video_course,
            'status_course' => $isDraft ? 'Rascunho' : 'Ativo',
            'price_course' => $isPaid
                ? ($data['price_course'] ?? 0)
                : 0,
            'color_course' => $data['color_course'] ?? ($course->color_course ?? '#3b82f6'),
        ];
        $updateData = array_merge($updateData, $this->extractCommerceFields($data, $isPaid));
        $updateData = $this->normalizeCoursePayload($updateData);

        $file = $this->request->getFile('image_course');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/instructor/img/courses', $newName);
            $updateData['image_course'] = $newName;
        }

        $iconName = $this->moveCourseIcon($this->request->getFile('icon_course'));
        if ($iconName) {
            $updateData['icon_course'] = $iconName;
        }

        try {
            $updated = $courseModel->update($id, $updateData);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[CourseController::editar] Falha ao atualizar curso #{id}: {msg}', [
                'id' => $id,
                'msg' => $e->getMessage(),
            ]);
            return redirect()->back()->withInput()
                ->with('error', 'Falha ao atualizar curso. Verifique os dados e as colunas da tabela courses.');
        }

        if (! $updated) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', $this->modelErrorMessage($courseModel, 'Falha ao atualizar curso.'));
        }

        // apagar e recriar
        $oldModules = $moduleModel->where('id_course_module', $id)->findAll();
        $oldModulesByPosition = [];
        foreach ($oldModules as $oldModule) {
            $oldModulesByPosition[(int) $oldModule->position_module] = $oldModule;
        }
        foreach ($oldModules as $mod) {
            $lessonModel->where('id_module_lesson', $mod->id_module)->delete();
        }
        $moduleModel->where('id_course_module', $id)->delete();

        foreach ($modules as $mIndex => $module) {
            $zipIndex = $module['zip_input_index'] ?? $mIndex;
            $zipFile = $this->request->getFile('modules_zip.' . $zipIndex);
            $zipName = $this->moveModuleZip($zipFile);
            if (! $zipName && ! empty($module['zip_existing'])) {
                $zipName = $module['zip_existing'];
            }
            $oldModule = $oldModulesByPosition[$mIndex + 1] ?? null;
            if (! $zipName && $oldModule) {
                $zipName = $oldModule->content_zip_module ?? null;
            }
            $minScore = (int) ($module['min_score'] ?? ($oldModule->min_score_module ?? self::DEFAULT_MIN_SCORE));

            $moduleInserted = $moduleModel->insert([
                'id_course_module' => $id,
                'title_module' => $module['title'] ?? 'Módulo ' . ($mIndex + 1),
                'description_module' => $module['description'] ?? '',
                'content_zip_module' => $zipName,
                'min_score_module' => $minScore,
                'position_module' => $mIndex + 1,
            ]);
            if (! $moduleInserted) {
                $db->transRollback();
                return redirect()->back()->withInput()
                    ->with('error', $this->modelErrorMessage($moduleModel, 'Falha ao salvar modulo do curso.'));
            }

            $moduleId = $moduleModel->insertID();

            foreach (($module['lessons'] ?? []) as $lIndex => $lesson) {
                $fileIndex = $lesson['file_input_index'] ?? $lIndex;
                $file = $this->request->getFile('lesson_files.' . $mIndex . '.' . $fileIndex);
                $hasUpload = $file && $file->isValid() && ! $file->hasMoved();
                if ($this->lessonIsEmpty($lesson) && ! $hasUpload) {
                    continue;
                }

                $quizQuestions = $lesson['quiz_questions'] ?? ($lesson['quiz'] ?? []);
                $lessonContent = null;
                if (($lesson['type'] ?? '') === 'quiz' && ! empty($quizQuestions)) {
                    $normalized = $this->normalizeQuizQuestions((array) $quizQuestions);
                    if (! empty($normalized)) {
                        $lessonContent = json_encode(['questions' => $normalized], JSON_UNESCAPED_UNICODE);
                    }
                }

                $fileIndex = $lesson['file_input_index'] ?? $lIndex;
                $file = $this->request->getFile('lesson_files.' . $mIndex . '.' . $fileIndex);
                $attachment = $this->moveLessonAttachment($file);
                $attachmentPath = $attachment['path'] ?? ($lesson['file_existing'] ?? null);
                $attachmentName = $attachment['name'] ?? ($lesson['file_existing_name'] ?? null);

                $lessonInserted = $lessonModel->insert([
                    'id_module_lesson' => $moduleId,
                    'title_lesson' => $lesson['title'] ?? 'Aula',
                    'type_lesson' => $lesson['type'] ?? 'text',
                    'content_lesson' => $lessonContent,
                    'attachment_path_lesson' => $attachmentPath,
                    'attachment_name_lesson' => $attachmentName,
                    'duration_lesson' => (int) ($lesson['duration'] ?? 0),
                    'video_url_lesson' => $lesson['video_url'] ?? null,
                    'is_preview_lesson' => $this->lessonPreviewFlag($lesson),
                    'position_lesson' => $lIndex + 1,
                ]);
                if (! $lessonInserted) {
                    $db->transRollback();
                    return redirect()->back()->withInput()
                        ->with('error', $this->modelErrorMessage($lessonModel, 'Falha ao salvar aula do curso.'));
                }
            }
        }

        // Projetos (se enviados)
        $projects = [];
        $projectsProvided = $this->request->getPost('projects_present') !== null;
        $projectsRaw = $this->request->getPost('projects_json') ?? $this->request->getPost('projects');
        if ($projectsRaw !== null) {
            $projects = is_string($projectsRaw) ? json_decode($projectsRaw, true) : $projectsRaw;
        }

        if ($projectsProvided) {
            $projectModel->where('id_course_project', $id)->delete();
            foreach ($projects as $pIndex => $project) {
                if (!is_array($project)) {
                    continue;
                }

                $title = trim((string) ($project['title'] ?? ''));
                $description = trim((string) ($project['description'] ?? ''));
                $file = $this->request->getFile('project_images.' . $pIndex);
                $imgName = $this->moveProjectImage($file);
                if (! $imgName && ! empty($project['img_existing'])) {
                    $imgName = $project['img_existing'];
                }

                if ($title === '' || $description === '') {
                    if ($title === '' && $description === '' && ! $imgName) {
                        continue;
                    }
                    continue;
                }

                $projectInserted = $projectModel->insert([
                    'id_course_project' => $id,
                    'img_project' => $imgName,
                    'title_project' => $title,
                    'description_project' => $description,
                ]);
                if (! $projectInserted) {
                    $db->transRollback();
                    return redirect()->back()->withInput()
                        ->with('error', $this->modelErrorMessage($projectModel, 'Falha ao salvar projeto do curso.'));
                }
            }
        }

        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->withInput()
                ->with('error', 'Falha ao persistir alteracoes do curso. Nenhuma alteracao foi salva.');
        }

        if (! $isDraft) {
            $this->clearAppCaches();
        }

        return redirect()->to('instructor/dashboard/meus_cursos')
            ->with('success', 'Curso atualizado com sucesso!');
    }

    public function deletar($id = null)
    {
        $courseModel = new \App\Models\CourseModel();
        $moduleModel = new \App\Models\ModuleModel();
        $lessonModel = new \App\Models\LessonModel();

        if (!$id) {
            return redirect()->back()->with('error', 'ID do curso não fornecido');
        }

        $course = $courseModel->find($id);
        if (!$course) {
            return redirect()->back()->with('error', 'Curso não encontrado');
        }

        if ($course->id_instructor_course != auth()->id()) {
            return redirect()->back()->with('error', 'Acesso negado');
        }

        // Deletar aulas
        $modules = $moduleModel->where('id_course_module', $id)->findAll();
        if (!empty($modules)) {
            return redirect()->back()->with('error', 'Antes de eliminar o curso, remova todas as aulas e módulos.');
        }
        foreach ($modules as $mod) {
            $lessonModel->where('id_module_lesson', $mod->id_module)->delete();
        }

        // Deletar módulos
        $moduleModel->where('id_course_module', $id)->delete();

        // Deletar curso
        $courseModel->delete($id);

        return redirect()->to('/instructor/dashboard/meus_cursos')
            ->with('swal', [
                'icon' => 'success',
                'title' => 'Curso eliminado!',
                'text' => 'O curso foi removido com sucesso.'
            ]);
    }
}
