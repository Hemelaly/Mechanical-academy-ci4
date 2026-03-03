<?php

namespace App\Controllers\Instructor;

use App\Controllers\BaseController;
use App\Models\CertificateModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CourseModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;
use App\Models\CourseSettingModel;
use App\Models\ExtendedUserModel;
use App\Models\JitsiModel;
use App\Models\JitsiRecordingModel;
use App\Models\PendingUserModel;
use App\Libraries\JitsiJwtService;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Authentication\Passwords;
use CodeIgniter\Shield\Models\PasswordResetModel;

class Dashboard extends BaseController
{
    private function normalizeCourseCompat(?object $course): ?object
    {
        if (! $course) {
            return null;
        }

        $learning = trim((string) ($course->learning_course ?? ''));
        $legacyLearning = trim((string) ($course->what_learn_course ?? ''));
        if ($learning === '' && $legacyLearning !== '') {
            $course->learning_course = $legacyLearning;
        }

        return $course;
    }

    private function wantsJson(): bool
    {
        return $this->request->isAJAX()
            || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    private function jsonMessage(string $message, int $statusCode = 200, array $extra = [])
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON(array_merge([
                'message' => $message,
                'csrf' => csrf_hash(),
            ], $extra));
    }

    private function sidebarLinks()
    {
        return [
            ['label' => 'Iní­cio', 'icon' => 'bi-house-door', 'url' => '/instructor/dashboard'],
            ['label' => 'Meus Cursos', 'icon' => 'bi-book', 'url' => '/instructor/dashboard/meus_cursos'],
            ['label' => 'Aula ao Vivo', 'icon' => 'bi-camera-reels', 'url' => '/instructor/dashboard/jitsi'],
            ['label' => 'Estudantes', 'icon' => 'bi-people', 'url' => '/instructor/dashboard/meus_estudantes'],
            ['label' => 'Financas', 'icon' => 'bi-cash-coin', 'url' => '/instructor/dashboard/financas'],
            ['label' => 'Logs', 'icon' => 'bi-journal-text', 'url' => '/instructor/dashboard/logs'],
            ['label' => 'Certificados', 'icon' => 'bi-folder', 'url' => '/instructor/dashboard/certificados'],
            ['label' => 'Perfil', 'icon' => 'bi-person-circle', 'url' => '/instructor/dashboard/perfil'],
        ];
    }

    private function makeJitsiRoom(string $title): string
    {
        $cfg = config('Jitsi');
        $prefix = trim((string) ($cfg->roomPrefix ?? 'academy'));
        $prefix = $prefix !== '' ? $prefix : 'academy';

        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($title)));
        $slug = trim((string) $slug, '-');
        $slug = $slug !== '' ? substr($slug, 0, 32) : 'live';

        try {
            $suffix = substr(bin2hex(random_bytes(6)), 0, 10);
        } catch (\Throwable $e) {
            $suffix = substr(sha1(uniqid('', true)), 0, 10);
        }

        return $prefix . '-' . $slug . '-' . $suffix;
    }

    public function index()
    {
        $user = service('auth')->user();

        return view('pages/instructor/home', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function my_courses()
    {
        $courseModel = new CourseModel();
        $user = service('auth')->user();
        $courses = $courseModel->getCoursesByInstructor($user->id);

        return view('pages/instructor/my_courses', [
            'user' => $user,
            'courses' => $courses,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function add_course()
    {
        $user = service('auth')->user();
        $courseModel = new CourseModel();
        $moduleModel = new ModuleModel();
        $lessonModel = new LessonModel ();
        $projectModel = new \App\Models\ProjectModel();

        $savedDraft = $courseModel
            ->where('id_instructor_course', $user->id)
            ->where('status_course', 'Rascunho')
            ->orderBy('updated_at', 'DESC')
            ->first();
        $loadDraft = $this->request->getGet('load_draft') === '1';
        $draft = $loadDraft ? $savedDraft : null;
        $savedDraft = $this->normalizeCourseCompat($savedDraft);
        $draft = $this->normalizeCourseCompat($draft);

        $draftModules = [];
        $draftProjects = [];
        if ($draft) {
            $draftModules = $moduleModel
                ->where('id_course_module', $draft->id_course)
                ->orderBy('position_module')
                ->findAll();

            foreach ($draftModules as &$m) {
                $m->lessons = $lessonModel
                    ->where('id_module_lesson', $m->id_module)
                    ->orderBy('position_lesson')
                    ->findAll();
            }
            unset($m);

            $draftProjects = $projectModel
                ->where('id_course_project', $draft->id_course)
                ->findAll();
        }

        return view('pages/instructor/add_course', [
            'user' => $user,
            'draft' => $draft,
            'savedDraft' => $savedDraft,
            'draftModules' => $draftModules,
            'draftProjects' => $draftProjects,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function edit_course($id)
    {
        $user = service('auth')->user();

        $courseModel = new \App\Models\CourseModel();
        $moduleModel = new \App\Models\ModuleModel();
        $lessonModel = new \App\Models\LessonModel();
        $projectModel = new \App\Models\ProjectModel();

        if (!$id) {
            return redirect()->to('instructor/dashboard/meus_cursos')
                ->with('error', 'ID do curso nÃƒÂ£o fornecido');
        }

        $course = $courseModel->find($id);
        $course = $this->normalizeCourseCompat($course);

        if (!$course) {
            return redirect()->to('instructor/dashboard/meus_cursos')
                ->with('error', 'Curso nÃƒÂ£o encontrado');
        }

        if ($course->id_instructor_course != auth()->id()) {
            return redirect()->to('instructor/dashboard/meus_cursos')
                ->with('error', 'Acesso negado');
        }

        // Ã°Å¸â€Â¹ Carregar mÃƒÂ³dulos e aulas
        $modules = $moduleModel
            ->where('id_course_module', $id)
            ->orderBy('position_module')
            ->findAll();

        foreach ($modules as &$m) {
            $m->lessons = $lessonModel
                ->where('id_module_lesson', $m->id_module)
                ->orderBy('position_lesson')
                ->findAll();
        }

        $projects = $projectModel
            ->where('id_course_project', $id)
            ->findAll();

        $db = db_connect();
        $enrolledCount = (int) $db->table('enrollments')
            ->where('id_course_enrollment', (int) $id)
            ->where('status_enrollment', 'ativa')
            ->countAllResults();

        // Ã¢Å“â€¦ APENAS retorna a view
        return view('pages/instructor/edit_course', [
            'user'         => $user,
            'course'       => $course,
            'modules'      => $modules,
            'projects'     => $projects,
            'enrolledCount' => $enrolledCount,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl'   => current_url()
        ]);
    }

    public function live($id = null)
    {
        $user  = service('auth')->user();
        $req   = $this->request;
        $model = new JitsiModel();
        $courseModel = new CourseModel();
        $recordingModel = new JitsiRecordingModel();
        if ($req->getMethod() === 'GET') {
            $aulas = $model->where('id_user_jitsi', $user->id)
                ->orderBy('id_jitsi', 'DESC')
                ->findAll();
            $recordingStats = [];
            $aulaIds = array_map(static fn ($a) => (int) $a->id_jitsi, $aulas);
            if (! empty($aulaIds)) {
                $stats = $recordingModel
                    ->select('id_jitsi_session, COUNT(*) as total_recordings, SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published_recordings', false)
                    ->whereIn('id_jitsi_session', $aulaIds)
                    ->groupBy('id_jitsi_session')
                    ->findAll();
                foreach ($stats as $s) {
                    $recordingStats[(int) $s->id_jitsi_session] = [
                        'total' => (int) ($s->total_recordings ?? 0),
                        'published' => (int) ($s->published_recordings ?? 0),
                    ];
                }
            }
            $courses = $courseModel->getCoursesByInstructor($user->id);
            return view('pages/instructor/live_class', [
                'user'         => $user,
                'sidebarLinks' => $this->sidebarLinks(),
                'currentUrl'   => current_url(),
                'aulas'        => $aulas,
                'courses'      => $courses,
                'recordingStats' => $recordingStats,
            ]);
        }
        $editId = (int) ($req->getPost('id_jitsi') ?: $id ?: 0);
        $isEdit = $editId > 0;
        $existingAula = null;
        if ($isEdit) {
            $existingAula = $model->find($editId);
            if (! $existingAula || (int) $existingAula->id_user_jitsi !== (int) $user->id) {
                return redirect()->to('/instructor/dashboard/jitsi')->with('error', 'Aula nao encontrada ou sem permissao.');
            }
        }
        $rules = [
            'classTitle' => [
                'label'  => 'Titulo da Aula',
                'rules'  => 'required|min_length[3]|max_length[255]',
            ],
            'classDescription' => [
                'label'  => 'Descricao',
                'rules'  => 'permit_empty|max_length[65535]',
            ],
            'associatedCourse' => [
                'label'  => 'Curso Associado',
                'rules'  => 'permit_empty|integer',
            ],
            'classType' => [
                'label' => 'Tipo de Aula',
                'rules' => 'required|in_list[instant,scheduled]',
            ],
            'roomStatus' => [
                'label' => 'Estado',
                'rules' => 'required|in_list[Pendente,Ao vivo,Expirado]',
            ],
            'roomPrivacy' => [
                'label' => 'Privacidade',
                'rules' => 'required|in_list[public,private,password]',
            ],
            'roomPassword' => [
                'label' => 'Senha da Sala',
                'rules' => 'permit_empty|min_length[4]|max_length[100]',
            ],
        ];
        if ($req->getPost('classType') === 'scheduled') {
            $rules['classDate'] = [
                'label' => 'Data da Aula',
                'rules' => 'required|valid_date[Y-m-d]',
            ];
            $rules['startTime'] = [
                'label' => 'Hora de Inicio',
                'rules' => 'required',
            ];
            $rules['endTime'] = [
                'label' => 'Hora de Termino',
                'rules' => 'required',
            ];
        }
        $roomPassword = trim((string) $req->getPost('roomPassword'));
        if ($req->getPost('roomPrivacy') === 'password' && (! $isEdit || $roomPassword !== '')) {
            $rules['roomPassword']['rules'] = 'required|min_length[4]|max_length[100]';
        }
        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        $courseId = (int) ($req->getPost('associatedCourse') ?: 0);
        if ($courseId > 0) {
            $ownedCourse = $courseModel
                ->where('id_course', $courseId)
                ->where('id_instructor_course', $user->id)
                ->first();
            if (! $ownedCourse) {
                return redirect()->back()->withInput()->with('error', 'Curso associado invalido.');
            }
        }
        $data = [
            'title_jitsi'        => trim((string) $req->getPost('classTitle')),
            'description_jitsi'  => trim((string) $req->getPost('classDescription')),
            'id_course_jitsi'    => $courseId > 0 ? $courseId : null,
            'class_type_jitsi'   => (string) $req->getPost('classType'),
            'meeting_date_jitsi' => $req->getPost('classDate') ?: null,
            'start_time_jitsi'   => $req->getPost('startTime') ?: null,
            'end_time_jitsi'     => $req->getPost('endTime') ?: null,
            'status_jitsi'       => (string) $req->getPost('roomStatus'),
            'privacy_jitsi'      => (string) $req->getPost('roomPrivacy'),
            'recording_jitsi'    => $req->getPost('enableRecording') ? 1 : 0,
            'chat_jitsi'         => $req->getPost('enableChat') ? 1 : 0,
            'screenshare_jitsi'  => $req->getPost('enableScreenShare') ? 1 : 0,
            'id_user_jitsi'      => $user->id,
        ];
        if ((string) $req->getPost('roomPrivacy') === 'password') {
            if ($roomPassword !== '') {
                $data['password_jitsi'] = password_hash($roomPassword, PASSWORD_DEFAULT);
            } elseif (! $isEdit) {
                $data['password_jitsi'] = null;
            }
        } else {
            $data['password_jitsi'] = null;
        }
        if (! $isEdit) {
            $data['room_jitsi'] = $this->makeJitsiRoom((string) $req->getPost('classTitle'));
        }
        if ($isEdit) {
            $model->update($editId, $data);
            return redirect()
                ->to('/instructor/dashboard/jitsi')
                ->with('swal', [
                    'icon'  => 'success',
                    'title' => 'Aula atualizada!',
                    'text'  => 'A aula foi editada com sucesso.'
                ]);
        }
        $model->insert($data);
        return redirect()
            ->to('/instructor/dashboard/jitsi')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Aula criada!',
                'text'  => 'A nova aula virtual foi criada com sucesso.'
            ]);
    }
    public function deleteJitsi($id)
    {
        $user  = service('auth')->user();
        $model = new JitsiModel();
        // Verifica se a aula existe
        $aula = $model->find($id);
        if (!$aula) {
            $this->auditLogger->write(
                'instructor.jitsi.delete_not_found',
                'warning',
                'Tentativa de excluir aula inexistente.',
                [
                    'jitsi_id' => (int) $id,
                ]
            );
            return redirect()->back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Aula nao encontrada',
                'text'  => 'A aula que tentou excluir nao existe.'
            ]);
        }
        // Impede que um instrutor exclua aula de outro instrutor
        if ($aula->id_user_jitsi != $user->id) {
            $this->auditLogger->write(
                'instructor.jitsi.delete_denied',
                'warning',
                'Tentativa de excluir aula de outro instrutor.',
                [
                    'jitsi_id' => (int) $id,
                    'owner_user_id' => (int) $aula->id_user_jitsi,
                ]
            );
            return redirect()->back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Acesso negado',
                'text'  => 'Voce nao tem permissao para excluir esta aula.'
            ]);
        }
        // Delete
        if ($model->delete($id)) {
            $this->auditLogger->write(
                'instructor.jitsi.deleted',
                'info',
                'Aula ao vivo removida.',
                [
                    'jitsi_id' => (int) $id,
                    'owner_user_id' => (int) $aula->id_user_jitsi,
                ]
            );
            return redirect()
                ->to('/instructor/dashboard/jitsi')
                ->with('swal', [
                    'icon'  => 'success',
                    'title' => 'Aula excluida!',
                    'text'  => 'A aula foi removida com sucesso.'
                ]);
        }
        // Caso ocorra algum erro inesperado
        $this->auditLogger->write(
            'instructor.jitsi.delete_failed',
            'error',
            'Falha ao excluir aula ao vivo.',
            [
                'jitsi_id' => (int) $id,
                'owner_user_id' => (int) $aula->id_user_jitsi,
            ]
        );
        return redirect()
            ->back()
            ->with('swal', [
                'icon'  => 'error',
                'title' => 'Erro ao excluir',
                'text'  => 'Nao foi possivel excluir esta aula.'
            ]);
    }
    public function stream($id)
    {
        $user  = service('auth')->user();
        $model = new JitsiModel();
        $recordingModel = new JitsiRecordingModel();
        /** @var JitsiJwtService $jitsiJwt */
        $jitsiJwt = service('jitsiJwt');
        $aula = $model->find($id);
        if (! $aula) {
            return redirect()->back()->with('swal', [
                'icon' => 'error',
                'title' => 'Aula nao encontrada',
                'text' => 'A aula que tentou acessar nao existe.'
            ]);
        }
        if ((int) $aula->id_user_jitsi !== (int) $user->id) {
            return redirect()->back()->with('swal', [
                'icon' => 'error',
                'title' => 'Sem permissao',
                'text' => 'Voce nao pode acessar esta sala.'
            ]);
        }
        $model->update((int) $aula->id_jitsi, ['status_jitsi' => 'Ao vivo']);
        $aula = $model->find($id);
        $displayName = trim((string) ($user->username ?? $user->name ?? ('Instrutor ' . $user->id)));
        $email = trim((string) ($user->email ?? ''));
        $avatar = ! empty($user->img) ? base_url((string) $user->img) : '';
        $token = null;
        try {
            $token = $jitsiJwt->buildToken(
                (string) $aula->room_jitsi,
                [
                    'id' => (string) $user->id,
                    'name' => $displayName,
                    'email' => $email,
                    'avatar' => $avatar,
                ],
                true,
                [
                    'recording' => (bool) $aula->recording_jitsi,
                    'screen-sharing' => (bool) $aula->screenshare_jitsi,
                ]
            );
        } catch (\Throwable $e) {
            log_message('warning', 'Falha ao gerar JWT do Jitsi: {message}', ['message' => $e->getMessage()]);
        }
        $recordings = $recordingModel
            ->where('id_jitsi_session', (int) $aula->id_jitsi)
            ->orderBy('id_jitsi_recording', 'DESC')
            ->findAll();
        return view('pages/instructor/live_stream', [
            'aula' => $aula,
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
            'jitsiDomain' => $jitsiJwt->getDomain(),
            'jitsiExternalApiScript' => $jitsiJwt->getExternalApiScriptUrl(),
            'jitsiRoomName' => $jitsiJwt->buildRoomName((string) $aula->room_jitsi),
            'jitsiToken' => $token,
            'jitsiRecordingMode' => $jitsiJwt->getDefaultRecordingMode(),
            'canModerate' => true,
            'canManageRecordings' => true,
            'backUrl' => site_url('instructor/dashboard/jitsi'),
            'endStreamUrl' => site_url('instructor/dashboard/jitsi/stream/' . (int) $aula->id_jitsi . '/end'),
            'saveRecordingUrl' => site_url('instructor/dashboard/jitsi/stream/' . (int) $aula->id_jitsi . '/recording'),
            'publishToggleBaseUrl' => site_url('instructor/dashboard/jitsi/recordings'),
            'recordings' => $recordings,
        ]);
    }
    public function endStream($id)
    {
        $user  = service('auth')->user();
        $model = new JitsiModel();
        $aula = $model->find($id);
        if (! $aula || (int) $aula->id_user_jitsi !== (int) $user->id) {
            return redirect()->to('/instructor/dashboard/jitsi')->with('error', 'Aula nao encontrada ou sem permissao.');
        }
        $model->update((int) $aula->id_jitsi, ['status_jitsi' => 'Expirado']);
        return redirect()->to('/instructor/dashboard/jitsi')->with('swal', [
            'icon' => 'success',
            'title' => 'Aula encerrada',
            'text' => 'A transmissao foi encerrada com sucesso.',
        ]);
    }
    public function storeRecording($id)
    {
        $user  = service('auth')->user();
        $jitsiModel = new JitsiModel();
        $recordingModel = new JitsiRecordingModel();
        $aula = $jitsiModel->find($id);
        if (! $aula || (int) $aula->id_user_jitsi !== (int) $user->id) {
            if ($this->wantsJson()) {
                return $this->jsonMessage('Aula nao encontrada ou sem permissao.', 403);
            }
            return redirect()->to('/instructor/dashboard/jitsi')->with('error', 'Aula nao encontrada ou sem permissao.');
        }
        $rules = [
            'recording_url' => 'required|valid_url|max_length[2048]',
            'provider_recording_id' => 'permit_empty|max_length[255]',
            'recording_mode' => 'required|in_list[file,stream,local,manual]',
            'status_recording' => 'required|in_list[pending,processing,ready,failed]',
            'duration_seconds' => 'permit_empty|integer',
            'publish_now' => 'permit_empty|in_list[0,1,on]',
        ];
        if (! $this->validate($rules)) {
            if ($this->wantsJson()) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'message' => 'Dados invalidos para gravacao.',
                        'errors' => $this->validator->getErrors(),
                        'csrf' => csrf_hash(),
                    ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $publishNow = in_array((string) $this->request->getPost('publish_now'), ['1', 'on'], true);
        $providerId = trim((string) $this->request->getPost('provider_recording_id'));
        $data = [
            'id_jitsi_session' => (int) $aula->id_jitsi,
            'recording_url' => trim((string) $this->request->getPost('recording_url')),
            'provider_recording_id' => $providerId !== '' ? $providerId : null,
            'recording_mode' => (string) $this->request->getPost('recording_mode'),
            'status_recording' => (string) $this->request->getPost('status_recording'),
            'duration_seconds' => $this->request->getPost('duration_seconds') !== null && $this->request->getPost('duration_seconds') !== ''
                ? (int) $this->request->getPost('duration_seconds')
                : null,
            'is_published' => $publishNow ? 1 : 0,
            'published_at' => $publishNow ? date('Y-m-d H:i:s') : null,
        ];
        if ($providerId !== '') {
            $existing = $recordingModel
                ->where('id_jitsi_session', (int) $aula->id_jitsi)
                ->where('provider_recording_id', $providerId)
                ->first();
            if ($existing) {
                $recordingModel->update((int) $existing->id_jitsi_recording, $data);
                $recordingId = (int) $existing->id_jitsi_recording;
            } else {
                $recordingModel->insert($data);
                $recordingId = (int) $recordingModel->getInsertID();
            }
        } else {
            $recordingModel->insert($data);
            $recordingId = (int) $recordingModel->getInsertID();
        }
        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'message' => 'Gravacao salva.',
                'recording_id' => $recordingId,
                'csrf' => csrf_hash(),
            ]);
        }
        return redirect()->back()->with('swal', [
            'icon' => 'success',
            'title' => 'Gravacao salva',
            'text' => 'O link da gravacao foi guardado.',
        ]);
    }
    public function toggleRecordingPublish($recordingId)
    {
        $user  = service('auth')->user();
        $db = db_connect();
        $row = $db->table('jitsi_recordings r')
            ->select('r.id_jitsi_recording, r.is_published, r.id_jitsi_session, j.id_user_jitsi')
            ->join('jitsi j', 'j.id_jitsi = r.id_jitsi_session')
            ->where('r.id_jitsi_recording', (int) $recordingId)
            ->get()
            ->getRow();
        if (! $row || (int) $row->id_user_jitsi !== (int) $user->id) {
            return redirect()->back()->with('error', 'Gravacao nao encontrada ou sem permissao.');
        }
        $publish = ! (bool) $row->is_published;
        $recordingModel = new JitsiRecordingModel();
        $recordingModel->update((int) $row->id_jitsi_recording, [
            'is_published' => $publish ? 1 : 0,
            'published_at' => $publish ? date('Y-m-d H:i:s') : null,
        ]);
        return redirect()->back()->with('swal', [
            'icon' => 'success',
            'title' => $publish ? 'Gravacao publicada' : 'Gravacao despublicada',
            'text' => $publish
                ? 'Os alunos ja podem ver esta gravacao.'
                : 'A gravacao foi ocultada dos alunos.',
        ]);
    }
    public function students()
    {
        $user = service('auth')->user();

        $enrollmentModel = new \App\Models\EnrollmentModel();

        $enrollments = $enrollmentModel
            ->getInstructorEnrollments($user->id)
            ->paginate(5, 'enrollments');

        $paymentModel = new \App\Models\PaymentModel();
        $payments = $paymentModel->getInstructorPendingPayments($user->id);

        return view('pages/instructor/students', [
            'user' => $user,
            'payments' => $payments,
            'enrollments' => $enrollments,
            'pager' => $enrollmentModel->pager,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
        ]);
    }

    public function studentsData()
    {
        $user = service('auth')->user();
        $search = trim((string) $this->request->getGet('q'));
        $status = strtolower((string) $this->request->getGet('status'));
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = (int) $this->request->getGet('per_page');
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min(max($perPage, 5), 50);
        $offset = ($page - 1) * $perPage;

        $db = db_connect();
        $builder = $db->table('enrollments e')
            ->select([
                'e.id_enrollment',
                'e.status_enrollment',
                'e.progress_enrollment',
                'e.updated_at AS last_enrollment_update',
                's.name_student',
                's.email_student',
                'c.title_course',
            ])
            ->select('(SELECT MAX(COALESCE(p.updated_at, p.created_at, p.completed_at_progress)) FROM progress p WHERE p.id_enrollment_progress = e.id_enrollment) AS last_activity', false)
            ->join('courses c', 'c.id_course = e.id_course_enrollment')
            ->join('students s', 's.id_user_student = e.id_student_enrollment')
            ->where('c.id_instructor_course', $user->id);

        if ($search !== '') {
            $builder->groupStart()
                ->like('s.name_student', $search)
                ->orLike('s.email_student', $search)
                ->orLike('c.title_course', $search)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('e.status_enrollment', $status);
        }

        $countBuilder = clone $builder;
        $total = (int) $countBuilder->countAllResults();

        $rows = $builder
            ->orderBy('e.updated_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $totalPages = (int) ceil($total / $perPage);

        return $this->response->setJSON([
            'items' => $rows,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ]);
    }

    public function pendingPaymentsData()
    {
        $user = service('auth')->user();
        $search = trim((string) $this->request->getGet('q'));
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = (int) $this->request->getGet('per_page');
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min(max($perPage, 5), 50);
        $offset = ($page - 1) * $perPage;

        $db = db_connect();
        $builder = $db->table('payments p')
            ->select([
                'p.id_payment',
                'p.status_payment',
                'p.proof_file_payment',
                'p.created_at',
                'pu.id AS id_user_payment',
                'pu.username',
                'pu.email',
                'c.id_course',
                'c.title_course',
            ])
            ->join('pending_users pu', 'pu.id = p.id_user_payment')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('c.id_instructor_course', $user->id)
            ->where('p.status_payment', 'Pendente');

        if ($search !== '') {
            $builder->groupStart()
                ->like('pu.username', $search)
                ->orLike('pu.email', $search)
                ->orLike('c.title_course', $search)
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $total = (int) $countBuilder->countAllResults();

        $rows = $builder
            ->orderBy('p.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $totalPages = (int) ceil($total / $perPage);

        return $this->response->setJSON([
            'items' => $rows,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ]);
    }

    public function toggleEnrollment($enrollmentId)
    {
        $user = service('auth')->user();
        $enrollmentId = (int) $enrollmentId;

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $row = $enrollmentModel
            ->select('enrollments.id_enrollment, enrollments.status_enrollment, courses.id_instructor_course')
            ->join('courses', 'courses.id_course = enrollments.id_course_enrollment')
            ->where('enrollments.id_enrollment', $enrollmentId)
            ->get()
            ->getRow();

        if (! $row || (int) $row->id_instructor_course !== (int) $user->id) {
            $this->auditLogger->write(
                'instructor.enrollment.toggle_denied',
                'warning',
                'Tentativa de alterar matricula sem permissao.',
                [
                    'enrollment_id' => $enrollmentId,
                ]
            );

            if ($this->wantsJson()) {
                return $this->jsonMessage('Acesso negado.', 403);
            }
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $currentStatus = strtolower((string) $row->status_enrollment);
        $newStatus = $currentStatus === 'ativa' ? 'cancelada' : 'ativa';
        $updated = $enrollmentModel->update($enrollmentId, ['status_enrollment' => $newStatus]);

        $this->auditLogger->write(
            'instructor.enrollment.toggled',
            $updated ? 'info' : 'error',
            $updated
                ? 'Status de matricula alterado.'
                : 'Falha ao alterar status de matricula.',
            [
                'enrollment_id' => $enrollmentId,
                'from_status' => $currentStatus,
                'to_status' => $newStatus,
            ]
        );

        $msg = $newStatus === 'ativa'
            ? 'Acesso do aluno liberado.'
            : 'Acesso do aluno bloqueado.';

        if ($this->wantsJson()) {
            return $this->jsonMessage($msg);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function financial()
    {
        $user = service('auth')->user();

        return view('pages/instructor/financial', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url()
        ]);
    }

    public function financialData()
    {
        $user = service('auth')->user();
        $year = (int) $this->request->getGet('year');
        if ($year <= 0) {
            $year = (int) date('Y');
        }

        $db = db_connect();
        $rows = $db->table('payments p')
            ->select('MONTH(p.created_at) as month, SUM(p.amount_payment) as total')
            ->join('courses c', 'c.id_course = p.id_course_payment')
            ->where('p.status_payment', 'Aprovado')
            ->where('c.id_instructor_course', $user->id)
            ->where('YEAR(p.created_at)', $year)
            ->groupBy('MONTH(p.created_at)')
            ->orderBy('MONTH(p.created_at)')
            ->get()
            ->getResultArray();

        $totals = array_fill(1, 12, 0.0);
        foreach ($rows as $row) {
            $month = (int) ($row['month'] ?? 0);
            if ($month >= 1 && $month <= 12) {
                $totals[$month] = (float) ($row['total'] ?? 0);
            }
        }

        $labels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $series = array_values($totals);

        return $this->response->setJSON([
            'labels' => $labels,
            'data' => $series,
        ]);
    }

    public function logs()
    {
        $user = service('auth')->user();

        return view('pages/instructor/logs', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
        ]);
    }

    public function logsData()
    {
        $user = service('auth')->user();
        $search = trim((string) $this->request->getGet('q'));
        $level = strtolower(trim((string) $this->request->getGet('level')));
        $dateFrom = $this->normalizeDateFilter((string) $this->request->getGet('date_from'));
        $dateTo = $this->normalizeDateFilter((string) $this->request->getGet('date_to'));
        if ($dateFrom !== null && $dateTo !== null && $dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }
        $page = max(1, (int) $this->request->getGet('page'));
        $perPage = (int) $this->request->getGet('per_page');
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min(max($perPage, 5), 50);
        $offset = ($page - 1) * $perPage;

        $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        if ($level !== '' && ! in_array($level, $levels, true)) {
            $level = '';
        }

        $builder = $this->buildAuditLogQuery((int) $user->id, $search, $level, $dateFrom, $dateTo);

        $countBuilder = clone $builder;
        $total = (int) $countBuilder->countAllResults();

        $rows = $builder
            ->orderBy('created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $items = array_map(fn (array $row): array => $this->mapAuditRow($row), $rows);

        $totalPages = (int) ceil($total / $perPage);

        return $this->response->setJSON([
            'items' => $items,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ]);
    }

    public function logsExportCsv()
    {
        $user = service('auth')->user();
        $search = trim((string) $this->request->getGet('q'));
        $level = strtolower(trim((string) $this->request->getGet('level')));
        $dateFrom = $this->normalizeDateFilter((string) $this->request->getGet('date_from'));
        $dateTo = $this->normalizeDateFilter((string) $this->request->getGet('date_to'));
        if ($dateFrom !== null && $dateTo !== null && $dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        if ($level !== '' && ! in_array($level, $levels, true)) {
            $level = '';
        }

        $rows = $this->buildAuditLogQuery((int) $user->id, $search, $level, $dateFrom, $dateTo)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $filename = 'logs-' . (int) $user->id . '-' . date('Ymd-His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        if ($handle === false) {
            return $this->response->setStatusCode(500)->setBody('Falha ao gerar CSV.');
        }

        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, ['Data', 'Nivel', 'Evento', 'Mensagem', 'Metodo', 'Rota', 'IP', 'Contexto']);

        foreach ($rows as $row) {
            $mapped = $this->mapAuditRow($row);
            fputcsv($handle, [
                (string) ($mapped['created_at'] ?? ''),
                (string) ($mapped['level_audit_log'] ?? ''),
                (string) ($mapped['event_audit_log'] ?? ''),
                (string) ($mapped['message_audit_log'] ?? ''),
                (string) ($mapped['method_audit_log'] ?? ''),
                (string) ($mapped['uri_audit_log'] ?? ''),
                (string) ($mapped['ip_address_audit_log'] ?? ''),
                (string) ($mapped['context_pretty'] ?? ''),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv === false ? '' : $csv);
    }

    private function buildAuditLogQuery(int $userId, string $search, string $level, ?string $dateFrom, ?string $dateTo)
    {
        $builder = db_connect()->table('audit_logs')
            ->select([
                'id_audit_log',
                'event_audit_log',
                'level_audit_log',
                'message_audit_log',
                'method_audit_log',
                'uri_audit_log',
                'ip_address_audit_log',
                'context_audit_log',
                'created_at',
            ])
            ->where('actor_user_id', $userId);

        if ($search !== '') {
            $builder->groupStart()
                ->like('event_audit_log', $search)
                ->orLike('message_audit_log', $search)
                ->orLike('uri_audit_log', $search)
                ->orLike('context_audit_log', $search)
                ->groupEnd();
        }

        if ($level !== '') {
            $builder->where('level_audit_log', $level);
        }

        if ($dateFrom !== null) {
            $builder->where('created_at >=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo !== null) {
            $builder->where('created_at <=', $dateTo . ' 23:59:59');
        }

        return $builder;
    }

    private function normalizeDateFilter(string $date): ?string
    {
        $date = trim($date);
        if ($date === '') {
            return null;
        }

        $parsed = \DateTime::createFromFormat('Y-m-d', $date);
        if (! $parsed) {
            return null;
        }

        return $parsed->format('Y-m-d') === $date ? $date : null;
    }

    private function mapAuditRow(array $row): array
    {
        $context = trim((string) ($row['context_audit_log'] ?? ''));
        $decoded = null;
        if ($context !== '') {
            $decoded = json_decode($context, true);
        }

        if (is_array($decoded)) {
            $pretty = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $row['context_pretty'] = $pretty === false ? $context : $pretty;
        } else {
            $row['context_pretty'] = $context;
        }

        return $row;
    }

    public function profile()
    {
        $users = new ExtendedUserModel();
        $userModel = new UserModel();
        $courseModel = new CourseModel();

        $user = auth()->user();

        if (! $user) {
            return redirect()->to(site_url('login'))
                ->with('error', 'SessÃƒÂ£o expirada. FaÃƒÂ§a login novamente.');
        }

        $profileUrl = current_url(false);
        $db = db_connect();
        $courseCount = (int) $courseModel
            ->where('id_instructor_course', $user->id)
            ->countAllResults();
        $avgProgressRow = $db->table('enrollments e')
            ->selectAvg('e.progress_enrollment', 'avg_progress')
            ->join('courses c', 'c.id_course = e.id_course_enrollment')
            ->where('c.id_instructor_course', $user->id)
            ->where('e.status_enrollment', 'ativa')
            ->get()
            ->getRow();
        $avgProgress = (int) round($avgProgressRow->avg_progress ?? 0);

        if ($this->request->getMethod() !== 'POST') {
            return view('pages/instructor/profile', [
                'user'         => $user,
                'courseCount'  => $courseCount,
                'avgProgress'  => $avgProgress,
                'sidebarLinks' => $this->sidebarLinks(),
                'currentUrl'   => $profileUrl,
            ]);
        }

        // ValidaÃƒÂ§ÃƒÂ£o
        $rules = [
            'nome'      => 'permit_empty|min_length[2]',
            'email'     => 'permit_empty|valid_email',
            'pais'      => 'permit_empty|max_length[100]',
            'provincia' => 'permit_empty|max_length[100]',
            'cidade'    => 'permit_empty|max_length[100]',
            'telefone'  => 'permit_empty|max_length[20]',
            'imagem'    => 'if_exist|is_image[imagem]|mime_in[imagem,image/jpg,image/jpeg,image/png,image/webp]|max_size[imagem,4096]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to($profileUrl)
                ->withInput()
                ->with('error', implode(', ', $this->validator->getErrors()));
        }

        $post = $this->request->getPost();

        // Se o nome foi enviado, verificar duplicidade
        $userName = trim($post['nome'] ?? '');
        $email    = trim($post['email'] ?? '');

        if (! empty($userName)) {
            $existingUser = $userModel
                ->where('username', $userName)
                ->where('id !=', $user->id)   // ignora o prÃƒÂ³prio usuÃƒÂ¡rio
                ->first();

            if ($existingUser) {
                return redirect()->to($profileUrl)->with('error', 'JÃƒÂ¡ existe um usuÃƒÂ¡rio com esse nome!');
            }
        }

        if (! empty($email) && $email !== ($user->email ?? '')) {
            $existingEmail = db_connect()
                ->table('auth_identities')
                ->where('type', 'email_password')
                ->where('secret', $email)
                ->where('user_id !=', $user->id)
                ->get()
                ->getRow();

            if ($existingEmail) {
                return redirect()->to($profileUrl)->with('error', 'Ja existe um usuario com esse email!');
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
                return redirect()->to($profileUrl)
                    ->withInput()
                    ->with('error', 'Falha ao mover a imagem.');
            }

            // Apagar antiga
            if (! empty($user->img)) {
                $old = FCPATH . $user->img;
                if (is_file($old)) {
                    @unlink($old);
                }
            }

            $filePath = 'assets/img/' . $newName;
        }

        // Dados para atualizar
        $dataProfile = [
            'username' => $userName ?: $user->username,
            'img'      => $filePath,
            'country'  => $post['pais']      ?? $user->country,
            'province' => $post['provincia'] ?? $user->province,
            'city'     => $post['cidade']    ?? $user->city,
            'phone'    => $post['telefone']  ?? $user->phone,
        ];

        // ---------------------------------------
        //    >>> ALTERACAO DE SENHA (SHIELD) <<<
        // ---------------------------------------
        $currentPassword = $post['password_actual'] ?? '';
        $newPassword     = $post['new_password'] ?? '';
        $confirmPassword = $post['confirm_password'] ?? '';
        $emailChanged    = ! empty($email) && $email !== ($user->email ?? '');

        if ($emailChanged) {
            $identity = db_connect()
                ->table('auth_identities')
                ->where('user_id', $user->id)
                ->where('type', 'email_password')
                ->get()
                ->getRow();

            if (! $identity) {
                return redirect()->to($profileUrl)->with('error', 'Identidade de senha nao encontrada.');
            }

            db_connect()
                ->table('auth_identities')
                ->where('id', $identity->id)
                ->update([
                    'secret'     => $email,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        if ($currentPassword || $newPassword || $confirmPassword) {
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                return redirect()->to($profileUrl)->with('error', 'Para alterar a senha, preencha todos os campos.');
            }

            $identity = db_connect()
                ->table('auth_identities')
                ->where('user_id', $user->id)
                ->where('type', 'email_password')
                ->get()
                ->getRow();

            if (! $identity) {
                return redirect()->to($profileUrl)->with('error', 'Identidade de senha nao encontrada.');
            }

            if (! password_verify($currentPassword, $identity->secret2)) {
                return redirect()->to($profileUrl)->with('error', 'A senha atual fornecida esta incorreta.');
            }

            if (strlen($newPassword) < 6) {
                return redirect()->to($profileUrl)->with('error', 'A nova senha deve ter no minimo 6 caracteres.');
            }

            if ($newPassword !== $confirmPassword) {
                return redirect()->to($profileUrl)->with('error', 'A confirmacao da senha nao coincide.');
            }

            db_connect()
                ->table('auth_identities')
                ->where('id', $identity->id)
                ->update([
                    'secret2'    => password_hash($newPassword, PASSWORD_DEFAULT),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        if (! $users->update($user->id, $dataProfile)) {
            return redirect()->to($profileUrl)
                ->with('error', implode(', ', $users->errors()));
        }

        // Atualiza usuÃƒÂ¡rio logado
        $updated = $users->find($user->id);
        auth()->setUser($updated);

        return redirect()->to($profileUrl)->with('success', 'Perfil atualizado com sucesso.');
    }

    public function approveEnrollment($courseId, $pendingId)
    {
        $actualUser = service('auth')->user();
        $courseId = (int) $courseId;
        $pendingId = (int) $pendingId;

        $db = db_connect();
        helper('text');

        $studentModel     = new \App\Models\StudentModel();
        $enrollmentModel  = new \App\Models\EnrollmentModel();
        $paymentModel     = new \App\Models\PaymentModel();
        $pendingUserModel = new \App\Models\PendingUserModel();
        $users            = new UserModel();

        $isReject = $this->request->getPost('status_payment') === 'Rejeitado';
        if ($isReject) {
            $paymentModel
                ->where('id_user_payment', $pendingId)
                ->where('id_course_payment', $courseId)
                ->set([
                    'status_payment' => 'Rejeitado',
                    'approved_by_payment' => $actualUser->id,
                ])
                ->update();

            $this->auditLogger->write(
                'instructor.payment.rejected',
                'info',
                'Pagamento rejeitado pelo instrutor.',
                [
                    'course_id' => $courseId,
                    'pending_user_id' => $pendingId,
                ]
            );

            if ($this->wantsJson()) {
                return $this->jsonMessage('Pagamento rejeitado.');
            }

            return redirect()->back()->with('success', 'Pagamento rejeitado.');
        }

        // 1. Buscar dados do pending_user
        $pendingUser = $pendingUserModel->find($pendingId);
        if (!$pendingUser) {
            $this->auditLogger->write(
                'instructor.enrollment.pending_user_not_found',
                'warning',
                'Usuario pendente nao encontrado para aprovacao.',
                [
                    'course_id' => $courseId,
                    'pending_user_id' => $pendingId,
                ]
            );

            if ($this->wantsJson()) {
                return $this->jsonMessage('Usuario pendente nao encontrado.', 404);
            }
            return redirect()->back()->with('error', 'UsuÃƒÂ¡rio pendente nÃƒÂ£o encontrado.');
        }

        // 2. Verificar se jÃƒÂ¡ existe um user real com este email
        $existingUser = $users->findByCredentials(['email' => $pendingUser->email]);

        if ($existingUser !== null) {
            // UsuÃƒÂ¡rio jÃƒÂ¡ existe Ã¢â€ â€™ apenas inscrever no curso

            // Buscar o estudante vinculado a este user
            $student = $studentModel->where('id_user_student', $existingUser->id)->first();

            if (!$student) {
                // Se ainda nÃƒÂ£o existe estudante, cria
                $studentId = $studentModel->insert([
                    'id_user_student' => $existingUser->id,
                    'name_student'    => $existingUser->username,
                    'email_student'   => $existingUser->email,
                ]);
            } else {
                $studentId = $student->id_student;
            }

            // Verifica se jÃƒÂ¡ estÃƒÂ¡ inscrito no curso
            $alreadyEnrolled = $enrollmentModel
                ->where('id_student_enrollment', $studentId)
                ->where('id_course_enrollment', $courseId)
                ->first();

            if ($alreadyEnrolled) {
                $this->auditLogger->write(
                    'instructor.enrollment.already_enrolled',
                    'notice',
                    'Aprovacao ignorada porque usuario ja estava inscrito.',
                    [
                        'course_id' => $courseId,
                        'pending_user_id' => $pendingId,
                        'existing_user_id' => (int) $existingUser->id,
                    ]
                );

                if ($this->wantsJson()) {
                    return $this->jsonMessage('Usuario ja inscrito neste curso.', 409);
                }
                return redirect()->back()->with('error', 'UsuÃƒÂ¡rio jÃƒÂ¡ estÃƒÂ¡ inscrito neste curso.');
            }

            // Criar nova inscriÃƒÂ§ÃƒÂ£o
            $enrollmentModel->insert([
                'id_course_enrollment'   => $courseId,
                'id_student_enrollment'  => $existingUser->id,
                'status_enrollment'      => 'ativa',
                'progress_enrollment'    => 0.00,
                'enrolled_at_enrollment' => date('Y-m-d H:i:s'),
            ]);

            // Atualizar pagamento
            $paymentModel
                ->where('id_user_payment', $pendingId)
                ->set([
                    'id_user_payment'     => $existingUser->id,
                    'status_payment'      => 'Aprovado',
                    'approved_by_payment' => $actualUser->id,
                ])
                ->update();

            // Remover pending_user
            $pendingUserModel->delete($pendingId);

            $this->auditLogger->write(
                'instructor.enrollment.approved_existing_user',
                'info',
                'Inscricao aprovada para usuario existente.',
                [
                    'course_id' => $courseId,
                    'pending_user_id' => $pendingId,
                    'existing_user_id' => (int) $existingUser->id,
                ]
            );


            if ($this->wantsJson()) {
                return $this->jsonMessage('Inscricao aprovada para usuario existente.');
            }

            return redirect()->back()->with('success', 'InscriÃƒÂ§ÃƒÂ£o aprovada para usuÃƒÂ¡rio jÃƒÂ¡ existente!');
        }

        // 3. Se o usuÃƒÂ¡rio ainda nÃƒÂ£o existe Ã¢â€ â€™ criar um novo
        $user = new User([
            'username' => $pendingUser->username,
        ]);


        $user->email = $pendingUser->email;
        $tempPassword = random_string('alnum', 12);
        $user->password = $tempPassword;
        $users->save($user);

        $userId = $users->getInsertID();
        $user   = $users->find($userId);

        // 3.1 Criar token de reset de senha
        $token   = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', strtotime('+1 day'));

        $db->table('password_resets')->insert([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => $expires
        ]);

        $link = site_url("reset-password?token={$token}");

        // Enviar email
        $email = \Config\Services::email();
        $email->setTo($user->email);
        $email->setSubject('Crie sua senha e acesse o curso');
        $email->setMessage("
        OlÃƒÂ¡ {$user->username},<br><br>
        Sua matrÃƒÂ­cula foi aprovada! Clique no link abaixo para criar sua senha e acessar o curso:<br><br>
        <a href='{$link}'>Criar minha senha</a>
        ");
        $email->send();

        // 4. Criar estudante vinculado
        $studentId = $studentModel->insert([
            'id_user_student' => $userId,
            'name_student'    => $pendingUser->username,
            'email_student'   => $pendingUser->email,
        ]);

        // 5. Criar inscriÃƒÂ§ÃƒÂ£o
        $result = $enrollmentModel->insert([
            'id_course_enrollment'   => $courseId,
            'id_student_enrollment'  => $userId,
            'status_enrollment'      => 'ativa',
            'progress_enrollment'    => 0.00,
            'enrolled_at_enrollment' => date('Y-m-d H:i:s'),
        ]);

        // 6. Atualizar pagamento
        $paymentModel
            ->where('id_user_payment', $pendingId)
            ->set([
                'id_user_payment'     => $userId,
                'status_payment'      => 'Aprovado',
                'approved_by_payment' => $actualUser->id,
            ])
            ->update();

        // 7. Remover pending_user
        $pendingUserModel->delete($pendingId);

        $this->auditLogger->write(
            'instructor.enrollment.approved_new_user',
            'info',
            'Inscricao aprovada com criacao de usuario.',
            [
                'course_id' => $courseId,
                'pending_user_id' => $pendingId,
                'new_user_id' => (int) $userId,
            ]
        );

        if ($this->wantsJson()) {
            return $this->jsonMessage('Inscricao aprovada e usuario criado com sucesso.');
        }

        return redirect()->back()->with('success', 'InscriÃƒÂ§ÃƒÂ£o aprovada e usuÃƒÂ¡rio criado com sucesso!');
    }
    public function certificate()
    {
        $courseModel = new CourseModel();
        // $course = $courseModel->find($idCourse);
        $certificateModel = new CertificateModel();

        $user = service('auth')->user();

        $certificates = $certificateModel->getForInstructorDashboard($user->id);

        return view('pages/instructor/certificates', [
            'user' => $user,
            'sidebarLinks' => $this->sidebarLinks(),
            'currentUrl' => current_url(),
            'certificates' => $certificates
        ]);
    }
}



