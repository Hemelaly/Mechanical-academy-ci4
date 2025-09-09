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
        $data['modules'] = isset($data['modules']) ? json_decode($data['modules'], true) : [];
        $data['tags'] = isset($data['tags']) ? json_decode($data['tags'], true) : [];

        // 1. Salvar curso
        $courseData = [
            'title_course'        => $data['title_course'] ?? '',
            'subtitle_course'     => $data['subtitle_course'] ?? '',
            'description_course'  => $data['description_course'] ?? '',
            'id_instructor_course' => auth()->id(),
            'status_course'       => 'Rascunho',
            'price_course'        => ($data['courseType'] ?? 'free') === 'paid' ? ($data['price_course'] ?? 0) : 0,
        ];

        // Imagem
        if ($file = $this->request->getFile('image_course')) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'assets/instructor/img/courses', $newName);
                $courseData['image_course'] = $newName;
            }
        }

        $success = $courseModel->insert($courseData);
        if (!$success) {
            return $this->response->setJSON(['errors' => $courseModel->errors()])->setStatusCode(400);
        }

        $courseId = $courseModel->insertID();

        // 2. Salvar módulos e aulas
        if (!empty($data['modules'])) {
            foreach ($data['modules'] as $mIndex => $module) {
                $moduleInsert = [
                    'id_course_module'   => $courseId,
                    'title_module'       => $module['title'] ?? 'Módulo ' . ($mIndex + 1),
                    'description_module' => $module['description'] ?? '',
                    'position_module'    => $mIndex + 1,
                ];
                $moduleModel->insert($moduleInsert);
                $moduleId = $moduleModel->insertID();

                if (!empty($module['lessons'])) {
                    foreach ($module['lessons'] as $lIndex => $lesson) {
                        $lessonInsert = [
                            'id_module_lesson' => $moduleId,
                            'title_lesson'     => $lesson['title'] ?? 'Aula sem título',
                            'type_lesson'      => $lesson['type'] ?? 'text',
                            'duration_lesson'  => $lesson['duration'] ?? 0,
                            'position_lesson'  => $lIndex + 1,
                            'video_url_lesson' => $lesson['video_url'] ?? null,
                        ];
                        $lessonModel->insert($lessonInsert);
                    }
                }
            }
        }

        return $this->response->setJSON(['success' => true, 'course_id' => $courseId]);
    }

    public function editar($id = null)
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

        // Receber dados do form
        $data = $this->request->getPost();

        // Atualizar curso
        $courseData = [
            'title_course'       => $data['title_course'] ?? $course->title_course,
            'subtitle_course'    => $data['subtitle_course'] ?? $course->subtitle_course,
            'description_course' => $data['description_course'] ?? $course->description_course,
            'price_course'       => ($data['courseType'] ?? 'free') === 'paid' ? ($data['price_course'] ?? 0) : 0,
            'status_course'      => $data['status_course'] ?? $course->status_course,
        ];

        if ($file = $this->request->getFile('image_course')) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'assets/instructor/img/courses', $newName);
                $courseData['image_course'] = $newName;
            }
        }

        $courseModel->update($id, $courseData);

        // Atualizar módulos e aulas
        if (!empty($data['modules'])) {
            // Limpar módulos antigos
            $oldModules = $moduleModel->where('id_course_module', $id)->findAll();
            foreach ($oldModules as $mod) {
                $lessonModel->where('id_module_lesson', $mod->id_module)->delete();
            }
            $moduleModel->where('id_course_module', $id)->delete();

            // Inserir novos módulos e aulas
            foreach ($data['modules'] as $mIndex => $module) {
                $moduleInsert = [
                    'id_course_module'   => $id,
                    'title_module'       => $module['title'] ?? 'Módulo ' . ($mIndex + 1),
                    'description_module' => $module['description'] ?? '',
                    'position_module'    => $mIndex + 1,
                ];
                $moduleModel->insert($moduleInsert);
                $moduleId = $moduleModel->insertID();

                if (!empty($module['lessons'])) {
                    foreach ($module['lessons'] as $lIndex => $lesson) {
                        $lessonInsert = [
                            'id_module_lesson' => $moduleId,
                            'title_lesson'     => $lesson['title'] ?? 'Aula sem título',
                            'type_lesson'      => $lesson['type'] ?? 'text',
                            'duration_lesson'  => $lesson['duration'] ?? 0,
                            'position_lesson'  => $lIndex + 1,
                            'video_url_lesson' => $lesson['video_url'] ?? null,
                        ];
                        $lessonModel->insert($lessonInsert);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Curso atualizado com sucesso!');
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

        return redirect()->back()->with('success', 'Curso deletado com sucesso');
    }
}
