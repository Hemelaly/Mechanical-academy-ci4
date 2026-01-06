<?php

namespace App\Controllers\Instructor;

use App\Controllers\BaseController;

class CourseController extends BaseController
{
    public function criar()
    {
        $courseModel = new \App\Models\CourseModel();
        $moduleModel = new \App\Models\ModuleModel();
        $lessonModel = new \App\Models\LessonModel();

        // Receber formData (multipart/form-data)
        $data = $this->request->getPost();

        // Processar módulos
        $data['modules'] = [];
        if ($this->request->getPost('modules')) {
            $modulesRaw = $this->request->getPost('modules');
            $data['modules'] = is_string($modulesRaw) ? json_decode($modulesRaw, true) : $modulesRaw;
        }

        // Processar tags (se houver)
        $data['tags'] = [];
        if ($this->request->getPost('tags')) {
            $tagsRaw = $this->request->getPost('tags');
            $data['tags'] = is_string($tagsRaw) ? json_decode($tagsRaw, true) : $tagsRaw;
        }

        // 1. Preparar dados do curso
        $courseData = [
            'title_course' => $data['title_course'] ?? '',
            'subtitle_course' => $data['subtitle_course'] ?? '',
            'description_course' => $data['description_course'] ?? '',
            'id_instructor_course' => auth()->id(),
            'status_course' => 'Rascunho',
            'price_course' => ($data['courseType'] ?? 'free') === 'paid' ? ($data['price_course'] ?? 0) : 0,
        ];

        // Upload de imagem
        $file = $this->request->getFile('image_course');

        if ($file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'assets/instructor/img/courses', $newName);
                $courseData['image_course'] = $newName;
            }
        }

        // Validar e inserir curso
        if (!$courseModel->validate($courseData)) {
            dd('Erros de validação:', $courseModel->errors());
        }

        if (!$courseModel->insert($courseData)) {
            dd('Erro ao inserir curso:', $courseModel->errors(), $courseModel->db->error());
        }

        $courseId = $courseModel->insertID();

        // 2. Salvar módulos e aulas
        if (!empty($data['modules'])) {
            foreach ($data['modules'] as $mIndex => $module) {
                $moduleInsert = [
                    'id_course_module' => $courseId,
                    'title_module' => $module['title'] ?? 'Módulo ' . ($mIndex + 1),
                    'description_module' => $module['description'] ?? '',
                    'position_module' => $mIndex + 1,
                ];

                $moduleModel->insert($moduleInsert);
                $moduleId = $moduleModel->insertID();

                if (!empty($module['lessons'])) {
                    foreach ($module['lessons'] as $lIndex => $lesson) {
                        $lessonInsert = [
                            'id_module_lesson' => $moduleId,
                            'title_lesson' => $lesson['title'] ?? 'Aula sem título',
                            'type_lesson' => $lesson['type'] ?? 'text',
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
        if ($this->request->getPost('modules')) {
            $raw = $this->request->getPost('modules');
            $modules = is_string($raw) ? json_decode($raw, true) : $raw;
        }

        $db->transStart();

        $courseModel->update($id, [
            'title_course'       => $data['title_course'] ?? $course->title_course,
            'subtitle_course'    => $data['subtitle_course'] ?? $course->subtitle_course,
            'description_course' => $data['description_course'] ?? $course->description_course,
            'status_course' => 'Ativo',
            'price_course'       => ($data['courseType'] ?? 'free') === 'paid'
                ? ($data['price_course'] ?? 0)
                : 0,
        ]);


        // apagar e recriar
        $oldModules = $moduleModel->where('id_course_module', $id)->findAll();
        foreach ($oldModules as $mod) {
            $lessonModel->where('id_module_lesson', $mod->id_module)->delete();
        }
        $moduleModel->where('id_course_module', $id)->delete();

        foreach ($modules as $mIndex => $module) {
            $moduleModel->insert([
                'id_course_module' => $id,
                'title_module'     => $module['title'] ?? 'Módulo ' . ($mIndex + 1),
                'position_module'  => $mIndex + 1,
            ]);

            $moduleId = $moduleModel->insertID();

            foreach (($module['lessons'] ?? []) as $lIndex => $lesson) {
                $lessonModel->insert([
                    'id_module_lesson' => $moduleId,
                    'title_lesson'     => $lesson['title'] ?? 'Aula',
                    'type_lesson'      => $lesson['type'] ?? 'text',
                    'duration_lesson'  => (int) ($lesson['duration'] ?? 0),
                    'video_url_lesson' => $lesson['video_url'] ?? null,
                    'position_lesson'  => $lIndex + 1,
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
