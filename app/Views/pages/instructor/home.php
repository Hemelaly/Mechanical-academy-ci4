<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Painel do Instrutor<?= $this->endSection() ?>

<?= $this->section('home_instructor') ?>

<?php
$courseDeltaLabel = (int) ($newCoursesThisMonth ?? 0) > 0
    ? '+' . (int) $newCoursesThisMonth . ' este mes'
    : 'Sem novos cursos este mes';
$studentDeltaLabel = (int) ($newStudentsThisMonth ?? 0) > 0
    ? '+' . (int) $newStudentsThisMonth . ' novos este mes'
    : 'Sem novos alunos este mes';
$revenueDelta = (float) ($monthRevenueDelta ?? 0);
$revenueDeltaPositive = $revenueDelta >= 0;
$revenueDeltaLabel = number_format(abs($revenueDelta), 2, ',', '.') . ' MZN vs mes anterior';
$pendingLabel = (int) ($pendingRequestsThisWeek ?? 0) > 0
    ? (int) $pendingRequestsThisWeek . ' novos nos ultimos 7 dias'
    : 'Nenhum novo pedido recente';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<div class="dash-page">
        <section class="dash-hero">
            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-white/45">Painel do instrutor</p>
                    <h1 class="mt-2">Olá, Professor <?= esc($user->username) ?></h1>
                    <p>Acompanhe cursos, matrículas, pagamentos e pedidos pendentes.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <a href="<?= site_url('instructor/dashboard/novo_curso') ?>" class="dash-btn dash-btn-primary">
                        <i class="bi bi-plus-lg"></i>
                        Criar curso
                    </a>
                    <a href="<?= site_url('instructor/dashboard/meus_cursos') ?>" class="dash-btn dash-btn-soft">
                        <i class="bi bi-gear"></i>
                        Gerir cursos
                    </a>
                    <a href="<?= site_url('instructor/dashboard/meus_estudantes') ?>" class="dash-btn dash-btn-ghost !border-white/15 !text-white hover:!bg-white/10">
                        <i class="bi bi-people"></i>
                        Estudantes
                    </a>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="dash-stat">
                <div class="dash-stat-icon bg-emerald-500/15 text-emerald-500">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <p class="dash-stat-label">Total de cursos</p>
                    <p class="dash-stat-value"><?= (int) ($totalCourses ?? 0) ?></p>
                    <p class="dash-stat-meta"><?= esc($courseDeltaLabel) ?></p>
                </div>
            </div>

            <div class="dash-stat">
                <div class="dash-stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <p class="dash-stat-label">Alunos inscritos</p>
                    <p class="dash-stat-value"><?= (int) ($totalStudents ?? 0) ?></p>
                    <p class="dash-stat-meta"><?= esc($studentDeltaLabel) ?></p>
                </div>
            </div>

            <div class="dash-stat">
                <div class="dash-stat-icon bg-violet-500/15 text-violet-400">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                    <p class="dash-stat-label">Receita mensal</p>
                    <p class="dash-stat-value"><?= number_format((float) ($monthRevenue ?? 0), 2, ',', '.') ?> MZN</p>
                    <p class="dash-stat-meta <?= $revenueDeltaPositive ? 'text-emerald-400' : 'text-rose-400' ?>"><?= esc($revenueDeltaLabel) ?></p>
                </div>
            </div>

            <div class="dash-stat">
                <div class="dash-stat-icon bg-amber-500/15 text-amber-400">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <p class="dash-stat-label">Pedidos pendentes</p>
                    <p class="dash-stat-value"><?= (int) ($pendingRequestsCount ?? 0) ?></p>
                    <p class="dash-stat-meta"><?= esc($pendingLabel) ?></p>
                </div>
            </div>
        </div>

        <div class="dash-card">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="dash-card-title">Cursos em destaque</h2>
                    <p class="dash-card-desc">Maior volume de alunos e receita.</p>
                </div>
                <a href="<?= site_url('instructor/dashboard/meus_cursos') ?>" class="text-sm font-medium text-blue-600 dark:text-blue-400">
                    Ver todos
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <?php if (! empty($featuredCourses)): ?>
                <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <?php foreach ($featuredCourses as $course): ?>
                        <?php
                        $status = strtolower(trim((string) ($course->status_course ?? '')));
                        $statusClass = $status === 'ativo'
                            ? 'bg-emerald-500/15 text-emerald-400'
                            : ($status === 'rascunho'
                                ? 'bg-amber-500/15 text-amber-400'
                                : 'bg-white/10 text-white/60');
                        $progress = max(0, min(100, (int) ($course->avg_progress ?? 0)));
                        ?>
                        <a href="<?= site_url('instructor/dashboard/meus_cursos/editar/' . (int) $course->id_course) ?>" class="block rounded-md border border-slate-200/80 bg-slate-50/80 p-4 transition hover:border-blue-500/40 dark:border-white/10 dark:bg-white/[0.03] group">
                            <div class="mb-4 flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate font-semibold text-slate-800 transition group-hover:text-blue-500 dark:text-white dark:group-hover:text-blue-300">
                                        <?= esc($course->title_course ?? 'Curso sem titulo') ?>
                                    </h3>
                                    <div class="mt-1 flex items-center gap-2 text-sm text-slate-500 dark:text-white/45">
                                        <i class="bi bi-people"></i>
                                        <span><?= (int) ($course->student_count ?? 0) ?> alunos</span>
                                    </div>
                                </div>
                                <span class="dash-badge <?= $statusClass ?>">
                                    <?= esc($course->status_course ?? 'Sem status') ?>
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500 dark:text-white/45">Progresso médio</span>
                                    <span class="font-semibold text-slate-800 dark:text-white"><?= $progress ?>%</span>
                                </div>
                                <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-white/10">
                                    <div class="h-1.5 rounded-full bg-blue-500" style="width: <?= $progress ?>%"></div>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between text-sm">
                                <span class="text-slate-500 dark:text-white/45"><?= (int) ($course->completed_count ?? 0) ?> concluídos</span>
                                <span class="font-semibold text-emerald-500"><?= number_format((float) ($course->revenue_total ?? 0), 2, ',', '.') ?> MZN</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="mt-5 rounded-md border border-dashed border-slate-300 p-8 text-center text-slate-500 dark:border-white/15 dark:text-white/45">
                    Ainda não há cursos suficientes para mostrar destaque.
                </div>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="dash-card">
                <h3 class="dash-card-title">Atividade recente</h3>

                <?php if (! empty($recentActivities)): ?>
                    <div class="mt-5 space-y-3">
                        <?php foreach ($recentActivities as $activity): ?>
                            <div class="flex items-start gap-3 rounded-md p-3 transition hover:bg-slate-50 dark:hover:bg-white/[0.03]">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md <?= esc($activity['icon_bg']) ?>">
                                    <i class="bi <?= esc($activity['icon']) ?> <?= esc($activity['icon_color']) ?>"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-slate-800 dark:text-white">
                                        <?= esc($activity['message']) ?>
                                    </p>
                                    <p class="mt-0.5 text-sm text-slate-500 dark:text-white/45"><?= esc($activity['time_label']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="mt-5 rounded-md border border-dashed border-slate-300 p-8 text-center text-slate-500 dark:border-white/15 dark:text-white/45">
                        Ainda não há movimentações recentes no seu painel.
                    </div>
                <?php endif; ?>
            </div>

            <div class="dash-card">
                <h3 class="dash-card-title">Resumo operacional</h3>

                <div class="mt-5 space-y-5">
                    <?php foreach (($operationalSummary ?? []) as $item): ?>
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <span class="font-medium text-slate-800 dark:text-white"><?= esc($item['title']) ?></span>
                                <span class="text-sm text-slate-500 dark:text-white/45"><?= esc($item['value']) ?></span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-white/10">
                                <div class="h-1.5 <?= esc($item['bar_class']) ?> rounded-full" style="width: <?= (int) ($item['percent'] ?? 0) ?>%"></div>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-500 dark:text-white/45"><?= (int) ($item['percent'] ?? 0) ?>%</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

</div>

<?= $this->endSection() ?>
