<?php

namespace App\Controllers\Instructor;

use App\Controllers\BaseController;

class CourseController extends BaseController
{
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

        $targetDir = FCPATH . 'assets/instructor/lesson_files';
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        if (! $file->move($targetDir, $newName)) {
            return null;
        }

        return [
            'path' => $newName,
            'name' => $file->getClientName(),
        ];
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

        // 1. Preparar dados do curso
        $status = $data['status_course'] ?? 'Rascunho';
        if ($status === 'Publicado') {
            $status = 'Ativo';
        }
        if (!in_array($status, ['Ativo', 'Rascunho', 'Arquivado'], true)) {
            $status = 'Rascunho';
        }

        $courseData = [
            'title_course' => $data['title_course'] ?? '',
            'subtitle_course' => $data['subtitle_course'] ?? '',
            'description_course' => $data['description_course'] ?? '',
            'id_instructor_course' => auth()->id(),
            'status_course' => $status,
            'price_course' => ($data['courseType'] ?? 'free') === 'paid' ? ($data['price_course'] ?? 0) : 0,
        ];

        // Upload de imagem
        $file = $this->request->getFile('image_course');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/instructor/img/courses', $newName);
            $courseData['image_course'] = $newName;
        }

        if ($draftId) {
            $course = $courseModel->find($draftId);
            if (!$course || $course->id_instructor_course != auth()->id()) {
                return redirect()->to('instructor/dashboard/meus_cursos')
                    ->with('error', 'Acesso inválido');
            }

            if (!$courseModel->validate($courseData)) {
                dd('Erros de validação:', $courseModel->errors());
            }

            if (!$courseModel->update($draftId, $courseData)) {
                dd('Erro ao atualizar curso:', $courseModel->errors(), $courseModel->db->error());
            }

            $courseId = $draftId;
        } else {
            // Validar e inserir curso
            if (!$courseModel->validate($courseData)) {
                dd('Erros de validação:', $courseModel->errors());
            }

            if (!$courseModel->insert($courseData)) {
                dd('Erro ao inserir curso:', $courseModel->errors(), $courseModel->db->error());
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
                    'min_score_module' => (int) ($module['min_score'] ?? 75),
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
                            $normalized = [];
                            foreach ($quizQuestions as $q) {
                                $question = trim((string) ($q['question'] ?? ''));
                                $options = $q['options'] ?? [];
                                $options = array_slice(array_pad(array_map('strval', (array) $options), 4, ''), 0, 4);
                                $correct = (int) ($q['correct'] ?? 0);
                                if ($correct < 0 || $correct > 3) {
                                    $correct = 0;
                                }
                                if ($question || array_filter($options)) {
                                    $normalized[] = [
                                        'question' => $question,
                                        'options' => $options,
                                        'correct' => $correct,
                                    ];
                                }
                            }
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
                        ];
                        $lessonModel->insert($lessonInsert);
                    }
                }
            }
        }

        return redirect()->to('instructor/dashboard/meus_cursos')->with('success', 'Curso criado com sucesso!');
    }

    public function draftCreate()
    {
        $courseModel = new \App\Models\CourseModel();

        $data = $this->request->getPost();

        $courseData = [
            'id_instructor_course' => auth()->id(),
            'status_course' => 'Rascunho',
            'title_course' => $data['title_course'] ?? '',
            'subtitle_course' => $data['subtitle_course'] ?? '',
            'description_course' => $data['description_course'] ?? '',
            'price_course' => ($data['courseType'] ?? 'free') === 'paid' ? ($data['price_course'] ?? 0) : 0,
        ];

        $file = $this->request->getFile('image_course');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/instructor/img/courses', $newName);
            $courseData['image_course'] = $newName;
        }

        $courseModel->skipValidation(true);
        if (!$courseModel->insert($courseData)) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok' => false,
                'message' => 'Falha ao criar rascunho.'
            ]);
        }

        return $this->response->setJSON([
            'ok' => true,
            'id_course' => $courseModel->insertID()
        ]);
    }

    public function draftSave($id)
    {
        $courseModel = new \App\Models\CourseModel();
        $moduleModel = new \App\Models\ModuleModel();
        $lessonModel = new \App\Models\LessonModel();

        $course = $courseModel->find($id);
        if (!$course || $course->id_instructor_course != auth()->id()) {
            return $this->response->setStatusCode(403)->setJSON([
                'ok' => false,
                'message' => 'Acesso negado.'
            ]);
        }

        $data = $this->request->getPost();

        $status = $data['status_course'] ?? $course->status_course;
        if ($status === 'Publicado') {
            $status = 'Ativo';
        }
        if (!in_array($status, ['Ativo', 'Rascunho', 'Arquivado'], true)) {
            $status = $course->status_course;
        }

        $updateData = [
            'title_course' => $data['title_course'] ?? $course->title_course,
            'subtitle_course' => $data['subtitle_course'] ?? $course->subtitle_course,
            'description_course' => $data['description_course'] ?? $course->description_course,
            'status_course' => $status,
            'price_course' => ($data['courseType'] ?? 'free') === 'paid'
                ? ($data['price_course'] ?? $course->price_course)
                : 0,
        ];

        $file = $this->request->getFile('image_course');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/instructor/img/courses', $newName);
            $updateData['image_course'] = $newName;
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
                        'min_score_module' => (int) ($module['min_score'] ?? 75),
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
                            $normalized = [];
                            foreach ($quizQuestions as $q) {
                                $question = trim((string) ($q['question'] ?? ''));
                                $options = $q['options'] ?? [];
                                $options = array_slice(array_pad(array_map('strval', (array) $options), 4, ''), 0, 4);
                                $correct = (int) ($q['correct'] ?? 0);
                                if ($correct < 0 || $correct > 3) {
                                    $correct = 0;
                                }
                                if ($question || array_filter($options)) {
                                    $normalized[] = [
                                        'question' => $question,
                                        'options' => $options,
                                        'correct' => $correct,
                                    ];
                                }
                            }
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
                            'position_lesson' => $lIndex + 1,
                        ]);
                    }
                }
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

        $courseModel->update($id, [
            'title_course' => $data['title_course'] ?? $course->title_course,
            'subtitle_course' => $data['subtitle_course'] ?? $course->subtitle_course,
            'description_course' => $data['description_course'] ?? $course->description_course,
            'status_course' => 'Ativo',
            'price_course' => ($data['courseType'] ?? 'free') === 'paid'
                ? ($data['price_course'] ?? 0)
                : 0,
        ]);

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
            $minScore = (int) ($module['min_score'] ?? ($oldModule->min_score_module ?? 75));

            $moduleModel->insert([
                'id_course_module' => $id,
                'title_module' => $module['title'] ?? 'Módulo ' . ($mIndex + 1),
                'content_zip_module' => $zipName,
                'min_score_module' => $minScore,
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
                    $normalized = [];
                    foreach ($quizQuestions as $q) {
                        $question = trim((string) ($q['question'] ?? ''));
                        $options = $q['options'] ?? [];
                        $options = array_slice(array_pad(array_map('strval', (array) $options), 4, ''), 0, 4);
                        $correct = (int) ($q['correct'] ?? 0);
                        if ($correct < 0 || $correct > 3) {
                            $correct = 0;
                        }
                        if ($question || array_filter($options)) {
                            $normalized[] = [
                                'question' => $question,
                                'options' => $options,
                                'correct' => $correct,
                            ];
                        }
                    }
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
                    'position_lesson' => $lIndex + 1,
                ]);
            }
        }

        $db->transComplete();

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
