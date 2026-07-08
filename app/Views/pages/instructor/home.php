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

<div class="space-y-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-900 rounded-2xl p-6 md:p-8 text-white shadow-lg">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <h1 class="text-2xl md:text-3xl font-bold mb-3">
                        Ola, Professor <?= esc($user->username) ?>!
                    </h1>
                    <p class="text-blue-100 text-sm md:text-base max-w-2xl leading-relaxed">
                        Acompanhe cursos, matriculas, pagamentos e pedidos pendentes com dados reais da plataforma.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="<?= site_url('instructor/dashboard/novo_curso') ?>"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <i class="bi bi-plus-lg"></i>
                        Criar Novo Curso
                    </a>
                    <a href="<?= site_url('instructor/dashboard/meus_cursos') ?>"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white/10 text-white font-semibold rounded-xl hover:bg-white/20 transition-all duration-300 border border-white/20">
                        <i class="bi bi-gear"></i>
                        Gerenciar Cursos
                    </a>
                    <a href="<?= site_url('instructor/dashboard/meus_estudantes') ?>"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-black/20 text-white font-semibold rounded-xl hover:bg-black/30 transition-all duration-300 border border-white/10">
                        <i class="bi bi-people"></i>
                        Ver Estudantes
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Total de Cursos</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= (int) ($totalCourses ?? 0) ?></h3>
                        <div class="flex items-center gap-1 mt-1">
                            <i class="bi bi-journal-text text-green-500 text-sm"></i>
                            <span class="text-green-500 text-sm font-medium"><?= esc($courseDeltaLabel) ?></span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center">
                        <i class="bi bi-journal-text text-green-600 dark:text-green-400 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Alunos Inscritos</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= (int) ($totalStudents ?? 0) ?></h3>
                        <div class="flex items-center gap-1 mt-1">
                            <i class="bi bi-person-plus text-blue-500 text-sm"></i>
                            <span class="text-blue-500 text-sm font-medium"><?= esc($studentDeltaLabel) ?></span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                        <i class="bi bi-people text-blue-600 dark:text-blue-400 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Receita Mensal</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format((float) ($monthRevenue ?? 0), 2, ',', '.') ?> MZN</h3>
                        <div class="flex items-center gap-1 mt-1">
                            <i class="bi <?= $revenueDeltaPositive ? 'bi-arrow-up-short text-green-500' : 'bi-arrow-down-short text-red-500' ?> text-sm"></i>
                            <span class="<?= $revenueDeltaPositive ? 'text-green-500' : 'text-red-500' ?> text-sm font-medium"><?= esc($revenueDeltaLabel) ?></span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center">
                        <i class="bi bi-currency-dollar text-purple-600 dark:text-purple-400 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Pedidos Pendentes</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= (int) ($pendingRequestsCount ?? 0) ?></h3>
                        <div class="flex items-center gap-1 mt-1">
                            <i class="bi bi-hourglass-split text-amber-500 text-sm"></i>
                            <span class="text-amber-500 text-sm font-medium"><?= esc($pendingLabel) ?></span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900 rounded-xl flex items-center justify-center">
                        <i class="bi bi-clock-history text-amber-600 dark:text-amber-400 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
                <div>
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white">Cursos em Destaque</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Cursos com maior volume de alunos e receita.</p>
                </div>
                <a href="<?= site_url('instructor/dashboard/meus_cursos') ?>" class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 font-semibold hover:text-blue-700 dark:hover:text-blue-300 transition-colors text-sm">
                    Ver todos os cursos
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <?php if (! empty($featuredCourses)): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($featuredCourses as $course): ?>
                        <?php
                        $status = strtolower(trim((string) ($course->status_course ?? '')));
                        $statusClass = $status === 'ativo'
                            ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300'
                            : ($status === 'rascunho'
                                ? 'bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300'
                                : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300');
                        $progress = max(0, min(100, (int) ($course->avg_progress ?? 0)));
                        ?>
                        <a href="<?= site_url('instructor/dashboard/meus_cursos/editar/' . (int) $course->id_course) ?>" class="block bg-slate-50 dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 group">
                            <div class="flex justify-between items-start mb-4 gap-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-1 truncate">
                                        <?= esc($course->title_course ?? 'Curso sem titulo') ?>
                                    </h3>
                                    <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-sm">
                                        <i class="bi bi-people"></i>
                                        <span><?= (int) ($course->student_count ?? 0) ?> alunos</span>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                    <?= esc($course->status_course ?? 'Sem status') ?>
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-600 dark:text-slate-400">Progresso medio</span>
                                    <span class="font-semibold text-slate-800 dark:text-white"><?= $progress ?>%</span>
                                </div>
                                <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-2 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full" style="width: <?= $progress ?>%"></div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center mt-4 text-sm">
                                <span class="text-slate-500 dark:text-slate-400"><?= (int) ($course->completed_count ?? 0) ?> concluidos</span>
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400"><?= number_format((float) ($course->revenue_total ?? 0), 2, ',', '.') ?> MZN</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-600 p-8 text-center text-slate-500 dark:text-slate-400">
                    Ainda nao ha cursos suficientes para mostrar destaque.
                </div>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-6">Atividade Recente</h3>

                <?php if (! empty($recentActivities)): ?>
                    <div class="space-y-4">
                        <?php foreach ($recentActivities as $activity): ?>
                            <div class="flex items-start gap-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <div class="w-10 h-10 <?= esc($activity['icon_bg']) ?> rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="bi <?= esc($activity['icon']) ?> <?= esc($activity['icon_color']) ?>"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-800 dark:text-white mb-1">
                                        <?= esc($activity['message']) ?>
                                    </p>
                                    <p class="text-slate-500 dark:text-slate-400 text-sm"><?= esc($activity['time_label']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-600 p-8 text-center text-slate-500 dark:text-slate-400">
                        Ainda nao ha movimentacoes recentes no seu painel.
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-6">Resumo Operacional</h3>

                <div class="space-y-6">
                    <?php foreach (($operationalSummary ?? []) as $item): ?>
                        <div>
                            <div class="flex justify-between items-center mb-2 gap-3">
                                <span class="font-semibold text-slate-800 dark:text-white"><?= esc($item['title']) ?></span>
                                <span class="text-slate-500 dark:text-slate-400 text-sm font-medium"><?= esc($item['value']) ?></span>
                            </div>
                            <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-2 <?= esc($item['bar_class']) ?> rounded-full" style="width: <?= (int) ($item['percent'] ?? 0) ?>%"></div>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2"><?= (int) ($item['percent'] ?? 0) ?>%</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

</div>

<?= $this->endSection() ?>
