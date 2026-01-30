<?php
// dd($enrollment)
$autoplayFlag = (int) ($_GET['autoplay'] ?? 0);
$auto = $autoplayFlag ? 1 : 0;
helper('text');
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

$isLastInModule = false;
if ($currentModule && !empty($currentModule->lessons)) {
    $lastIndex = count($currentModule->lessons) - 1;
    if ($lastIndex >= 0) {
        $lastLesson = $currentModule->lessons[$lastIndex];
        $isLastInModule = (int) $lastLesson->id_lesson === (int) $lesson->id_lesson;
    }
}
$nextModuleUrl = !empty($nextModuleLessonSlug)
    ? site_url('student/dashboard/inscricoes/' . $courseSlug . '/' . $nextModuleLessonSlug)
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

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm mb-6">
            <a href="/student/dashboard/inscricoes" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors flex items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                Voltar aos Cursos
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
            $nextUrlAttr = $nextLesson
                ? (!empty($courseSlug) && !empty($nextLessonSlug)
                    ? site_url('student/dashboard/inscricoes/' . $courseSlug . '/' . $nextLessonSlug)
                    : site_url('student/dashboard/ver_aulas/' . $nextLesson))
                : '';
            $prevUrlAttr = $prevLesson
                ? (!empty($courseSlug) && !empty($prevLessonSlug)
                    ? site_url('student/dashboard/inscricoes/' . $courseSlug . '/' . $prevLessonSlug)
                    : site_url('student/dashboard/ver_aulas/' . $prevLesson))
                : '';
            ?>
            <div id="lesson-content"
                data-lesson-id="<?= (int) $lesson->id_lesson ?>"
                data-next-url="<?= esc($nextUrlAttr) ?>"
                data-prev-url="<?= esc($prevUrlAttr) ?>"
                data-is-quiz="<?= $isQuiz ? '1' : '0' ?>"
                data-quiz-attempted="<?= $quizAttempted ? '1' : '0' ?>"
                data-quiz-passed="<?= $quizPassed ? '1' : '0' ?>"
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
                                            $nextUrl = (!empty($courseSlug) && !empty($nextLessonSlug))
                                                ? site_url('student/dashboard/inscricoes/' . $courseSlug . '/' . $nextLessonSlug)
                                                : site_url('student/dashboard/ver_aulas/' . $nextLesson);
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
                                        <div id="autoNote" class="text-gray-500 dark:text-gray-400 text-xs">
                                            Indo automaticamente em <span id="countdown" class="font-semibold">5</span>s...
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
                                <form id="quizForm" class="space-y-4">
                                    <?php foreach ($quizQuestions as $qIndex => $question): ?>
                                        <div class="quiz-question-item bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4"
                                            data-correct="<?= (int) ($question['correct'] ?? 0) ?>">
                                            <p class="font-semibold text-gray-900 dark:text-slate-100 text-sm mb-3">
                                                <?= esc(($qIndex + 1) . '. ' . ($question['question'] ?? '')) ?>
                                            </p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                <?php for ($opt = 0; $opt < 4; $opt++): ?>
                                                    <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm cursor-pointer hover:border-blue-400 transition-colors">
                                                        <input type="radio"
                                                            name="quiz_q_<?= $qIndex ?>"
                                                            value="<?= $opt ?>"
                                                            class="text-blue-600 focus:ring-blue-500">
                                                        <span class="text-gray-800 dark:text-slate-100">
                                                            <?= esc($question['options'][$opt] ?? '') ?>
                                                        </span>
                                                    </label>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                        <button type="submit"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-5 rounded-lg transition-colors text-sm">
                                            Enviar respostas
                                        </button>
                                        <div id="quizResult" class="text-sm text-gray-600 dark:text-gray-300"></div>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between items-center">
                    <?php if ($prevLesson): ?>
                        <?php
                        $prevUrl = (!empty($courseSlug) && !empty($prevLessonSlug))
                            ? site_url('student/dashboard/inscricoes/' . $courseSlug . '/' . $prevLessonSlug)
                            : site_url('student/dashboard/ver_aulas/' . $prevLesson);
                        ?>
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
                                        <div class="lesson-row flex items-center justify-between p-3 border-t border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors <?= $isCurrent ? 'bg-blue-50 dark:bg-blue-900/30 border-l-2 border-blue-500' : '' ?>"
                                            data-lesson-id="<?= (int)$l->id_lesson ?>">

                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="lesson-check w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                        <?= $isDone ? 'checked' : '' ?>
                                                        aria-label="Marcar aula como conclu?da">
                                                </div>

                                                <?php $lessonSlug = url_title($l->title_lesson, '-', true); ?>
                                                <?php
                                                $lessonSlug = $lessonSlugById[(int) $l->id_lesson] ?? '';
                                                $iconClass = in_array($l->type_lesson, ['quiz', 'text'], true) ? 'bi-file-text' : 'bi-camera-video';
                                                ?>
                                                <?php
                                                $lessonUrl = $lessonSlug
                                                    ? site_url('student/dashboard/inscricoes/' . $courseSlug . '/' . $lessonSlug)
                                                    : site_url('student/dashboard/ver_aulas/' . $l->id_lesson);
                                                ?>
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
                                        <div class="lesson-row flex items-center justify-between p-3 border-t border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors <?= $isCurrent ? 'bg-blue-50 dark:bg-blue-900/30 border-l-2 border-blue-500' : '' ?>"
                                            data-lesson-id="<?= (int)$l->id_lesson ?>">

                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <div class="relative">
                                                    <input type="checkbox"
                                                        class="lesson-check w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                                        <?= $isDone ? 'checked' : '' ?>
                                                        aria-label="Marcar aula como concluída">
                                                </div>

                                                <?php
                                                $lessonSlug = $lessonSlugById[(int) $l->id_lesson] ?? '';
                                                $iconClass = in_array($l->type_lesson, ['quiz', 'text'], true) ? 'bi-file-text' : 'bi-camera-video';
                                                ?>
                                                <?php
                                                $lessonUrl = $lessonSlug
                                                    ? site_url('student/dashboard/inscricoes/' . $courseSlug . '/' . $lessonSlug)
                                                    : site_url('student/dashboard/ver_aulas/' . $l->id_lesson);
                                                ?>
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
    const csrfName = document.querySelector('meta[name="csrf-name"]')?.content;
    let csrfHash = document.querySelector('meta[name="csrf-hash"]')?.content;
    const enrollmentId = document.querySelector('.container')?.dataset?.enrollmentId;
    const pendingCertificateUrl = "<?= site_url('student/certificates/pending') ?>";
    let certificateInfo = <?= json_encode(array_merge(
                                [
                                    'completedAt' => null,
                                    'availableAt' => null,
                                    'pdfReady' => false,
                                ],
                                $certificateInfo ?? []
                            ), JSON_UNESCAPED_SLASHES) ?>;
    certificateInfo.downloadUrl = "<?= site_url('certificados/download/' . (int) ($enrollment->id_enrollment ?? 0)) ?>";

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

    const CERT_WAIT_MS = 48 * 60 * 60 * 1000;

    function parseIsoDate(value) {
        if (!value) return null;
        const d = new Date(value);
        return Number.isNaN(d.getTime()) ? null : d;
    }

    function computeAvailableAt(info) {
        const available = parseIsoDate(info.availableAt);
        if (available) return available;
        const completed = parseIsoDate(info.completedAt);
        if (!completed) return null;
        return new Date(completed.getTime() + CERT_WAIT_MS);
    }

    function formatCountdown(ms) {
        if (!ms || ms <= 0) return 'agora';
        const totalSeconds = Math.ceil(ms / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        return `${hours}h ${minutes}m ${seconds}s`;
    }

    function startCertificateCountdown(availableAt) {
        const counter = document.getElementById('certCountdown');
        if (!counter || !availableAt) return;

        const tick = () => {
            const remaining = availableAt.getTime() - Date.now();
            counter.textContent = formatCountdown(remaining);
            if (remaining <= 0) {
                clearInterval(timer);
            }
        };

        tick();
        const timer = setInterval(tick, 1000);
    }

    function showCourseCompletedModal(overrides = {}) {
        if (courseModalShown) return;
        courseModalShown = true;

        document.body.style.overflow = 'hidden';

        certificateInfo = {
            ...certificateInfo,
            ...overrides
        };

        const availableAt = computeAvailableAt(certificateInfo);
        const isReady = Boolean(certificateInfo.pdfReady) && availableAt && Date.now() >= availableAt.getTime();
        const message = isReady ?
            'O seu certificado em PDF j foi gerado e est disponvel.' :
            `O seu certificado estar disponvel em <b><span id="certCountdown">${formatCountdown(availableAt ? (availableAt.getTime() - Date.now()) : CERT_WAIT_MS)}</span></b>.`;
        const actionButton = isReady ?
            `<a href="${certificateInfo.downloadUrl}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm text-center">Ver PDF</a>` :
            `<a href="<?= site_url('student/dashboard/meus_certificados') ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm text-center">Ver Certificados</a>`;

        const modal = `
            <div id="courseCompletedModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 max-w-md w-[90%] text-center shadow-xl">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="bi bi-trophy text-3xl text-green-600 dark:text-green-400"></i>
                </div>

                <h4 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">Parabens!</h4>

                <p class="text-gray-600 dark:text-gray-300 mb-2 text-sm">
                Você concluiu 100% do curso.
                </p>

                <p class="text-gray-600 dark:text-gray-300 mb-5 text-sm">
                ${message}
                </p>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button id="certOkBtn"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm">
                    OK
                </button>

                ${actionButton}
                </div>
            </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modal);
        if (!isReady) {
            startCertificateCountdown(availableAt);
        }

        document.getElementById('certOkBtn').addEventListener('click', () => {
            requestPendingCertificate();
            const m = document.getElementById('courseCompletedModal');
            if (m) m.remove();
            document.body.style.overflow = '';
        });
    }


    async function requestPendingCertificate() {
        if (pendingCertificateRequested) return;
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

    function computeProgress() {
        const total = document.querySelectorAll('.lesson-row').length;
        const done = document.querySelectorAll('.lesson-check:checked').length;
        const pct = total ? Math.round((done / total) * 100) : 0;
        const ppEl = document.getElementById('progressPercentage');
        const barEl = document.getElementById('progressBar');
        if (ppEl) ppEl.textContent = pct + '%';
        if (barEl) barEl.style.width = pct + '%';
        return pct;
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
                if (data?.completed_at) {
                    certificateInfo.completedAt = data.completed_at;
                }
                if (data?.available_at) {
                    certificateInfo.availableAt = data.available_at;
                }
                showCourseCompletedModal({
                    completedAt: data?.completed_at || null,
                    availableAt: data?.available_at || null,
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

            if (checkbox && !checkbox.checked) {
                checkbox.checked = true;
                completedLessons = Math.min(totalLessons, completedLessons + 1);
                recomputeFromCounters();
                computeProgress();
            }

            return data;
        } catch (e) {
            alert('Erro de rede ao salvar o quiz.');
            return null;
        }
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

    let endOverlay = document.getElementById('endOverlay');
    let goNextBtn = document.getElementById('goNextBtn');
    let stayBtn = document.getElementById('stayBtn');
    let countdownEl = document.getElementById('countdown');
    let autoNote = document.getElementById('autoNote');
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
        countdownEl = document.getElementById('countdown');
        autoNote = document.getElementById('autoNote');
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
            nextLessonBtn.addEventListener('click', () => {
                if (canProceedToNextLesson()) {
                    window.location.href = withAutoplay(nextUrl);
                } else {
                    showCompletionWarning();
                }
            });
        }

        const quizForm = document.getElementById('quizForm');
        if (quizForm && !quizForm.dataset.bound) {
            quizForm.dataset.bound = '1';
            quizForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const questions = document.querySelectorAll('.quiz-question-item');
                const total = questions.length;
                if (!total) return;

                let correct = 0;
                let answered = 0;

                questions.forEach((qEl, idx) => {
                    const selected = qEl.querySelector(`input[name="quiz_q_${idx}"]:checked`);
                    if (!selected) return;
                    answered++;
                    const correctIndex = parseInt(qEl.dataset.correct || '0', 10);
                    if (parseInt(selected.value, 10) === correctIndex) {
                        correct++;
                    }
                });

                const resultEl = document.getElementById('quizResult');
                if (answered < total) {
                    if (resultEl) {
                        resultEl.textContent = 'Responda todas as perguntas antes de enviar.';
                        resultEl.className = 'text-sm text-yellow-600 dark:text-yellow-400';
                    }
                    return;
                }

                const score = Math.round((correct / total) * 100);
                const data = await submitQuizScore(score);
                if (!data) return;

                if (resultEl) {
                    const minScore = <?= (int) $quizMinScore ?>;
                    if (score >= minScore) {
                        resultEl.textContent = `Parabéns! Você fez ${score}% e atingiu a nota mínima.`;
                        resultEl.className = 'text-sm text-green-600 dark:text-green-400';
                        quizAttempted = true;
                        quizPassed = true;
                        if (nextLessonBtn) {
                            nextLessonBtn.disabled = false;
                            const label = nextLessonBtn.querySelector('.next-lesson-label');
                            if (label) {
                                label.textContent = isLastInModule ? 'Próximo Módulo' : 'Próxima Aula';
                            }
                        }
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Parabéns!',
                                text: `Você alcançou ${score}% e já pode avançar para a próxima fase.`,
                                confirmButtonText: 'Continuar'
                            }).then(() => {
                                if (nextModuleUrl) {
                                    window.location.href = nextModuleUrl;
                                }
                            });
                            return;
                        }
                        if (nextModuleUrl) {
                            window.location.href = nextModuleUrl;
                        }
                    } else {
                        resultEl.textContent = `Você fez ${score}%. Tente novamente para alcançar ${minScore}%.`;
                        resultEl.className = 'text-sm text-red-600 dark:text-red-400';
                        quizAttempted = true;
                        if (nextLessonBtn) {
                            nextLessonBtn.disabled = false;
                        }
                    }
                }
            });
        }

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

    let autoTimer = null;
    let seconds = 5;

    function showEndOverlay() {
        if (!endOverlay) return;
        endOverlay.classList.remove('hidden');
        endOverlay.classList.add('flex');

        if (goNextBtn && nextUrl) {
            goNextBtn.href = withAutoplay(nextUrl);
        }

        if (autoNote && countdownEl) {
            seconds = 5;
            countdownEl.textContent = seconds;
            autoTimer = setInterval(() => {
                seconds--;
                countdownEl.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(autoTimer);
                    if (nextUrl) window.location.href = withAutoplay(nextUrl);
                }
            }, 1000);
        }
    }

    function hideEndOverlay() {
        if (!endOverlay) return;
        endOverlay.classList.add('hidden');
        endOverlay.classList.remove('flex');
        if (autoTimer) {
            clearInterval(autoTimer);
            autoTimer = null;
        }
    }

    // Check if user can proceed to next lesson
    function canProceedToNextLesson() {
        if (isQuizLesson) {
            return quizAttempted || !nextLessonBtn?.disabled;
        }
        const currentCheckbox = document.querySelector(`.lesson-row[data-lesson-id="${currentLessonId}"] .lesson-check`);
        return currentCheckbox?.checked || hasReached95Percent;
    }

    // Show warning if user hasn't completed 95% of current lesson
    function showCompletionWarning() {
        const warningModal = `
            <div id="completionWarning" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md mx-4 shadow-xl">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="bi bi-exclamation-triangle text-2xl text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Atenção</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm">
                            Você precisa assistir pelo menos 95% desta aula antes de prosseguir para a próxima.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <button id="continueWatching" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm flex-1">
                                Continuar Assistindo
                            </button>
                            <button id="forceNext" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm flex-1">
                                Ir Mesmo Assim
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

        continueWatching.addEventListener('click', () => {
            warning.remove();
            if (player) {
                player.play().catch(() => {});
            }
        });

        forceNext.addEventListener('click', () => {
            warning.remove();
            window.location.href = withAutoplay(nextUrl);
        });

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
            showCourseCompletedModal();
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