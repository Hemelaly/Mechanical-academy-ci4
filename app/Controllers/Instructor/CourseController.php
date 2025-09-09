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

        // Receber JSON do fetch
        $data = json_decode($this->request->getBody(), true);

        // 1. Salvar curso
        $courseData = [
            'title_course'        => $data['title_course'] ?? '',
            'subtitle_course'     => $data['subtitle_course'] ?? '',
            'description_course'  => $data['description_course'] ?? '',
            'id_instructor_course' => auth()->id(),
            'status_course'       => 'Rascunho',
            'price_course'        => ($data['courseType'] ?? 'free') === 'paid' ? ($data['price_course'] ?? 0) : 0,
        ];

        if ($file = $this->request->getFile('image_course')) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'assets/instructor/img/courses', $newName);
                $courseData['image_course'] = $newName;
            }
        }

        $success = $courseModel->insert($courseData);
        if (!$success) return $this->response->setJSON(['errors' => $courseModel->errors()])->setStatusCode(400);

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
}
