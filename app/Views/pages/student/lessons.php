<?php
// dd($enrollment)
$autoplayFlag = (int) ($_GET['autoplay'] ?? 0);
$auto = $autoplayFlag ? 1 : 0;
helper('text');
$previewMode = (bool) ($previewMode ?? false);
$previewUrlsByLessonId = is_array($previewUrlsByLessonId ?? null) ? $previewUrlsByLessonId : [];
$previewBackUrl = (string) ($previewBackUrl ?? site_url('instructor/dashboard/meus_cursos'));
$isQuiz = ($lesson->type_lesson ?? '') === 'quiz';
$quizQuestions = [];
$quizMinScore = 75;
$quizScore = isset($quizScore) ? (float) $quizScore : null;
$quizAttempted = $isQuiz && $quizScore !== null;
$quizPassed = $quizAttempted && $quizScore >= $quizMinScore;
$currentModule = null;
if (!empty($modules)) {
    foreach ($modules as $mod) {
        if ((int) $mod->id_module === (int) $lesson->id_module_lesson) {
            $currentModule = $mod;
            break;
        }
    }
}
if ($currentModule && isset($currentModule->min_score_module)) {
    $quizMinScore = (int) $currentModule->min_score_module;
}
if ($isQuiz && $quizScore !== null) {
    $quizPassed = $quizScore >= $quizMinScore;
}
if ($isQuiz && !empty($lesson->content_lesson)) {
    $decodedQuiz = json_decode($lesson->content_lesson, true);
    if (is_array($decodedQuiz) && !empty($decodedQuiz['questions'])) {
        $quizQuestions = $decodedQuiz['questions'];
    }
}

foreach ($quizQuestions as $qIndex => $q) {
    if (!is_array($q)) {
        $quizQuestions[$qIndex] = [
            'question' => (string) $q,
            'options' => ['', '', '', ''],
            'correct' => 0,
        ];
        continue;
    }

    if (!isset($q['options'])) {
        $quizQuestions[$qIndex]['options'] = ['', '', '', ''];
        $quizQuestions[$qIndex]['correct'] = 0;
    }
}

$resolveLessonUrl = static function (?int $lessonId, ?string $lessonSlug = null) use ($previewMode, $previewUrlsByLessonId, $courseSlug) {
    $lessonId = (int) $lessonId;

    if ($lessonId <= 0) {
        return '';
    }

    if ($previewMode) {
        return (string) ($previewUrlsByLessonId[$lessonId] ?? '');
    }

    if (!empty($courseSlug) && !empty($lessonSlug)) {
        return site_url('student/dashboard/inscricoes/' . $courseSlug . '/' . $lessonSlug);
    }

    return site_url('student/dashboard/ver_aulas/' . $lessonId);
};

$backToCoursesUrl = $previewMode ? $previewBackUrl : site_url('student/dashboard/inscricoes');
$backToCoursesLabel = $previewMode ? 'Voltar ao editor' : 'Voltar aos Cursos';
$certificateDashboardUrl = $previewMode ? $previewBackUrl : site_url('student/dashboard/certificados');
$pendingCertificateUrlValue = $previewMode ? '' : site_url('student/certificates/pending');
$certificateDownloadUrl = $previewMode
    ? $previewBackUrl
    : site_url('certificados/download/' . (int) ($enrollment->id_enrollment ?? 0));
$isLastInModule = false;
if ($currentModule && !empty($currentModule->lessons)) {
    $lastIndex = count($currentModule->lessons) - 1;
    if ($lastIndex >= 0) {
        $lastLesson = $currentModule->lessons[$lastIndex];
        $isLastInModule = (int) $lastLesson->id_lesson === (int) $lesson->id_lesson;
    }
}
$nextModuleUrl = !empty($nextModuleLessonId)
    ? $resolveLessonUrl((int) $nextModuleLessonId, $nextModuleLessonSlug ?? null)
    : '';
?>

<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Assistir<?= $this->endSection() ?>

<?= $this->section('lessons') ?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<!-- Vimeo Player -->
<script src="https://player.vimeo.com/api/player.js"></script>

<!-- CSRF para AJAX -->
<meta name="csrf-name" content="<?= csrf_token() ?>">
<meta name="csrf-hash" content="<?= csrf_hash() ?>">

<style>
    .blocked-access #lesson-content,
    .blocked-access .lesson-row,
    .blocked-access .module-header,
    .blocked-access #nextLessonBtn,
    .blocked-access #drawerToggle,
    .blocked-access #closeDrawer,
    .blocked-access #lesson-content a,
    .blocked-access #lesson-content button,
    .blocked-access #lesson-content input,
    .blocked-access #lesson-content select,
    .blocked-access #lesson-content textarea {
        pointer-events: none;
        opacity: 0.65;
    }

  .blocked-access #lesson-content iframe {
        pointer-events: none;
    }
    .quiz-option.selected {
        background-color: #dbf4ff;
        border-color: #3b82f6;
        color: #1d4ed8;
    }
    .dark .quiz-option.selected {
        background-color: #1d3557;
        border-color: #3b82f6;
        color: #bfdbfe;
    }

    .quiz-failure-overlay {
        position: fixed;
        inset: 0;
        z-index: 60;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.75);
    }

    .quiz-failure-card {
        background: #0f172a;
        border-radius: 24px;
        padding: 2rem;
        color: #fff;
        max-width: 480px;
        width: min(95vw, 480px);
        text-align: center;
        box-shadow: 0 20px 45px rgba(8, 10, 20, 0.65);
    }

    .quiz-failure-overlay.hidden {
        display: none;
    }

    .quiz-failure-card button {
        background: #2563eb;
        color: #fff;
        border: none;
        padding: 0.85rem 1.5rem;
        border-radius: 999px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .quiz-failure-card button:hover {
        background: #1d4ed8;
    }
</style>

<?php
$enrollmentStatus = strtolower((string) ($enrollment->status_enrollment ?? ''));
$accessBlocked = ($accessBlocked ?? false) || ($enrollmentStatus === 'cancelada');
?>
<div class="min-h-screen text-gray-900 dark:text-gray-100 transition-colors duration-300 <?= ($accessBlocked || (bool) session('blocked_access')) ? 'blocked-access' : '' ?>">
    <div class="container mx-auto" data-enrollment-id="<?= (int)($enrollment->id_enrollment) ?>" data-enrollment-status="<?= esc($enrollmentStatus) ?>">

        <?php if ($accessBlocked || (bool) session('blocked_access')) : ?>
            <div id="blockedAccessModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md mx-4 shadow-xl">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="bi bi-lock-fill text-2xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Acesso bloqueado</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm">
                            Você foi bloqueado pelo instrutor do curso. Entre em contato com o instrutor para mais detalhes.
                        </p>
                        <div class="flex justify-center">
                            <button id="blockedOkBtn" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm" onclick="window.location.href=document.referrer || '<?= site_url('student/dashboard/meus_cursos') ?>'">
                                OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.body.style.overflow = 'hidden';
                const blockedBtn = document.getElementById('blockedOkBtn');
                const blockedModal = document.getElementById('blockedAccessModal');
                const fallbackUrl = <?= json_encode(site_url('student/dashboard/meus_cursos')) ?>;
                const goBack = () => {
                    if (document.referrer) {
                        window.location.href = document.referrer;
                        return;
                    }
                    window.location.href = fallbackUrl;
                };
                blockedBtn?.addEventListener('click', goBack);
                blockedModal?.addEventListener('click', (e) => {
                    if (e.target === blockedModal) goBack();
                });
            </script>
        <?php endif; ?>

        <?php if ($previewMode): ?>
            <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-4 text-sm text-blue-900 shadow-sm dark:border-blue-800 dark:bg-blue-900/20 dark:text-slate-100">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="font-semibold">Modo de pré-visualização do instrutor</div>
                        <div class="text-blue-800/80 dark:text-blue-100/80">
                            Esta tela reaproveita a página de assistir aulas. Navegação, progresso e quiz aqui são apenas simulados.
                        </div>
                    </div>
                    <a href="<?= esc($previewBackUrl) ?>" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition-colors hover:bg-blue-700">
                        <i class="bi bi-arrow-left"></i>
                        Voltar ao editor
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm mb-6">
            <a href="<?= esc($backToCoursesUrl) ?>" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors flex items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                <?= esc($backToCoursesLabel) ?>
            </a>
            <span class="text-gray-400 dark:text-gray-600">/</span>
            <span class="font-medium text-gray-700 dark:text-gray-300 text-sm truncate max-w-xs md:max-w-md"><?= esc($course->title_course) ?></span>
        </nav>

        <!-- Progress Bar -->
        <?php
        $completedLessonIds = $completedLessonIds ?? [];
        // Calcular progresso inicial
        $totalLessons = 0;
        $completedLessons = 0;
        foreach ($modules as $m) {
            $totalLessons += count($m->lessons);
            foreach ($m->lessons as $l) {
                if (in_array($l->id_lesson, $completedLessonIds)) {
                    $completedLessons++;
                }
            }
        }
        $initialProgress = $totalLessons ? round(($completedLessons / $totalLessons) * 100) : 0;

        $autoSuffix = ($accessBlocked || ((int) $initialProgress === 100) || !$autoplayFlag)
            ? ''
            : '?autoplay=1';
        ?>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-6 shadow-sm">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-700 dark:text-gray-300 text-sm font-medium">Progresso do Curso</span>
                <span id="progressPercentage" class="font-bold text-blue-600 dark:text-blue-400 text-sm"><?= $initialProgress ?>%</span>
            </div>
            <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div id="progressBar" class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-500" style="width: <?= $initialProgress ?>%"></div>
            </div>
        </div>

        <!-- Mobile Drawer Button -->
        <div class="lg:hidden mb-4">
            <button id="drawerToggle" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2 text-sm shadow-md">
                <i class="bi bi-list"></i>
                Ver Conteúdo do Curso
            </button>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Video Section -->
            <?php
            $nextUrlAttr = $nextLesson ? $resolveLessonUrl((int) $nextLesson, $nextLessonSlug ?? null) : '';
            $prevUrlAttr = $prevLesson ? $resolveLessonUrl((int) $prevLesson, $prevLessonSlug ?? null) : '';
            ?>
            <div id="lesson-content"
                data-lesson-id="<?= (int) $lesson->id_lesson ?>"
                data-next-url="<?= esc($nextUrlAttr) ?>"
                data-prev-url="<?= esc($prevUrlAttr) ?>"
                data-is-quiz="<?= $isQuiz ? '1' : '0' ?>"
                data-quiz-attempted="<?= $quizAttempted ? '1' : '0' ?>"
                data-quiz-passed="<?= $quizPassed ? '1' : '0' ?>"
                data-quiz-score="<?= $quizScore !== null ? esc($quizScore) : '' ?>"
                data-is-last-module="<?= $isLastInModule ? '1' : '0' ?>"
                data-next-module-url="<?= esc($nextModuleUrl) ?>"
                class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-md">
                    <?php if (!$isQuiz): ?>
                        <!-- Video Player -->
                        <div class="relative pt-[56.25%] bg-black rounded-t-xl overflow-hidden">
                            <?php
                            function getVimeoId($url)
                            {
                                preg_match('/vimeo\.com\/(?:video\/)?([0-9]+)/', $url, $m);
                                return $m[1] ?? null;
                            }
                            $videoId = getVimeoId($lesson->video_url_lesson);
                            ?>
                            <?php if ($videoId): ?>
                                <iframe id="vimeoPlayer"
                                    src="https://player.vimeo.com/video/<?= esc($videoId) ?>?badge=0&autopause=0&player_id=<?= esc($lesson->id_lesson) ?>&app_id=58479&title=0&byline=0&portrait=0&autoplay=<?= $auto ?>"
                                    allow="autoplay; fullscreen; picture-in-picture"
                                    allowfullscreen referrerpolicy="no-referrer" loading="lazy"
                                    sandbox="allow-same-origin allow-scripts allow-presentation"
                                    class="absolute inset-0 w-full h-full"
                                    oncontextmenu="return false">
                                </iframe>
                            <?php else: ?>
                                <div class="absolute inset-0 flex items-center justify-center text-white">
                                    <div class="text-center p-4">
                                        <i class="bi bi-exclamation-triangle text-3xl mb-3 text-yellow-400"></i>
                                        <p class="text-lg font-medium">Link de vídeo inválido</p>
                                        <p class="text-sm text-gray-300 mt-1">Entre em contato com o suporte técnico</p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- End Overlay -->
                            <?php if ($nextLesson): ?>
                                <div id="endOverlay" class="absolute inset-0 hidden items-center justify-center backdrop-blur-sm bg-black bg-opacity-70 z-10">
                                    <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl p-6 max-w-md w-[90%] text-center shadow-xl">
                                        <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="bi bi-check-lg text-2xl text-green-600 dark:text-green-400"></i>
                                        </div>
                                        <h4 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">Aula concluída</h4>
                                        <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm">Avance para a próxima aula quando quiser.</p>
                                        <div class="flex flex-col sm:flex-row gap-3 justify-center mb-4">
                                            <?php
                                            $nextUrl = $resolveLessonUrl((int) $nextLesson, $nextLessonSlug ?? null);
                                            ?>
                                            <a id="goNextBtn"
                                                <?php
                                                $autoSuffix = ($accessBlocked || ((int)($initialProgress ?? 0) === 100) || !$autoplayFlag)
                                                    ? ''
                                                    : '?autoplay=1';
                                                ?>
                                                href="<?= $nextUrl ?><?= $autoSuffix ?>"
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm flex items-center justify-center gap-2">
                                                Próxima Aula
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                            <button id="stayBtn" type="button" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm">
                                                Ficar aqui
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Lesson Info -->
                    <div class="p-5">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3"><?= esc($lesson->title_lesson) ?></h2>
                        <div class="flex flex-wrap gap-4 text-gray-600 dark:text-gray-400 text-sm mb-4">
                            <div class="flex items-center gap-1.5">
                                <i class="bi bi-stopwatch text-blue-500"></i>
                                <span><?= esc($lesson->duration_lesson) ?> minutos</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="bi bi-calendar3 text-blue-500"></i>
                                <span><?= date('d/m/Y', strtotime($lesson->created_at)) ?></span>
                            </div>
                        </div>
                        <?php if (!$isQuiz): ?>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-sm"><?= esc($lesson->content_lesson) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($lesson->attachment_path_lesson)): ?>
                            <div class="mt-4">
                                <a href="<?= esc(base_url('assets/instructor/lesson_files/' . $lesson->attachment_path_lesson)) ?>"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium rounded-lg transition-colors"
                                    download>
                                    <i class="bi bi-paperclip"></i>
                                    <?= esc($lesson->attachment_name_lesson ?? 'Baixar anexo') ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($isQuiz): ?>
                        <div class="p-5 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                    Quiz
                                </h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    Nota mínima: <?= (int) $quizMinScore ?>%
                                </span>
                            </div>

                            <?php if ($quizAttempted): ?>
                                <div class="mb-4 rounded-lg border border-emerald-200 dark:border-emerald-700 bg-emerald-50/80 dark:bg-emerald-900/30 px-3 py-2 text-sm text-emerald-700 dark:text-emerald-200">
                                    Você já realizou este quiz e obteve <strong><?= (int) $quizScore ?>%</strong>.
                                </div>
                            <?php endif; ?>

                            <?php if (empty($quizQuestions)): ?>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    Este quiz ainda não tem perguntas cadastradas.
                                </p>
                            <?php else: ?>
                                <div id="quizStepper" class="space-y-4">
                                    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm space-y-4">
                                            <div class="flex flex-wrap items-center justify-between gap-2 text-xs uppercase tracking-wider text-slate-500">
                                                <span id="quizStepLabel">Pergunta 1 de <?= count($quizQuestions) ?></span>
                                                <span id="quizProgressPercent">0%</span>
                                            </div>
                                        <div class="h-1 w-full bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden">
                                            <div id="quizProgressBar" class="h-full bg-blue-600 rounded-full w-0 transition-all duration-300"></div>
                                        </div>
                                        <div id="quizOptions" class="space-y-3">
                                            <?php foreach ($quizQuestions as $qIndex => $question): ?>
                                                <div class="quiz-question-block hidden space-y-3" data-question-index="<?= $qIndex ?>">
                                                    <p class="text-lg font-semibold text-slate-900 dark:text-white">
                                                        <?= esc($question['question'] ?? '') ?>
                                                    </p>
                                                    <div class="grid gap-3">
                                                        <?php for ($opt = 0; $opt < 4; $opt++): ?>
                                                            <button type="button"
                                                                class="quiz-option w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-4 py-3 text-left text-sm font-medium text-slate-900 dark:text-slate-100 hover:border-blue-500 transition-colors"
                                                                data-option-index="<?= $opt ?>">
                                                                <?= esc($question['options'][$opt] ?? '') ?>
                                                            </button>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="text-sm text-slate-500 dark:text-slate-400" id="quizHint">Selecione uma resposta para continuar.</div>
                                        <div class="flex gap-3">
                                            <button id="quizPrevBtn" type="button" class="flex-1 px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-600 dark:text-slate-200 disabled:opacity-50 disabled:cursor-not-allowed" disabled>Anterior</button>
                                            <button id="quizNextBtn" type="button" class="flex-1 px-4 py-3 rounded-lg text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:bg-slate-400 disabled:cursor-not-allowed" disabled>Próxima pergunta</button>
                                        </div>
                                    </div>
                                    <div id="quizSummary" class="hidden bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-700 rounded-2xl p-5 shadow-sm space-y-3">
                                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Resultado do quiz</h3>
                                        <p id="quizSummaryScore" class="text-3xl font-extrabold text-blue-600"></p>
                                        <p id="quizSummaryNote" class="text-sm text-slate-500 dark:text-slate-400"></p>
                                        <div id="quizSummaryWrongList" class="space-y-3"></div>
                                        <div class="flex justify-end">
                                            <button id="quizRetryBtn" type="button" class="hidden text-sm font-semibold text-blue-600 hover:text-blue-700">Refazer quiz</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="quizFailDialog" class="hidden quiz-failure-overlay">
                                    <div class="quiz-failure-card space-y-4">
                                        <p class="text-xs uppercase tracking-wider text-slate-300">Quiz Finalizado</p>
                                        <p id="quizFailScore" class="text-5xl font-extrabold text-red-400">0%</p>
                                        <p id="quizFailDetail" class="text-sm text-slate-300">Você acertou 0 de 0 questões</p>
                                        <p class="text-sm text-red-200">
                                            Você não alcançou a nota mínima de <strong id="quizFailMinScore">0%</strong>.
                                            Estude um pouco mais e tente novamente.
                                        </p>
                                        <button id="quizFailRetryBtn" type="button">Voltar para a primeira pergunta</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between items-center">
                    <?php if ($prevLesson): ?>
                        <?php $prevUrl = $resolveLessonUrl((int) $prevLesson, $prevLessonSlug ?? null); ?>
                        <a href="<?= $prevUrl ?><?= $autoSuffix ?>"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-5 rounded-lg transition-colors flex items-center gap-2 text-sm shadow-sm">
                            <i class="bi bi-arrow-left"></i>
                            Aula Anterior
                        </a>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>

                    <?php if ($nextLesson): ?>
                        <button id="nextLessonBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-5 rounded-lg transition-colors flex items-center gap-2 text-sm shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
                            <?= ($isQuiz && ! $quizAttempted) ? 'disabled' : '' ?>>
                            <span class="next-lesson-label"><?= $isLastInModule ? 'Próximo Módulo' : 'Próxima Aula' ?></span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar - Desktop -->
            <div class="hidden lg:block space-y-4">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Conteúdo do Curso</h3>

                    <!-- Course Sidebar Content -->
                    <div class="space-y-3 max-h-[calc(100vh-200px)] overflow-y-auto pr-2">
                        <?php foreach ($modules as $index => $m): ?>
                            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                <!-- Module Header -->
                                <button class="module-header w-full flex justify-between items-center p-3 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                    onclick="toggleModule(<?= $index ?>)">
                                    <div class="flex items-center gap-3">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        <span class="font-medium text-gray-900 dark:text-white text-left text-sm"><?= esc($m->title_module) ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-600 dark:text-gray-400 text-xs"><?= count($m->lessons) ?> aulas</span>
                                        <i class="bi bi-chevron-down text-gray-500 text-xs transition-transform duration-300"></i>
                                    </div>
                                </button>

                                <!-- Module Content -->
                                <div id="module-<?= $index ?>" class="module-content hidden">
                                    <?php foreach ($m->lessons as $l): ?>
                                        <?php $isCurrent = ($l->id_lesson == $lesson->id_lesson); ?>
                                        <?php $isDone = in_array($l->id_lesson, $completedLessonIds ?? [], true); ?>
                                        <?php $isQuizLessonRow = ($l->type_lesson === 'quiz'); ?>
                                        <div class="lesson-row flex items-center justify-between p-3 border-t border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors <?= $isCurrent ? 'bg-blue-50 dark:bg-blue-900/30 border-l-2 border-blue-500' : '' ?>"
                                            data-lesson-id="<?= (int)$l->id_lesson ?>"
                                            data-lesson-type="<?= esc($l->type_lesson) ?>">

                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="lesson-check w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                        <?= $isDone ? 'checked' : '' ?>
                                                        aria-label="Marcar aula como conclu?da"
                                                        <?= $isQuizLessonRow ? 'disabled title="A conclusão deste quiz é controlada pelas respostas."' : '' ?>>
                                                </div>

                                                <?php $lessonSlug = url_title($l->title_lesson, '-', true); ?>
                                                <?php
                                                $lessonSlug = $lessonSlugById[(int) $l->id_lesson] ?? '';
                                                $iconClass = in_array($l->type_lesson, ['quiz', 'text'], true) ? 'bi-file-text' : 'bi-camera-video';
                                                ?>
                                                <?php $lessonUrl = $resolveLessonUrl((int) $l->id_lesson, $lessonSlug ?: null); ?>
                                                <a class="lesson-link flex items-center gap-2 text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex-1 min-w-0"
                                                    href="<?= $lessonUrl ?>">
                                                    <i class="bi <?= $iconClass ?> text-slate-400"></i>
                                                    <span class="truncate text-sm"><?= esc($l->title_lesson) ?></span>
                                                    <?php if (!empty($l->attachment_path_lesson)): ?>
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-200 text-slate-700 dark:bg-slate-600 dark:text-slate-100 rounded-full text-[10px] font-medium">
                                                            <i class="bi bi-paperclip"></i>
                                                            Arquivo
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($isCurrent): ?>
                                                        <span class="badge-current font-medium px-2 py-0.5 bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-full whitespace-nowrap text-xs">
                                                            Atual
                                                        </span>
                                                    <?php endif; ?>
                                                </a>
                                            </div>

                                            <span class="text-gray-500 dark:text-gray-400 ml-2 whitespace-nowrap text-xs">
                                                <?= esc($l->duration_lesson) ?> min
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div id="mobileDrawer" class="fixed inset-y-0 right-0 w-full max-w-sm bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 transform translate-x-full transition-transform duration-300 z-50 lg:hidden shadow-xl">
            <div class="p-4 h-full flex flex-col">
                <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Conteúdo do Curso</h3>
                    <button id="closeDrawer" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="bi bi-x-lg text-lg"></i>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto pb-4">
                    <!-- Course Sidebar Content for Mobile -->
                    <div class="space-y-3">
                        <?php foreach ($modules as $index => $m): ?>
                            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                <!-- Module Header -->
                                <button class="module-header w-full flex justify-between items-center p-3 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                    onclick="toggleModuleMobile(<?= $index ?>)">
                                    <div class="flex items-center gap-3">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        <span class="font-medium text-gray-900 dark:text-white text-left text-sm"><?= esc($m->title_module) ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-600 dark:text-gray-400 text-xs"><?= count($m->lessons) ?> aulas</span>
                                        <i class="bi bi-chevron-down text-gray-500 text-xs transition-transform duration-300"></i>
                                    </div>
                                </button>

                                <!-- Module Content -->
                                <div id="module-mobile-<?= $index ?>" class="module-content-mobile hidden">
                                    <?php foreach ($m->lessons as $l): ?>
                                        <?php $isCurrent = ($l->id_lesson == $lesson->id_lesson); ?>
                                        <?php $isDone = in_array($l->id_lesson, $completedLessonIds ?? [], true); ?>
                                        <?php $isQuizLessonRow = ($l->type_lesson === 'quiz'); ?>
                                        <div class="lesson-row flex items-center justify-between p-3 border-t border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors <?= $isCurrent ? 'bg-blue-50 dark:bg-blue-900/30 border-l-2 border-blue-500' : '' ?>"
                                            data-lesson-id="<?= (int)$l->id_lesson ?>"
                                            data-lesson-type="<?= esc($l->type_lesson) ?>">

                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="lesson-check w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                        <?= $isDone ? 'checked' : '' ?>
                                                        aria-label="Marcar aula como concluída"
                                                        <?= $isQuizLessonRow ? 'disabled title="A conclusão deste quiz é controlada pelas respostas."' : '' ?>>
                                                </div>

                                                <?php
                                                $lessonSlug = $lessonSlugById[(int) $l->id_lesson] ?? '';
                                                $iconClass = in_array($l->type_lesson, ['quiz', 'text'], true) ? 'bi-file-text' : 'bi-camera-video';
                                                ?>
                                                <?php $lessonUrl = $resolveLessonUrl((int) $l->id_lesson, $lessonSlug ?: null); ?>
                                                <a class="lesson-link flex items-center gap-2 text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex-1 min-w-0"
                                                    href="<?= $lessonUrl ?>" onclick="closeDrawerFunc()">
                                                    <i class="bi <?= $iconClass ?> text-slate-400"></i>
                                                    <span class="truncate text-sm"><?= esc($l->title_lesson) ?></span>
                                                    <?php if (!empty($l->attachment_path_lesson)): ?>
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-200 text-slate-700 dark:bg-slate-600 dark:text-slate-100 rounded-full text-[10px] font-medium">
                                                            <i class="bi bi-paperclip"></i>
                                                            Arquivo
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($isCurrent): ?>
                                                        <span class="badge-current font-medium px-2 py-0.5 bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-full whitespace-nowrap text-xs">
                                                            Atual
                                                        </span>
                                                    <?php endif; ?>
                                                </a>
                                            </div>

                                            <span class="text-gray-500 dark:text-gray-400 ml-2 whitespace-nowrap text-xs">
                                                <?= esc($l->duration_lesson) ?> min
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="drawerBackdrop" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40 lg:hidden"></div>
    </div>
</div>

<script>
    /* =========================
       CSRF & Progress Management
       ========================= */
    const previewMode = <?= $previewMode ? 'true' : 'false' ?>;
    const previewBackUrl = <?= json_encode($previewBackUrl, JSON_UNESCAPED_SLASHES) ?>;
    const csrfName = document.querySelector('meta[name="csrf-name"]')?.content;
    let csrfHash = document.querySelector('meta[name="csrf-hash"]')?.content;
    const enrollmentId = document.querySelector('.container')?.dataset?.enrollmentId;
    const pendingCertificateUrl = <?= json_encode($pendingCertificateUrlValue, JSON_UNESCAPED_SLASHES) ?>;
    let certificateInfo = <?= json_encode(array_merge(
                                [
                                    'completedAt' => null,
                                    'availableAt' => null,
                                    'pdfReady' => false,
                                ],
                                $certificateInfo ?? []
                            ), JSON_UNESCAPED_SLASHES) ?>;
    certificateInfo.downloadUrl = <?= json_encode($certificateDownloadUrl, JSON_UNESCAPED_SLASHES) ?>;

    // Track video progress
    let currentVideoProgress = 0;
    let hasReached95Percent = <?= in_array($lesson->id_lesson, $completedLessonIds) ? 'true' : 'false' ?>;

    // Estado do progresso vindo do backend (INITIAL)
    const totalLessons = <?= (int)$totalLessons ?>;
    let completedLessons = <?= (int)$completedLessons ?>;
    let courseProgress = <?= (int)$initialProgress ?>;

    let courseModalShown = false;
    let pendingCertificateRequested = false;

    function setProgressUI(pct) {
        courseProgress = pct;

        const ppEl = document.getElementById('progressPercentage');
        const barEl = document.getElementById('progressBar');

        if (barEl) barEl.style.width = pct + '%';

        if (!ppEl || !barEl) return;

        if (pct >= 100) {
            // Texto "Concluído"
            ppEl.innerHTML = `
      <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400 font-bold">
        <i class="bi bi-check-circle"></i> Concluído
      </span>
    `;

            // Muda a barra para verde (remove o gradiente azul)
            barEl.classList.remove('from-blue-500', 'to-blue-600');
            barEl.classList.add('from-green-500', 'to-green-600');
        } else {
            // Volta ao normal (percentual)
            ppEl.textContent = pct + '%';

            // Garante gradiente azul quando n?o est? conclu?do
            barEl.classList.remove('from-green-500', 'to-green-600');
            barEl.classList.add('from-blue-500', 'to-blue-600');
        }
    }

    function parseIsoDate(value) {
        if (!value) return null;
        const d = new Date(value);
        return Number.isNaN(d.getTime()) ? null : d;
    }

    function showCourseCompletedModal(overrides = {}) {
        if (courseModalShown) return;
        courseModalShown = true;

        document.body.style.overflow = 'hidden';

        certificateInfo = {
            ...certificateInfo,
            ...overrides
        };

        const isReady = Boolean(certificateInfo.pdfReady);
        let message = isReady ?
            'O seu certificado em PDF já foi gerado e está disponível.' :
            'Seu certificado foi registrado e aparecerá em Meus Certificados em instantes.';
        if (previewMode) {
            message = 'Esta e apenas uma pre-visualizacao. Nenhum progresso ou certificado real foi gerado.';
        }
        const actionHref = previewMode ? previewBackUrl : (isReady ? certificateInfo.downloadUrl : "<?= $certificateDashboardUrl ?>");
        const actionLabel = previewMode ? 'Voltar ao editor' : (isReady ? 'Ver PDF' : 'Ver Certificados');
        const actionColorClass = previewMode ? 'bg-slate-700 hover:bg-slate-800' : (isReady ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-600 hover:bg-gray-700');

        const modal = `
            <div id="courseCompletedModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 max-w-md w-[90%] text-center shadow-xl">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="bi bi-trophy text-3xl text-green-600 dark:text-green-400"></i>
                </div>

                <h4 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">Parabéns!</h4>

                <p class="text-gray-600 dark:text-gray-300 mb-2 text-sm">
                Você concluiu 100% do curso.
                </p>

                <p id="courseCompletedMessage" class="text-gray-600 dark:text-gray-300 mb-5 text-sm">
                ${message}
                </p>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button id="certOkBtn"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm">
                    OK
                </button>

                <a id="courseCompletedAction" href="${actionHref}" class="${actionColorClass} text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm text-center">
                    ${actionLabel}
                </a>
                </div>
            </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modal);
        document.getElementById('certOkBtn').addEventListener('click', () => {
            const m = document.getElementById('courseCompletedModal');
            if (m) m.remove();
            document.body.style.overflow = '';
        });
        refreshCourseCompletedModal();
    }

    function refreshCourseCompletedModal() {
        const modal = document.getElementById('courseCompletedModal');
        if (!modal) return;

        const messageEl = modal.querySelector('#courseCompletedMessage');
        const actionLink = modal.querySelector('#courseCompletedAction');
        const isReady = Boolean(certificateInfo.pdfReady);
        if (messageEl) {
            let newMessage = isReady ?
                'O seu certificado em PDF já foi gerado e está disponível.' :
                'Seu certificado foi registrado e aparecerá em Meus Certificados em instantes.';
            if (previewMode) {
                newMessage = 'Esta e apenas uma pre-visualizacao. Nenhum progresso ou certificado real foi gerado.';
            }
            if (messageEl.textContent !== newMessage) {
                messageEl.textContent = newMessage;
            }
        }

        if (actionLink) {
            const readyHref = certificateInfo.downloadUrl;
            const pendingHref = "<?= $certificateDashboardUrl ?>";
            if (previewMode) {
                actionLink.href = previewBackUrl;
                actionLink.textContent = 'Voltar ao editor';
                actionLink.classList.remove('bg-green-600', 'hover:bg-green-700', 'bg-gray-600', 'hover:bg-gray-700');
                actionLink.classList.add('bg-slate-700', 'hover:bg-slate-800');
            } else {
                actionLink.href = isReady ? readyHref : pendingHref;
                actionLink.textContent = isReady ? 'Ver PDF' : 'Ver Certificados';
                actionLink.classList.remove('bg-slate-700', 'hover:bg-slate-800');
                if (isReady) {
                    actionLink.classList.remove('bg-gray-600', 'hover:bg-gray-700');
                    actionLink.classList.add('bg-green-600', 'hover:bg-green-700');
                } else {
                    actionLink.classList.remove('bg-green-600', 'hover:bg-green-700');
                    actionLink.classList.add('bg-gray-600', 'hover:bg-gray-700');
                }
            }
        }
    }

    function handleCourseCompletion(meta = {}) {
        certificateInfo = {
            ...certificateInfo,
            completedAt: meta.completedAt ?? certificateInfo.completedAt,
            availableAt: meta.availableAt ?? certificateInfo.availableAt,
            pdfReady: typeof meta.pdfReady !== 'undefined' ? meta.pdfReady : certificateInfo.pdfReady,
        };

        if (!previewMode) {
            requestPendingCertificate();
        }
        showCourseCompletedModal({
            completedAt: certificateInfo.completedAt,
            availableAt: certificateInfo.availableAt,
        });
        refreshCourseCompletedModal();
    }


    async function requestPendingCertificate() {
        if (pendingCertificateRequested) return;
        if (previewMode) return;
        if (!enrollmentId) return;

        pendingCertificateRequested = true;

        try {
            const data = await fetchJSON(pendingCertificateUrl, {
                method: 'POST',
                body: JSON.stringify({
                    enrollment_id: Number(enrollmentId)
                })
            });

            if (!data?.ok) {
                pendingCertificateRequested = false;
                console.warn(data?.message || 'Não foi possível emitir o certificado pendente.');
            }

            if (data?.available_at) {
                certificateInfo.availableAt = data.available_at;
            }
            if (data?.completed_at) {
                certificateInfo.completedAt = data.completed_at;
            }
            if (typeof data?.pdf_ready !== 'undefined') {
                certificateInfo.pdfReady = Boolean(data.pdf_ready);
            }
            refreshCourseCompletedModal();
        } catch (e) {
            pendingCertificateRequested = false;
            console.warn('Falha ao emitir o certificado pendente:', e);
        }
    }

    function recomputeFromCounters() {
        const pct = totalLessons ? Math.round((completedLessons / totalLessons) * 100) : 0;
        setProgressUI(pct);
        return pct;
    }

    async function fetchJSON(url, opts = {}) {
        const cfg = {
            method: 'GET',
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            ...opts
        };

        if (!cfg.headers) cfg.headers = {};
        if (csrfName && csrfHash && cfg.method !== 'GET') {
            cfg.headers['Content-Type'] = 'application/json';
            cfg.headers[csrfName] = csrfHash;
        }

        const res = await fetch(url, cfg);
        const newHash = res.headers.get('X-CSRF-Hash');
        if (newHash) csrfHash = newHash;
        return res.json();
    }

    function getLessonProgressSnapshot() {
        const lessonIds = new Set();
        const completedIds = new Set();

        document.querySelectorAll('.lesson-row[data-lesson-id]').forEach((row) => {
            const lessonId = Number(row.dataset.lessonId || 0);
            if (!lessonId) return;

            lessonIds.add(lessonId);

            const checkbox = row.querySelector('.lesson-check');
            if (checkbox?.checked) {
                completedIds.add(lessonId);
            }
        });

        return {
            total: lessonIds.size,
            done: completedIds.size
        };
    }

    function computeProgress() {
        const snapshot = getLessonProgressSnapshot();
        const total = snapshot.total || totalLessons;
        const done = snapshot.done;
        const pct = total ? Math.round((done / total) * 100) : 0;
        const ppEl = document.getElementById('progressPercentage');
        const barEl = document.getElementById('progressBar');
        if (ppEl) ppEl.textContent = pct + '%';
        if (barEl) barEl.style.width = pct + '%';
        return pct;
    }

    function syncLessonCheckboxState(lessonId, isChecked) {
        document.querySelectorAll(`.lesson-row[data-lesson-id="${Number(lessonId)}"] .lesson-check`).forEach((checkbox) => {
            checkbox.checked = isChecked;
        });
    }

    async function toggleLessonComplete(lessonId, isChecked, checkboxEl) {
        if (accessBlocked) {
            showBlockedAccessModal();
            if (checkboxEl) checkboxEl.checked = !isChecked;
            return;
        }
        if (!enrollmentId) {
            alert('Matr&iacute;cula n&atilde;o identificada. Recarregue a p&aacute;gina.');
            if (checkboxEl) checkboxEl.checked = !isChecked;
            return;
        }

        const lessonRow = checkboxEl?.closest('.lesson-row');
        const lessonType = lessonRow?.dataset?.lessonType;
        if (lessonType === 'quiz') {
            if (checkboxEl) {
                checkboxEl.checked = false;
                checkboxEl.disabled = true;
            }
            alert('Este quiz só é marcado como concluído quando você alcançar a nota mínima respondendo às perguntas.');
            return;
        }

        if (previewMode) {
            syncLessonCheckboxState(lessonId, isChecked);
            completedLessons = getLessonProgressSnapshot().done;
            const pct = recomputeFromCounters();
            if (pct === 100) {
                handleCourseCompletion({
                    pdfReady: false
                });
            }
            return;
        }

        if (checkboxEl) checkboxEl.disabled = true;

        try {
            const url = isChecked ?
                '<?= site_url('student/lessons/complete') ?>' :
                '<?= site_url('student/lessons/uncomplete') ?>';

            const data = await fetchJSON(url, {
                method: 'POST',
                body: JSON.stringify({
                    lesson_id: Number(lessonId),
                    enrollment_id: Number(enrollmentId)
                })
            });

            if (!data?.ok) {
                if (checkboxEl) checkboxEl.checked = !isChecked;
                alert(data?.message || 'Não foi possível atualizar a conclusão.');
                return;
            }

            // Atualiza progresso SEM computeProgress()
            if (isChecked) {
                completedLessons = Math.min(totalLessons, completedLessons + 1);
            } else {
                completedLessons = Math.max(0, completedLessons - 1);
            }

            const pct = recomputeFromCounters();

            if (pct === 100) {
                handleCourseCompletion({
                    completedAt: data?.completed_at ?? null,
                    availableAt: data?.available_at ?? null,
                });
            }

            // Update progress after successful completion
            computeProgress();

        } catch (e) {
            if (checkboxEl) checkboxEl.checked = !isChecked;
            alert('Erro de rede ao salvar. Tente novamente.');
        } finally {
            if (checkboxEl) checkboxEl.disabled = false;
        }
    }

    async function submitQuizScore(score) {
        const passed = score >= quizMinScoreValue;

        if (previewMode) {
            syncLessonCheckboxState(currentLessonId, passed);
            completedLessons = getLessonProgressSnapshot().done;
            const pct = recomputeFromCounters();
            if (pct === 100) {
                handleCourseCompletion({
                    pdfReady: false
                });
            }

            return {
                ok: true,
                preview: true,
                pdf_ready: false
            };
        }

        if (!enrollmentId) {
            alert('Matr&iacute;cula n&atilde;o identificada. Recarregue a p&aacute;gina.');
            return null;
        }

        const checkbox = document.querySelector(`.lesson-row[data-lesson-id="${currentLessonId}"] .lesson-check`);

        try {
            const data = await fetchJSON('<?= site_url('student/lessons/complete') ?>', {
                method: 'POST',
                body: JSON.stringify({
                    lesson_id: Number(currentLessonId),
                    enrollment_id: Number(enrollmentId),
                    score: Number(score)
                })
            });

            if (!data?.ok) {
                alert(data?.message || 'Não foi possível salvar o quiz.');
                return null;
            }

            if (checkbox) {
                const wasChecked = checkbox.checked;
                checkbox.checked = passed;
                if (passed && !wasChecked) {
                    completedLessons = Math.min(totalLessons, completedLessons + 1);
                } else if (!passed && wasChecked) {
                    completedLessons = Math.max(0, completedLessons - 1);
                }
            }

            const pct = recomputeFromCounters();
            computeProgress();
            if (pct === 100) {
                handleCourseCompletion({
                    completedAt: data?.completed_at ?? null,
                    availableAt: data?.available_at ?? null,
                });
            }

            return data;
        } catch (e) {
            alert('Erro de rede ao salvar o quiz.');
            return null;
        }
    }

    function escapeHtml(value) {
        if (typeof value !== 'string') return '';
        return value
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function initQuizStepper() {
        const quizStepper = document.getElementById('quizStepper');
        if (!quizStepper || !quizQuestionsData.length) return;

        const optionsContainer = document.getElementById('quizOptions');
        if (optionsContainer) {
            quizQuestionBlocks = Array.from(optionsContainer.querySelectorAll('.quiz-question-block'));
            optionsContainer.addEventListener('click', (event) => {
                const button = event.target.closest('.quiz-option');
                if (!button) return;
                const optionIndex = Number(button.dataset.optionIndex);
                quizSelections[currentQuizIndex] = optionIndex;
                renderQuizStep();
            });
        }

        renderQuizStep();

        document.getElementById('quizPrevBtn')?.addEventListener('click', () => {
            if (currentQuizIndex === 0) return;
            currentQuizIndex--;
            renderQuizStep();
        });

        document.getElementById('quizNextBtn')?.addEventListener('click', async () => {
            if (quizSelections[currentQuizIndex] === null) return;
            if (currentQuizIndex < quizQuestionsData.length - 1) {
                currentQuizIndex++;
                renderQuizStep();
                return;
            }
            await finishQuiz();
        });
    }

    function renderQuizStep() {
        const total = quizQuestionsData.length;
        const stepLabel = document.getElementById('quizStepLabel');
        const progressPercent = document.getElementById('quizProgressPercent');
        const progressBar = document.getElementById('quizProgressBar');
        const hint = document.getElementById('quizHint');
        const prevBtn = document.getElementById('quizPrevBtn');
        const nextBtn = document.getElementById('quizNextBtn');

        if (!stepLabel || !progressPercent || !progressBar || !hint || !prevBtn || !nextBtn) return;

        const currentBlock = quizQuestionBlocks[currentQuizIndex];
        if (!currentBlock) return;

        stepLabel.textContent = `Pergunta ${currentQuizIndex + 1} de ${total}`;
        const pct = total ? Math.round((currentQuizIndex / total) * 100) : 0;
        progressPercent.textContent = `${pct}%`;
        progressBar.style.width = `${pct}%`;

        quizQuestionBlocks.forEach((block, idx) => {
            block.classList.toggle('hidden', idx !== currentQuizIndex);
        });

        const selectedOption = quizSelections[currentQuizIndex];
        currentBlock.querySelectorAll('.quiz-option').forEach(button => {
            const optionIndex = Number(button.dataset.optionIndex);
            button.classList.toggle('selected', selectedOption === optionIndex);
        });

        hint.textContent = quizSelections[currentQuizIndex] === null ? 'Selecione uma resposta para continuar.' : 'Resposta registrada. Clique em próxima pergunta.';
        prevBtn.disabled = currentQuizIndex === 0;
        nextBtn.disabled = quizSelections[currentQuizIndex] === null;
        nextBtn.textContent = currentQuizIndex === total - 1 ? 'Enviar respostas' : 'Próxima pergunta';
    }

    async function finishQuiz() {
        const total = quizQuestionsData.length;
        const wrongAnswers = [];
        let correct = 0;

        quizQuestionsData.forEach((question, idx) => {
            const selected = quizSelections[idx];
            if (selected === null) return;
            const correctIndex = parseInt(question.correct ?? 0, 10);
            if (parseInt(selected, 10) === correctIndex) {
                correct++;
                return;
            }
            wrongAnswers.push({
                index: idx + 1,
                question: question.question ?? '',
                selected: question.options?.[selected] ?? 'Resposta inválida'
            });
        });

        const score = Math.round((correct / total) * 100);
        const data = await submitQuizScore(score);
        if (!data) return;

        quizAttempted = true;
        quizPassed = score >= quizMinScoreValue;
        if (nextLessonBtn) {
            nextLessonBtn.disabled = !quizPassed;
            if (quizPassed) {
                const label = nextLessonBtn.querySelector('.next-lesson-label');
                if (label) {
                    label.textContent = isLastInModule ? 'Próximo Módulo' : 'Próxima Aula';
                }
            }
        }

        if (window.Swal && quizPassed) {
            Swal.fire({
                icon: 'success',
                title: 'Parabéns!',
                html: `
                    <p class="text-lg font-semibold">${score}%</p>
                    <p class="text-sm text-slate-500 dark:text-slate-200">
                        Você acertou ${correct} de ${total} questões e alcançou os ${quizMinScoreValue}% mínimos.
                    </p>
                `,
                confirmButtonText: 'Fechar'
            }).then(() => {
                const target = nextModuleUrl || withAutoplay(nextUrl);
                if (target) {
                    window.location.href = target;
                }
            });
        }

        if (quizPassed) {
            hideQuizFailPopup();
            showQuizSummary(score, wrongAnswers, correct);
        } else {
            openQuizFailPopup(score, correct);
        }
    }

    function showQuizSummary(score, wrongAnswers, correctCount = 0) {
        const summary = document.getElementById('quizSummary');
        const stepper = document.getElementById('quizStepper');
        const scoreEl = document.getElementById('quizSummaryScore');
        const noteEl = document.getElementById('quizSummaryNote');
        const wrongList = document.getElementById('quizSummaryWrongList');
        const retryBtn = document.getElementById('quizRetryBtn');

        if (quizPassed) {
            stepper?.classList.add('hidden');
        } else {
            stepper?.classList.remove('hidden');
        }
        if (!summary || !scoreEl || !noteEl || !wrongList) return;

        const titleColor = quizPassed ? 'text-emerald-600' : 'text-red-600';
        scoreEl.innerHTML = `
            <div class="text-center space-y-2">
                <p class="text-xs uppercase tracking-wider text-slate-500">Quiz Finalizado</p>
                <p class="text-5xl font-extrabold ${quizPassed ? 'text-emerald-600' : 'text-blue-600'}">${score}%</p>
                <p class="text-sm text-slate-600 dark:text-slate-400">Você acertou ${correctCount} de ${quizQuestionsData.length} questões</p>
                <p class="font-semibold ${titleColor}">
                    ${quizPassed ? `Nota mínima alcançada (${quizMinScoreValue}%) — siga para a próxima aula!` : `Estude um pouco mais e tente novamente para atingir ${quizMinScoreValue}%.`}
                </p>
            </div>
        `;
        noteEl.textContent = '';
        if (wrongAnswers.length === 0) {
            wrongList.innerHTML = `<div class="text-sm text-emerald-600 dark:text-emerald-300">Nenhuma resposta incorreta registrada.</div>`;
        } else {
            wrongList.innerHTML = wrongAnswers.map(item => `
                <div class="rounded-xl border border-red-100 dark:border-red-600 bg-red-50 dark:bg-red-900/30 p-3 text-sm text-red-800 dark:text-red-200">
                    <p class="font-semibold mb-1">Questão ${item.index}</p>
                    <p>${escapeHtml(item.question)}</p>
                    <p class="text-xs mt-2 text-red-600 dark:text-red-300">Você escolheu: ${escapeHtml(item.selected)}</p>
                </div>
            `).join('');
        }

        summary.classList.remove('hidden');
        if (retryBtn) {
            retryBtn.classList.remove('hidden');
            retryBtn.onclick = resetQuizState;
        }
    }

    const quizFailDialog = document.getElementById('quizFailDialog');
    const quizFailScoreEl = document.getElementById('quizFailScore');
    const quizFailDetailEl = document.getElementById('quizFailDetail');
    const quizFailMinScoreEl = document.getElementById('quizFailMinScore');
    const quizFailRetryBtn = document.getElementById('quizFailRetryBtn');

        if (quizFailRetryBtn) {
            quizFailRetryBtn.addEventListener('click', () => {
                hideQuizFailPopup();
                resetQuizState();
            });
        }

    quizFailDialog?.addEventListener('click', (event) => {
        if (event.target === quizFailDialog) {
            hideQuizFailPopup();
        }
    });

    function openQuizFailPopup(score, correctCount) {
        if (!quizFailDialog) return;
        quizFailScoreEl && (quizFailScoreEl.textContent = `${score}%`);
        quizFailDetailEl && (quizFailDetailEl.textContent = `Você acertou ${correctCount} de ${quizQuestionsData.length} questões`);
        quizFailMinScoreEl && (quizFailMinScoreEl.textContent = `${quizMinScoreValue}%`);
        quizFailDialog.classList.remove('hidden');
    }

    function hideQuizFailPopup() {
        quizFailDialog?.classList.add('hidden');
    }

    function resetQuizState() {
        const summary = document.getElementById('quizSummary');
        const stepper = document.getElementById('quizStepper');
        summary?.classList.add('hidden');
        stepper?.classList.remove('hidden');
        quizSelections.fill(null);
        currentQuizIndex = 0;
        quizAttempted = false;
        quizPassed = false;
        if (nextLessonBtn) {
            nextLessonBtn.disabled = true;
        }
        renderQuizStep();
        hideQuizFailPopup();
    }

    // Initialize lesson checkboxes
    document.querySelectorAll('.lesson-check').forEach(cb => {
        cb.addEventListener('change', (e) => {
            if (accessBlocked) {
                showBlockedAccessModal();
                e.target.checked = !e.target.checked;
                return;
            }
            const row = e.target.closest('.lesson-row');
            const id = row?.dataset?.lessonId;
            if (id) toggleLessonComplete(id, e.target.checked, e.target);
        });
    });

    // Compute initial progress
    computeProgress();


    /* =========================
       Module Accordion - Desktop
       ========================= */
    function toggleModule(index) {
        const moduleContent = document.getElementById(`module-${index}`);
        const moduleHeader = document.querySelector(`button[onclick="toggleModule(${index})"]`);
        const chevron = moduleHeader.querySelector('i.bi-chevron-down');

        if (moduleContent.classList.contains('hidden')) {
            // Close all other modules
            document.querySelectorAll('.module-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.querySelectorAll('.module-header i.bi-chevron-down').forEach(icon => {
                icon.classList.remove('rotate-180');
            });
            // Open this module
            moduleContent.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            moduleContent.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }

    /* =========================
       Module Accordion - Mobile
       ========================= */
    function toggleModuleMobile(index) {
        const moduleContent = document.getElementById(`module-mobile-${index}`);
        const moduleHeader = document.querySelector(`button[onclick="toggleModuleMobile(${index})"]`);
        const chevron = moduleHeader.querySelector('i.bi-chevron-down');

        if (moduleContent.classList.contains('hidden')) {
            // Close all other mobile modules
            document.querySelectorAll('.module-content-mobile').forEach(content => {
                content.classList.add('hidden');
            });
            document.querySelectorAll('.module-header i.bi-chevron-down').forEach(icon => {
                icon.classList.remove('rotate-180');
            });
            // Open this module
            moduleContent.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            moduleContent.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }

    // Auto-expand module containing current lesson
    document.addEventListener('DOMContentLoaded', () => {
        // Desktop
        const currentLesson = document.querySelector('.lesson-row.bg-blue-50, .lesson-row.dark\\:bg-blue-900');
        if (currentLesson) {
            const module = currentLesson.closest('.module-content');
            if (module) {
                module.classList.remove('hidden');
                const moduleIndex = module.id.split('-')[1];
                const moduleHeader = document.querySelector(`button[onclick="toggleModule(${moduleIndex})"]`);
                const chevron = moduleHeader?.querySelector('i.bi-chevron-down');
                if (chevron) {
                    chevron.classList.add('rotate-180');
                }
            }
        }

        // Mobile
        const currentLessonMobile = document.querySelector('.lesson-row.bg-blue-50, .lesson-row.dark\\:bg-blue-900');
        if (currentLessonMobile) {
            const moduleMobile = currentLessonMobile.closest('.module-content-mobile');
            if (moduleMobile) {
                moduleMobile.classList.remove('hidden');
                const moduleIndex = moduleMobile.id.split('-')[2];
                const moduleHeader = document.querySelector(`button[onclick="toggleModuleMobile(${moduleIndex})"]`);
                const chevron = moduleHeader?.querySelector('i.bi-chevron-down');
                if (chevron) {
                    chevron.classList.add('rotate-180');
                }
            }
        }
    });

    /* =========================
       Mobile Drawer
       ========================= */
    const drawerToggle = document.getElementById('drawerToggle');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const closeDrawer = document.getElementById('closeDrawer');
    const drawerBackdrop = document.getElementById('drawerBackdrop');

    function openDrawer() {
        mobileDrawer.classList.remove('translate-x-full');
        drawerBackdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawerFunc() {
        mobileDrawer.classList.add('translate-x-full');
        drawerBackdrop.classList.add('hidden');
        document.body.style.overflow = '';
    }

    if (drawerToggle) {
        drawerToggle.addEventListener('click', openDrawer);
    }

    if (closeDrawer) {
        closeDrawer.addEventListener('click', closeDrawerFunc);
    }

    if (drawerBackdrop) {
        drawerBackdrop.addEventListener('click', closeDrawerFunc);
    }

    /* =========================
       Vimeo Player & Navigation
       ========================= */
    let vimeoIframe = document.getElementById('vimeoPlayer');
    let player = vimeoIframe ? new Vimeo.Player(vimeoIframe) : null;

    let currentLessonId = <?= (int)$lesson->id_lesson ?>;
    let hasNext = <?= $nextLesson ? 'true' : 'false' ?>;
    let nextUrl = "<?= esc($nextUrlAttr) ?>";
    let isQuizLesson = <?= $isQuiz ? 'true' : 'false' ?>;
    let quizAttempted = <?= $quizAttempted ? 'true' : 'false' ?>;
    let quizPassed = <?= ($isQuiz && $quizPassed) ? 'true' : 'false' ?>;
    let isLastInModule = <?= $isLastInModule ? 'true' : 'false' ?>;
    let nextModuleUrl = "<?= $nextModuleUrl ?>";
    <?php
    $quizQuestionsJson = json_encode($quizQuestions, JSON_UNESCAPED_UNICODE);
    if ($quizQuestionsJson === false) {
        $quizQuestionsJson = '[]';
    }
    ?>
    const quizQuestionsData = <?= $quizQuestionsJson ?>;
    const quizMinScoreValue = <?= (int) $quizMinScore ?>;
    const quizSelections = Array(quizQuestionsData.length).fill(null);
    let currentQuizIndex = 0;
    let quizQuestionBlocks = [];

    let endOverlay = document.getElementById('endOverlay');
    let goNextBtn = document.getElementById('goNextBtn');
    let stayBtn = document.getElementById('stayBtn');
    let nextLessonBtn = document.getElementById('nextLessonBtn');

    function withAutoplay(url) {
        return url + (url.includes('?') ? '&' : '?') + 'autoplay=1';
    }

    function readLessonStateFromDom() {
        const lessonContent = document.getElementById('lesson-content');
        if (!lessonContent) return;

        currentLessonId = Number(lessonContent.dataset.lessonId || 0);
        nextUrl = lessonContent.dataset.nextUrl || '';
        hasNext = Boolean(nextUrl);
        isQuizLesson = lessonContent.dataset.isQuiz === '1';
        quizAttempted = lessonContent.dataset.quizAttempted === '1';
        quizPassed = lessonContent.dataset.quizPassed === '1';
        isLastInModule = lessonContent.dataset.isLastModule === '1';
        nextModuleUrl = lessonContent.dataset.nextModuleUrl || '';

        const currentCheckbox = document.querySelector(`.lesson-row[data-lesson-id="${currentLessonId}"] .lesson-check`);
        hasReached95Percent = currentCheckbox ? currentCheckbox.checked : false;
        currentVideoProgress = 0;

        endOverlay = document.getElementById('endOverlay');
        goNextBtn = document.getElementById('goNextBtn');
        stayBtn = document.getElementById('stayBtn');
        nextLessonBtn = document.getElementById('nextLessonBtn');

        vimeoIframe = document.getElementById('vimeoPlayer');
        if (player) {
            player.unload().catch(() => {});
        }
        player = vimeoIframe ? new Vimeo.Player(vimeoIframe) : null;
    }

    function highlightCurrentLesson() {
        document.querySelectorAll('.lesson-row').forEach((row) => {
            row.classList.remove('bg-blue-50', 'dark:bg-blue-900/30', 'border-l-2', 'border-blue-500');
            const badge = row.querySelector('.badge-current');
            if (badge) badge.remove();
        });

        const currentRow = document.querySelector(`.lesson-row[data-lesson-id="${currentLessonId}"]`);
        if (currentRow) {
            currentRow.classList.add('bg-blue-50', 'dark:bg-blue-900/30', 'border-l-2', 'border-blue-500');
            const link = currentRow.querySelector('.lesson-link');
            if (link) {
                link.insertAdjacentHTML('beforeend',
                    '<span class="badge-current font-medium px-2 py-0.5 bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-full whitespace-nowrap text-xs">Atual</span>'
                );
            }
        }
    }

    function bindLessonContent() {
        if (accessBlocked) {
            if (nextLessonBtn) {
                nextLessonBtn.disabled = true;
            }
            return;
        }

        if (stayBtn) {
            stayBtn.addEventListener('click', hideEndOverlay);
        }

        if (nextLessonBtn) {
            nextLessonBtn.disabled = isQuizLesson && !quizPassed;
            nextLessonBtn.addEventListener('click', () => {
                if (canProceedToNextLesson()) {
                    window.location.href = withAutoplay(nextUrl);
                } else {
                    showCompletionWarning();
                }
            });
        }

        initQuizStepper();

        bindVideoPlayer();
    }

    function bindVideoPlayer() {
        if (!player) return;
        if (accessBlocked) return;

        player.off('ended');
        player.off('timeupdate');

        player.on('ended', async function() {
            try {
                await markCompletedOnEnd();
            } catch (e) {
                console.warn('Falha ao marcar conclu?da no t?rmino:', e);
            }
            if (hasNext && nextUrl) showEndOverlay();
        });

        // Track progress and mark as completed at 95%
        player.on('timeupdate', async function(data) {
            try {
                const duration = (await player.getDuration()) || 0;
                const watched = data.seconds || 0;
                currentVideoProgress = duration > 0 ? (watched / duration) * 100 : 0;

                if (duration > 0 && currentVideoProgress >= 95 && !hasReached95Percent) {
                    const checkbox = document.querySelector(`.lesson-row[data-lesson-id="${currentLessonId}"] .lesson-check`);
                    if (checkbox && !checkbox.checked) {
                        checkbox.checked = true;
                        await toggleLessonComplete(currentLessonId, true, checkbox);
                        hasReached95Percent = true;
                    }
                }
            } catch (error) {
                // Silent fail
            }
        });
    }

    async function loadLesson(url) {
        if (accessBlocked) {
            showBlockedAccessModal();
            return;
        }
        try {
            const res = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!res.ok) {
                window.location.href = url;
                return;
            }

            const html = await res.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.getElementById('lesson-content');
            const currentContent = document.getElementById('lesson-content');
            if (newContent && currentContent) {
                currentContent.replaceWith(newContent);
                history.pushState({}, '', url);
                readLessonStateFromDom();
                bindLessonContent();
                highlightCurrentLesson();
            } else {
                window.location.href = url;
            }
        } catch (e) {
            window.location.href = url;
        }
    }

    function showEndOverlay() {
        if (!endOverlay) return;
        if (isQuizLesson && !canProceedToNextLesson()) return;
        endOverlay.classList.remove('hidden');
        endOverlay.classList.add('flex');

        if (goNextBtn && nextUrl) {
            goNextBtn.href = withAutoplay(nextUrl);
        }
    }

    function hideEndOverlay() {
        if (!endOverlay) return;
        endOverlay.classList.add('hidden');
        endOverlay.classList.remove('flex');
    }

    // Check if user can proceed to next lesson
    function canProceedToNextLesson() {
        if (isQuizLesson) {
            return quizPassed;
        }
        const currentCheckbox = document.querySelector(`.lesson-row[data-lesson-id="${currentLessonId}"] .lesson-check`);
        return currentCheckbox?.checked || hasReached95Percent;
    }

    // Show warning if user hasn't completed 95% of current lesson
    function showCompletionWarning() {
        const isQuizWarning = isQuizLesson;
        const warningMessage = isQuizWarning
            ? `Você precisa atingir pelo menos ${quizMinScoreValue}% neste quiz antes de avançar.`
            : 'Você precisa assistir pelo menos 95% desta aula antes de prosseguir para a próxima.';
        const primaryLabel = isQuizWarning ? 'Refazer quiz' : 'Continuar Assistindo';
        const secondaryLabel = isQuizWarning ? 'Cancelar' : 'Ir Mesmo Assim';

        const warningModal = `
            <div id="completionWarning" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md mx-4 shadow-xl">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="bi bi-exclamation-triangle text-2xl text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Atenção</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm">
                            ${warningMessage}
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <button id="continueWatching" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm flex-1">
                                ${primaryLabel}
                            </button>
                            <button id="forceNext" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm flex-1">
                                ${secondaryLabel}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', warningModal);

        const warning = document.getElementById('completionWarning');
        const continueWatching = document.getElementById('continueWatching');
        const forceNext = document.getElementById('forceNext');

        if (isQuizWarning && forceNext) {
            forceNext.classList.add('hidden');
        }

        continueWatching.addEventListener('click', () => {
            warning.remove();
            if (isQuizWarning) {
                resetQuizState();
            } else if (player) {
                player.play().catch(() => {});
            }
        });

        if (forceNext) {
            if (isQuizWarning) {
                forceNext.addEventListener('click', () => {
                    warning.remove();
                });
            } else {
                forceNext.addEventListener('click', () => {
                    warning.remove();
                    window.location.href = withAutoplay(nextUrl);
                });
            }
        }

        // Close on backdrop click
        warning.addEventListener('click', (e) => {
            if (e.target === warning) {
                warning.remove();
            }
        });
    }

    // Mark lesson as completed when video ends
    async function markCompletedOnEnd() {
        const checkbox = document.querySelector(`.lesson-row[data-lesson-id="${currentLessonId}"] .lesson-check`);
        if (checkbox && !checkbox.checked) {
            checkbox.checked = true;
            await toggleLessonComplete(currentLessonId, true, checkbox);
            hasReached95Percent = true;
        }
    }

    // Autoplay on page load if autoplay=1
    const urlParams = new URLSearchParams(window.location.search);
    const shouldAutoplay = urlParams.get('autoplay') === '1';

    if (player && shouldAutoplay) {
        player.ready().then(() => {
            player.play().catch(() => {
                // Browser may block autoplay
            });
        });
    }

    const statusFromDom = document.querySelector('[data-enrollment-status]')?.dataset?.enrollmentStatus || '';
    const accessBlocked = statusFromDom === 'cancelada';

    function showBlockedAccessModal() {
        if (!accessBlocked) return;
        const fallbackUrl = <?= json_encode(site_url('student/dashboard/meus_cursos')) ?>;
        const blockedModal = `
            <div id="blockedAccessModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md mx-4 shadow-xl">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="bi bi-lock-fill text-2xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Acesso bloqueado</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm">
                            Você foi bloqueado pelo instrutor do curso. Entre em contato com o instrutor para mais detalhes.
                        </p>
                        <div class="flex justify-center">
                            <button id="blockedOkBtn" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm">
                                OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', blockedModal);
        document.body.style.overflow = 'hidden';

        const goBack = () => {
            if (document.referrer) {
                window.location.href = document.referrer;
                return;
            }
            window.location.href = fallbackUrl;
        };

        document.getElementById('blockedOkBtn')?.addEventListener('click', goBack);
        const modalEl = document.getElementById('blockedAccessModal');
        modalEl?.addEventListener('click', (e) => {
            if (e.target === modalEl) goBack();
        });
    }
    document.addEventListener('DOMContentLoaded', () => {
        // já pinta a UI com o valor inicial do backend
        setProgressUI(courseProgress);

        if (courseProgress === 100) {
            handleCourseCompletion();
        }

        showBlockedAccessModal();

        readLessonStateFromDom();
        bindLessonContent();
        highlightCurrentLesson();
    });

    document.addEventListener('click', (e) => {
        const link = e.target.closest('.lesson-link');
        if (!link) return;
        e.preventDefault();
        if (accessBlocked) {
            showBlockedAccessModal();
            return;
        }
        const url = link.getAttribute('href');
        if (url) {
            loadLesson(url);
        }
    });

    window.addEventListener('popstate', () => {
        loadLesson(window.location.href);
    });
</script>

<?= $this->endSection() ?>
