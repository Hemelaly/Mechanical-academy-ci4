<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Painel do Estudante<?= $this->endSection() ?>

<?= $this->section('home_student') ?>

<div class="dash-page">
    <section class="dash-hero">
        <div class="relative z-10 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-white/45">Painel do estudante</p>
                <h1 class="mt-2">Olá, <?= esc($user->username) ?></h1>
                <p>Acompanhe o seu desempenho e continue a jornada de aprendizado.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="/student/dashboard/inscricoes" class="dash-btn dash-btn-primary">
                    <i class="bi bi-play-circle text-sm"></i>
                    Meus cursos
                </a>
                <a href="/student/dashboard/cursos" class="dash-btn dash-btn-soft">
                    <i class="bi bi-collection text-sm"></i>
                    Explorar
                </a>
            </div>
        </div>
    </section>

    <?php
    $totalProgress = 0;
    $activeCount = 0;
    foreach ($courses as $course) {
        if (in_array($course->id_course, $activeCourseIds)) {
            $totalProgress += $progress->{$course->id_course}->progress;
            $activeCount++;
        }
    }
    $avgProgress = $activeCount > 0 ? round($totalProgress / $activeCount) : 0;
    ?>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="dash-stat">
            <div class="dash-stat-icon">
                <i class="bi bi-book"></i>
            </div>
            <div>
                <p class="dash-stat-label">Cursos ativos</p>
                <p class="dash-stat-value"><?= count($activeCourseIds) ?></p>
            </div>
        </div>

        <div class="dash-stat">
            <div class="dash-stat-icon bg-emerald-500/15 text-emerald-500">
                <i class="bi bi-grid-3x3"></i>
            </div>
            <div>
                <p class="dash-stat-label">Total de cursos</p>
                <p class="dash-stat-value"><?= count($courses) ?></p>
            </div>
        </div>

        <div class="dash-stat">
            <div class="dash-stat-icon bg-violet-500/15 text-violet-400">
                <i class="bi bi-graph-up"></i>
            </div>
            <div>
                <p class="dash-stat-label">Progresso médio</p>
                <p class="dash-stat-value"><?= $avgProgress ?>%</p>
            </div>
        </div>
    </div>

    <div class="dash-panel overflow-hidden">
        <div class="dash-panel-head">
            <div>
                <h5 class="dash-card-title">
                    <i class="bi bi-play-circle text-blue-500"></i>
                    Cursos em andamento
                </h5>
                <p class="dash-card-desc">Continue de onde parou</p>
            </div>
            <span class="dash-badge bg-blue-500/15 text-blue-400">
                <?= count($activeCourseIds) ?> ativos
            </span>
        </div>

        <div class="space-y-3 p-4 sm:p-5">
            <?php foreach ($courses as $course): ?>
                <?php if (in_array($course->id_course, $activeCourseIds)): ?>
                    <?php
                    $lessonUrl = (!empty($lesson[0]->courseSlug) && !empty($lesson[0]->resumeLessonSlug))
                        ? site_url('student/dashboard/inscricoes/' . $lesson[0]->courseSlug . '/' . $lesson[0]->resumeLessonSlug)
                        : site_url('student/dashboard/ver_aulas/' . $lesson[0]->resumeLessonId);
                    $statusEnrollment = strtolower((string) ($lesson[0]->status_enrollment ?? $lesson[0]->enrollmentStatus ?? ''));
                    $isBlocked = $statusEnrollment === 'cancelada';
                    $progressPct = (int) ($lesson[0]->progress ?? 0);
                    $autoSuffix = (!$isBlocked && $progressPct < 100) ? '?autoplay=1' : '';
                    $courseProgress = (int) round($progress->{$course->id_course}->progress);
                    ?>
                    <div class="group rounded-md border border-slate-200/80 bg-slate-50/80 p-4 transition hover:border-blue-500/40 dark:border-white/10 dark:bg-white/[0.03]">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="mb-3 flex items-start gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md bg-blue-600/15 text-blue-500">
                                        <i class="bi bi-play-btn"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="mb-1 flex flex-wrap items-center gap-2">
                                            <h3 class="text-sm font-semibold text-slate-800 transition group-hover:text-blue-500 dark:text-white dark:group-hover:text-blue-300">
                                                <?= esc($course->title_course) ?>
                                            </h3>
                                            <span class="dash-badge bg-slate-200 text-slate-600 dark:bg-white/10 dark:text-white/60">
                                                <?= esc($course->category ?? 'Curso') ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-white/45">
                                            <i class="bi bi-person"></i>
                                            <span><?= esc($course->name_instructor ?? 'N/A') ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-500 dark:text-white/45">Seu progresso</span>
                                        <span class="font-semibold text-slate-800 dark:text-white"><?= esc($courseProgress) ?>%</span>
                                    </div>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-white/10">
                                        <div class="h-1.5 rounded-full bg-blue-500" style="width: <?= esc($courseProgress) ?>%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-2 lg:flex-col lg:items-end">
                                <a href="<?= $lessonUrl ?><?= $autoSuffix ?>"
                                    class="dash-btn dash-btn-primary whitespace-nowrap">
                                    <i class="bi bi-play-circle"></i>
                                    Continuar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if (count($activeCourseIds) === 0): ?>
                <div class="py-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-md bg-slate-100 dark:bg-white/5">
                        <i class="bi bi-book text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="mb-2 text-sm font-semibold text-slate-600 dark:text-white/60">
                        Nenhum curso em andamento
                    </h3>
                    <p class="mb-6 text-slate-500 dark:text-white/40">
                        Comece um novo curso para ver o progresso aqui.
                    </p>
                    <a href="/student/dashboard/cursos" class="dash-btn dash-btn-primary inline-flex">
                        <i class="bi bi-plus-lg"></i>
                        Explorar cursos
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
        <a href="/student/dashboard/inscricoes"
            class="dash-card group text-center transition hover:border-blue-500/40">
            <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-md bg-blue-600/15 text-blue-500 transition group-hover:scale-105">
                <i class="bi bi-collection-play"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Meus cursos</span>
        </a>

        <a href="/student/dashboard/cursos"
            class="dash-card group text-center transition hover:border-emerald-500/40">
            <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-md bg-emerald-500/15 text-emerald-500 transition group-hover:scale-105">
                <i class="bi bi-compass"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Descobrir</span>
        </a>

        <a href="/student/profile"
            class="dash-card group text-center transition hover:border-violet-500/40">
            <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-md bg-violet-500/15 text-violet-400 transition group-hover:scale-105">
                <i class="bi bi-person"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Perfil</span>
        </a>

        <a href="/student/settings"
            class="dash-card group text-center transition hover:border-amber-500/40">
            <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-md bg-amber-500/15 text-amber-400 transition group-hover:scale-105">
                <i class="bi bi-gear"></i>
            </div>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Configurações</span>
        </a>
    </div>
</div>

<?= $this->endSection() ?>
