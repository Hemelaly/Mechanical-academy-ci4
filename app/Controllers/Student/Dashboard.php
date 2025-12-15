<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\ExtendedUserModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;
use CodeIgniter\Shield\Models\UserModel;

class Dashboard extends BaseController
{
    private function sidebarLinks()
    {
        return [
            [
                'label' => 'Início',
                'icon' => 'bi-house-door',
                'url' => '/student/dashboard',
                'pattern' => '/student/dashboard' // Apenas correspondência exata
            ],
            [
                'label' => 'Meus Cursos',
                'icon' => 'bi-book',
                'url' => '/student/dashboard/inscricoes',
                'pattern' => '/student/dashboard/inscricoes*' // Com * para subpáginas
            ],
            [
                'label' => 'Todos Cursos',
                'icon' => 'bi-book',
                'url' => '/student/dashboard/cursos',
                'pattern' => '/student/dashboard/cursos*' // Com * para subpáginas
            ],
            [
                'label' => 'Perfil',
                'icon' => 'bi-person-circle',
                'url' => '/student/dashboard/perfil',
                'pattern' => '/student/dashboard/perfil*' // Com * para subpáginas
            ],
        ];
    }

    public function index()
    {
        $auth = service('auth');
        $user = $auth->user();

        $coursesModel     = new CourseModel();
        $enrollmentModel  = new \App\Models\EnrollmentModel();
        $lessonModel      = new \App\Models\LessonModel();
        $db               = db_connect();

        // Todos os cursos (se ainda usa na home)
        $courses = $coursesModel->findAll();

        // Inscrições do usuário
        $enrollments = $enrollmentModel
            ->where('id_student_enrollment', $user->id)
            ->findAll();

        // Mapas úteis
        $activeCourseIds      = [];
        $pendingCourseIds     = [];
        $enrollmentByCourseId = []; // [id_course => enrollment row]

        foreach ($enrollments as $enr) {
            $courseId = (int) $enr->id_course_enrollment;
            $enrollmentByCourseId[$courseId] = $enr;

            if ($enr->status_enrollment === 'Ativa')    $activeCourseIds[]  = $courseId;
            if ($enr->status_enrollment === 'Pendente') $pendingCourseIds[] = $courseId;
        }

        // === OBJETO DE PROGRESSO POR CURSO ===
        // Monta um objeto com a estrutura:
        // $progressByCourse->{id_course} = (object)[
        //   courseId, enrollmentId, status, progress, updatedAt
        // ]
        $progressByCourseArr = [];
        foreach ($enrollmentByCourseId as $courseId => $enr) {
            $progressByCourseArr[$courseId] = (object) [
                'courseId'     => (int) $courseId,
                'enrollmentId' => (int) $enr->id_enrollment,
                'status'       => (string) $enr->status_enrollment,
                'progress'     => (int) ($enr->progress_enrollment ?? 0),
                'updatedAt'    => $enr->updated_at ?? null,
            ];
        }
        // Converte o array associativo para stdClass (objeto) como você pediu
        $progressByCourse = (object) $progressByCourseArr;

        // Cursos nos quais o aluno está inscrito (tua função custom)
        $lessons = $enrollmentModel->getStudentEnrolledCourses($user->id);

        // Helper para calcular a aula de retomada (resume) por curso/enrollment
        $calcResume = function (int $courseId, int $enrollmentId) use ($db) {
            $ordered = $db->table('lessons l')
                ->select('l.id_lesson')
                ->join('modules m', 'm.id_module = l.id_module_lesson')
                ->where('m.id_course_module', $courseId)
                ->orderBy('m.position_module', 'ASC')
                ->orderBy('l.position_lesson', 'ASC')
                ->get()->getResultArray();
            $orderedIds = array_map(fn($r) => (int)$r['id_lesson'], $ordered);

            if (empty($orderedIds)) return null;

            $completedLessonIds = array_column(
                $db->table('progress')
                    ->select('id_lesson_progress')
                    ->where('id_enrollment_progress', $enrollmentId)
                    ->where('completed_at_progress IS NOT NULL', null, false)
                    ->get()->getResultArray(),
                'id_lesson_progress'
            );
            $completedSet = array_flip($completedLessonIds);

            foreach ($orderedIds as $lid) {
                if (!isset($completedSet[$lid])) return $lid; // primeira não concluída
            }
            return end($orderedIds); // todas concluídas -> última
        };

        // Para cada curso inscrito: calcular resume e anexar progresso
        foreach ($lessons as &$courseRow) {
            $courseId     = (int) $courseRow->id_course;
            $enrollment   = $enrollmentByCourseId[$courseId] ?? null;
            $enrollmentId = $enrollment->id_enrollment ?? 0;

            // resume
            if ($enrollmentId) {
                $courseRow->resumeLessonId = $calcResume($courseId, (int)$enrollmentId);
            } else {
                $courseRow->resumeLessonId = null;
            }

            // progresso do enrollment (direto na linha do curso)
            $courseRow->progress = isset($progressByCourseArr[$courseId])
                ? $progressByCourseArr[$courseId]->progress
                : 0.0;
        }
        unset($courseRow);

        return view('pages/student/home', [
            'user'              => $user,
            'courses'           => $courses,
            'lesson'           => $lessons,          // lista de cursos inscritos (cada item com ->resumeLessonId e ->progress)
            'progress'  => $progressByCourse, // objeto com progresso por id de curso
            'activeCourseIds'   => $activeCourseIds,
            'pendingCourseIds'  => $pendingCourseIds,
            'sidebarLinks'      => $this->sidebarLinks(),
            'currentUrl'        => current_url(),
        ]);
    }

    public function my_courses()
    {
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $lessonModel     = new \App\Models\LessonModel();
        $db              = db_connect();

        $user = service('auth')->user();

        // Cursos nos quais o aluno está inscrito (função custom)
        $courses = $enrollmentModel->getStudentEnrolledCourses($user->id);

        // Todas as matrículas do aluno
        $enrollments = $enrollmentModel
            ->where('id_student_enrollment', $user->id)
            ->findAll();

        // Mapas: curso -> (id_enrollment, progress, etc.)
        $enrollmentByCourse = [];   // [id_course => id_enrollment]
        $progressByCourseArr = [];  // [id_course => (object)progress info]

        foreach ($enrollments as $enr) {
            $courseId = (int) $enr->id_course_enrollment;
            $enrollmentByCourse[$courseId] = (int) $enr->id_enrollment;

            $progressByCourseArr[$courseId] = (object) [
                'courseId'     => $courseId,
                'enrollmentId' => (int) $enr->id_enrollment,
                'status'       => (string) $enr->status_enrollment,
                // se sua coluna já é % inteiro (0–100), mantenha (int). Se for decimal (0–100.x), use (float)
                'progress'     => (int) ($enr->progress_enrollment ?? 0),
                'updatedAt'    => $enr->updated_at ?? null,
            ];
        }
        $progressByCourse = (object) $progressByCourseArr;

        // Helper: retorna a aula de retomada (primeira não concluída; se todas, a última)
        $calcResume = function (int $courseId, int $enrollmentId) use ($db) {
            $ordered = $db->table('lessons l')
                ->select('l.id_lesson')
                ->join('modules m', 'm.id_module = l.id_module_lesson')
                ->where('m.id_course_module', $courseId)
                ->orderBy('m.position_module', 'ASC')
                ->orderBy('l.position_lesson', 'ASC')
                ->get()->getResultArray();

            $orderedIds = array_map(fn($r) => (int)$r['id_lesson'], $ordered);
            if (empty($orderedIds)) return null;

            $completedLessonIds = array_column(
                $db->table('progress')
                    ->select('id_lesson_progress')
                    ->where('id_enrollment_progress', $enrollmentId)
                    ->where('completed_at_progress IS NOT NULL', null, false)
                    ->get()->getResultArray(),
                'id_lesson_progress'
            );
            $completedSet = array_flip($completedLessonIds);

            foreach ($orderedIds as $lid) {
                if (!isset($completedSet[$lid])) return $lid; // primeira não concluída
            }
            return end($orderedIds); // todas concluídas -> última
        };

        // Para cada curso, definir resumeLessonId e anexar progresso
        foreach ($courses as &$course) {
            $courseId     = (int) $course->id_course;
            $enrollmentId = $enrollmentByCourse[$courseId] ?? 0;

            $course->resumeLessonId = $enrollmentId ? $calcResume($courseId, $enrollmentId) : null;

            // progresso direto no objeto do curso
            $course->progress = isset($progressByCourseArr[$courseId])
                ? $progressByCourseArr[$courseId]->progress
                : 0;

            // se quiser, também pode anexar enrollmentId/status
            $course->enrollmentId = isset($progressByCourseArr[$courseId]) ? $progressByCourseArr[$courseId]->enrollmentId : null;
            $course->enrollmentStatus = isset($progressByCourseArr[$courseId]) ? $progressByCourseArr[$courseId]->status : null;
        }
        unset($course);

        return view('pages/student/my_courses', [
            'user'             => $user,
            'courses'          => $courses,          // cada item tem ->resumeLessonId e ->progress
            'progress' => $progressByCourse, // objeto opcional para lookup por id_course
            'sidebarLinks'     => $this->sidebarLinks(),
            'currentUrl'       => current_url()
        ]);
    }

    public function lessons($id)
    {
        $lessonModel     = new LessonModel();
        $moduleModel     = new ModuleModel();
        $courseModel     = new CourseModel();
        $enrollmentModel = new EnrollmentModel();
        $db              = db_connect();

        // Usuário atual
        $authUser = service('auth')->user();
        if (! $authUser) {
            return redirect()->to(site_url('login'))
                ->with('error', 'Sessão expirada. Faça login novamente.');
        }
        $userId = function_exists('user_id') ? user_id() : ($authUser->id ?? $authUser->getId());

        $id = (int) $id;

        // Helper para calcular retomada e ordem global (por curso)
        $calcResume = function (int $courseId, int $enrollmentId) use ($db) {
            $ordered = $db->table('lessons l')
                ->select('l.id_lesson')
                ->join('modules m', 'm.id_module = l.id_module_lesson')
                ->where('m.id_course_module', $courseId)
                ->orderBy('m.position_module', 'ASC')
                ->orderBy('l.position_lesson', 'ASC')
                ->get()->getResultArray();

            $orderedIds = array_map(fn($r) => (int) $r['id_lesson'], $ordered);
            if (empty($orderedIds)) {
                return [null, $orderedIds];
            }

            // Concluídas POR ESTA MATRÍCULA
            $completedLessonIds = array_column(
                $db->table('progress')
                    ->select('id_lesson_progress')
                    ->where('id_enrollment_progress', $enrollmentId)
                    ->where('completed_at_progress IS NOT NULL', null, false)
                    ->get()->getResultArray(),
                'id_lesson_progress'
            );
            $completedSet = array_flip($completedLessonIds);

            $resumeId = null;
            foreach ($orderedIds as $lid) {
                if (! isset($completedSet[$lid])) {
                    $resumeId = $lid;
                    break;
                }
            }
            if ($resumeId === null) {
                // tudo concluído → volta à última
                $resumeId = end($orderedIds);
            }

            return [$resumeId, $orderedIds];
        };

        // Tenta achar como AULA
        $lesson = $lessonModel->find($id);

        // ===========================
        // NÃO É AULA → tratar como CURSO
        // ===========================
        if (! $lesson) {
            $course = $courseModel->find($id);
            if (! $course) {
                return redirect()->to('/student/dashboard/meus_cursos')
                    ->with('error', 'Curso não encontrado.');
            }

            // MATRÍCULA do usuário logado nesse curso
            $enrollment = $enrollmentModel
                ->where('id_course_enrollment',  (int) $course->id_course)
                ->where('id_student_enrollment', (int) $userId)   // <<< FILTRO PELO ESTUDANTE
                ->first();

            if (! $enrollment) {
                return redirect()->to('/student/dashboard/checkout/' . (int) $course->id_course)
                    ->with('warning', 'Você precisa estar inscrito neste curso.');
            }

            [$resumeId] = $calcResume((int) $course->id_course, (int) $enrollment->id_enrollment);
            if ($resumeId) {
                return redirect()->to('/student/dashboard/ver_aulas/' . $resumeId)
                    ->with('info', 'Retomando de onde parou.');
            }

            return redirect()->to('/student/dashboard/meus_cursos')
                ->with('error', 'Nenhuma aula encontrada para este curso.');
        }

        // ===========================
        // É AULA → obter módulo/curso
        // ===========================
        $module = $moduleModel->find($lesson->id_module_lesson);
        if (! $module) {
            return redirect()->to('/student/dashboard/meus_cursos')
                ->with('error', 'Módulo da aula não encontrado.');
        }

        $course = $courseModel->find($module->id_course_module);
        if (! $course) {
            return redirect()->to('/student/dashboard/meus_cursos')
                ->with('error', 'Curso da aula não encontrado.');
        }

        // MATRÍCULA do usuário logado nesse curso
        $enrollment = $enrollmentModel
            ->where('id_course_enrollment',  (int) $course->id_course)
            ->where('id_student_enrollment', (int) $userId)      // <<< FILTRO PELO ESTUDANTE
            ->first();

        if (! $enrollment) {
            return redirect()->to('/student/dashboard/checkout/' . (int) $course->id_course)
                ->with('warning', 'Você precisa estar inscrito neste curso.');
        }

        // Força retomar apenas se tentar ir à frente
        $override = (int) (service('request')->getGet('override') ?? 0);
        [$resumeId, $orderedIds] = $calcResume((int) $course->id_course, (int) $enrollment->id_enrollment);

        if (! $override && $resumeId) {
            $reqIndex    = array_search((int) $lesson->id_lesson, $orderedIds, true);
            $resumeIndex = array_search((int) $resumeId,          $orderedIds, true);

            if ($reqIndex !== false && $resumeIndex !== false && $reqIndex > $resumeIndex) {
                return redirect()->to('/student/dashboard/ver_aulas/' . $resumeId)
                    ->with('warning', 'Conclua a aula anterior para continuar.');
            }
        }

        // Sidebar: módulos + aulas (sem interferir em prev/next)
        $modules = $moduleModel->where('id_course_module', $course->id_course)
            ->orderBy('position_module')
            ->findAll();

        foreach ($modules as &$m) {
            $m->lessons = $lessonModel->where('id_module_lesson', $m->id_module)
                ->orderBy('position_lesson')
                ->findAll();
        }
        unset($m);

        // IDs concluídos POR ESTA MATRÍCULA
        $completedLessonIds = array_column(
            $db->table('progress')
                ->select('id_lesson_progress')
                ->where('id_enrollment_progress', (int) $enrollment->id_enrollment)
                ->where('completed_at_progress IS NOT NULL', null, false)
                ->get()->getResultArray(),
            'id_lesson_progress'
        );

        // Prev/Next globais com base em $orderedIds
        $currIndex = array_search((int) $lesson->id_lesson, $orderedIds, true);
        $prevLesson = ($currIndex !== false && $currIndex > 0)
            ? $orderedIds[$currIndex - 1]
            : null;
        $nextLesson = ($currIndex !== false && $currIndex < count($orderedIds) - 1)
            ? $orderedIds[$currIndex + 1]
            : null;

        return view('pages/student/lessons', [
            'course'             => $course,
            'enrollment'         => (object) $enrollment,
            'modules'            => $modules,
            'lesson'             => $lesson,
            'prevLesson'         => $prevLesson,
            'nextLesson'         => $nextLesson,
            'completedLessonIds' => $completedLessonIds,
            'user'               => $authUser,
            'sidebarLinks'       => $this->sidebarLinks(),
            'currentUrl'         => current_url(false),
        ]);
    }

    public function courses()
    {
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $coursesModel    = new CourseModel();
        $lessonModel     = new \App\Models\LessonModel();
        $db              = db_connect();

        $user = service('auth')->user();

        // Todos os cursos (se a view usa)
        $courses = $coursesModel->findAll();

        // Todas as matrículas do usuário
        $enrollments = $enrollmentModel
            ->where('id_student_enrollment', $user->id)
            ->findAll();

        // IDs de cursos por status + mapa curso→enrollment row
        $activeCourseIds      = [];
        $pendingCourseIds     = [];
        $enrollmentByCourseId = []; // [id_course => enrollment row]

        foreach ($enrollments as $enr) {
            $courseId = (int) $enr->id_course_enrollment;
            $enrollmentByCourseId[$courseId] = $enr;

            if ($enr->status_enrollment === 'Ativa')    $activeCourseIds[]  = $courseId;
            if ($enr->status_enrollment === 'Pendente') $pendingCourseIds[] = $courseId;
        }

        // Cursos nos quais o aluno está inscrito (tua função custom)
        $lessons = $enrollmentModel->getStudentEnrolledCourses($user->id);

        // Helper: retorna a aula de retomada (primeira não concluída; se todas, a última)
        $calcResume = function (int $courseId, int $enrollmentId) use ($db) {
            // ordem global das aulas do curso
            $ordered = $db->table('lessons l')
                ->select('l.id_lesson')
                ->join('modules m', 'm.id_module = l.id_module_lesson')
                ->where('m.id_course_module', $courseId)
                ->orderBy('m.position_module', 'ASC')
                ->orderBy('l.position_lesson', 'ASC')
                ->get()->getResultArray();

            $orderedIds = array_map(fn($r) => (int)$r['id_lesson'], $ordered);
            if (empty($orderedIds)) return null;

            // aulas concluídas desta matrícula
            $completedLessonIds = array_column(
                $db->table('progress')
                    ->select('id_lesson_progress')
                    ->where('id_enrollment_progress', $enrollmentId)
                    ->where('completed_at_progress IS NOT NULL', null, false)
                    ->get()->getResultArray(),
                'id_lesson_progress'
            );
            $completedSet = array_flip($completedLessonIds);

            // primeira NÃO concluída; se todas concluídas, a última
            foreach ($orderedIds as $lid) {
                if (!isset($completedSet[$lid])) return $lid;
            }
            return end($orderedIds);
        };

        // Para cada curso inscrito: calcular resume + progresso
        foreach ($lessons as &$row) {
            $courseId   = (int) $row->id_course;
            $enrollment = $enrollmentByCourseId[$courseId] ?? null;

            // resume
            if ($enrollment) {
                $row->resumeLessonId = $calcResume($courseId, (int)$enrollment->id_enrollment);
            } else {
                $row->resumeLessonId = null;
            }

            // progresso (progress_enrollment da tabela enrollments)
            $row->progress = $enrollment ? (int) ($enrollment->progress_enrollment ?? 0) : 0;
        }
        unset($row);

        return view('pages/student/courses', [
            'user'            => $user,
            'courses'         => $courses,
            'lesson'         => $lessons,        // << a lista com resumeLessonId e progress
            'activeCourseIds' => $activeCourseIds,
            'pendingCourseIds' => $pendingCourseIds,
            'sidebarLinks'    => $this->sidebarLinks(),
            'currentUrl'      => current_url()
        ]);
    }

    public function checkout($idCourse)
    {
        $courseModel = new CourseModel();
        $course = $courseModel->find($idCourse);

        $enrollmentModel = new EnrollmentModel();
        $existingEnrollment = $enrollmentModel
            ->select('enrollments.*, courses.title_course')
            ->join('courses', 'courses.id_course = enrollments.id_course_enrollment')
            ->where('id_student_enrollment', service('auth')->user()->id)
            ->where('id_course_enrollment', $idCourse)
            ->first();

        $user = service('auth')->user();

        return view('pages/student/checkout', [
            'user' => $user,
            'course' => $course,
            'enrollment' => $existingEnrollment,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function profile()
    {
        $users = new ExtendedUserModel();
        $userModel = new UserModel();

        $user = auth()->user();

        if (! $user) {
            return redirect()->to(site_url('login'))
                ->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Sessão Expirada',
                    'text'  => 'Por favor, faça login novamente.'
                ]);
        }

        $profileUrl = current_url(false);

        // GET → exibe o perfil
        if ($this->request->getMethod() !== 'POST') {
            return view('pages/student/profile', [
                'user'         => $user,
                'sidebarLinks' => $this->sidebarLinks(),
                'currentUrl'   => $profileUrl,
            ]);
        }

        // Validação básica
        $rules = [
            'nome'      => 'permit_empty|min_length[2]',
            'pais'      => 'permit_empty|max_length[100]',
            'provincia' => 'permit_empty|max_length[100]',
            'cidade'    => 'permit_empty|max_length[100]',
            'telefone'  => 'permit_empty|max_length[20]',
            'imagem'    => 'if_exist|is_image[imagem]|mime_in[imagem,image/jpg,image/jpeg,image/png,image/webp]|max_size[imagem,4096]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to($profileUrl)
                ->withInput()
                ->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Erro!',
                    'text'  => implode(', ', $this->validator->getErrors())
                ]);
        }

        $post = $this->request->getPost();

        // Verificar duplicidade do nome
        $userName = trim($post['nome'] ?? '');

        if (! empty($userName)) {
            $existingUser = $userModel
                ->where('username', $userName)
                ->where('id !=', $user->id)
                ->first();

            if ($existingUser) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Erro!',
                    'text'  => 'Já existe um usuário com esse nome!'
                ]);
            }
        }

        // Upload da imagem
        $file     = $this->request->getFile('imagem');
        $filePath = $user->img;

        if ($file && $file->isValid() && $file->getError() === UPLOAD_ERR_OK) {

            $targetDir = FCPATH . 'assets/img/';
            if (! is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            $newName = $file->getRandomName();

            if (! $file->move($targetDir, $newName)) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon' => 'error',
                    'title' => 'Erro!',
                    'text' => 'Falha ao mover a imagem.'
                ]);
            }

            // Remover imagem antiga se existir
            if (! empty($user->img)) {
                $old = FCPATH . $user->img;
                if (is_file($old)) {
                    @unlink($old);
                }
            }

            $filePath = 'assets/img/' . $newName;
        }

        // Criar payload de atualização
        $dataProfile = [
            'username' => $userName ?: $user->username,
            'img'      => $filePath,
            'country'  => $post['pais']      ?? $user->country,
            'province' => $post['provincia'] ?? $user->province,
            'city'     => $post['cidade']    ?? $user->city,
            'phone'    => $post['telefone']  ?? $user->phone,
        ];

        // ---------------------------------------
        //    >>> ALTERAÇÃO DE SENHA (SHIELD) <<<
        // ---------------------------------------
        $currentPassword = $post['password_actual'] ?? '';
        $newPassword     = $post['new_password'] ?? '';
        $confirmPassword = $post['confirm_password'] ?? '';

        if ($currentPassword || $newPassword || $confirmPassword) {

            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon' => 'error',
                    'title' => 'Erro!',
                    'text' => 'Para alterar a senha, preencha todos os campos.'
                ]);
            }

            // Buscar identidade do Shield
            $identity = db_connect()
                ->table('auth_identities')
                ->where('user_id', $user->id)
                ->where('type', 'email_password')
                ->get()
                ->getRow();

            if (! $identity) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon' => 'error',
                    'title' => 'Erro interno',
                    'text' => 'Identidade de senha não encontrada.'
                ]);
            }

            // Verificar senha atual
            if (! password_verify($currentPassword, $identity->secret)) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon' => 'error',
                    'title' => 'Senha incorreta',
                    'text' => 'A senha atual fornecida está incorreta.'
                ]);
            }

            // Nova senha
            if (strlen($newPassword) < 6) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon' => 'error',
                    'title' => 'Erro!',
                    'text' => 'A nova senha deve ter no mínimo 6 caracteres.'
                ]);
            }

            if ($newPassword !== $confirmPassword) {
                return redirect()->to($profileUrl)->with('swal', [
                    'icon' => 'error',
                    'title' => 'Erro!',
                    'text' => 'A confirmação da senha não coincide.'
                ]);
            }

            // Atualizar senha no Shield
            db_connect()
                ->table('auth_identities')
                ->where('id', $identity->id)
                ->update([
                    'secret'     => password_hash($newPassword, PASSWORD_DEFAULT),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        // Atualizar dados do usuário
        if (! $users->update($user->id, $dataProfile)) {
            return redirect()->to($profileUrl)->with('swal', [
                'icon' => 'error',
                'title' => 'Erro!',
                'text' => implode(', ', $users->errors())
            ]);
        }

        // Atualizar sessão
        $updated = $users->find($user->id);
        auth()->setUser($updated);

        return redirect()->to($profileUrl)->with('swal', [
            'icon'  => 'success',
            'title' => 'Sucesso!',
            'text'  => 'Perfil atualizado com sucesso.'
        ]);
    }
}
