<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Dashboard do Admin<?= $this->endSection() ?>

<?= $this->section('home_admin') ?>
<?php
helper('number');

$userName = $userName ?? ($user->username ?? 'Administrador');

$stats = $stats ?? [
    ['label' => 'Usuarios ativos', 'value' => 1248, 'delta' => 12.4, 'icon' => 'bi-people', 'tone' => 'blue'],
    ['label' => 'Novas inscricoes', 'value' => 84, 'delta' => 4.1, 'icon' => 'bi-journal-check', 'tone' => 'emerald'],
    ['label' => 'Receita do mes', 'value' => 256400, 'delta' => -2.7, 'icon' => 'bi-cash-coin', 'tone' => 'amber', 'prefix' => 'MZN '],
    ['label' => 'Cursos ativos', 'value' => 38, 'delta' => 1.8, 'icon' => 'bi-book', 'tone' => 'purple'],
];

$activity = $activity ?? [
    ['title' => 'Novo instrutor aprovado', 'time' => 'Ha 12 minutos', 'icon' => 'bi-person-badge', 'tone' => 'blue'],
    ['title' => 'Pagamento confirmado', 'time' => 'Ha 1 hora', 'icon' => 'bi-credit-card', 'tone' => 'emerald'],
    ['title' => 'Curso publicado', 'time' => 'Ha 3 horas', 'icon' => 'bi-megaphone', 'tone' => 'amber'],
    ['title' => 'Relatorio semanal gerado', 'time' => 'Ontem', 'icon' => 'bi-bar-chart', 'tone' => 'purple'],
];

$popularCourses = $popularCourses ?? [
    ['name' => 'Marketing Digital 101', 'students' => 320, 'progress' => 78, 'tone' => 'blue'],
    ['name' => 'Gestao Financeira', 'students' => 210, 'progress' => 62, 'tone' => 'emerald'],
    ['name' => 'UI/UX para iniciantes', 'students' => 156, 'progress' => 54, 'tone' => 'purple'],
];

$quickActions = $quickActions ?? [
    ['label' => 'Novo curso', 'href' => site_url('/admin/dashboard/cursos'), 'icon' => 'bi-plus-circle'],
    ['label' => 'Aprovar usuarios', 'href' => site_url('/admin/dashboard/estudantes'), 'icon' => 'bi-person-check'],
    ['label' => 'Relatorios', 'href' => '#', 'icon' => 'bi-file-earmark-text'],
    ['label' => 'Configuracoes', 'href' => site_url('/admin/dashboard/perfil'), 'icon' => 'bi-gear'],
];
?>

<div class="space-y-6">
    <!-- Banner Hero -->
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 p-8 text-white">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute right-0 top-0 h-64 w-64 translate-x-32 -translate-y-32 rounded-full bg-white"></div>
            <div class="absolute bottom-0 left-0 h-48 w-48 -translate-x-24 translate-y-24 rounded-full bg-cyan-300"></div>
        </div>

        <div class="relative z-10">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold">Olá, <?= esc($userName) ?>!</h1>
                    <p class="mt-2 text-sm text-blue-100 leading-relaxed">
                        Aqui está um resumo das principais métricas e actividades do painel administrativo.
                    </p>
                </div>
                <div class="hidden md:flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                    <i class="bi bi-bar-chart text-2xl text-white"></i>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button
                    class="flex items-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-semibold text-blue-600 shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:bg-white/95 hover:shadow-xl">
                    <i class="bi bi-calendar-event text-sm"></i>
                    Últimos 30 dias
                </button>
                <button
                    class="flex items-center gap-2 rounded-xl border border-white/20 bg-white/15 px-6 py-3.5 text-sm font-semibold text-white backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:bg-white/25 hover:border-white/30">
                    <i class="bi bi-download text-sm"></i>
                    Exportar
                </button>
            </div>
        </div>
    </section>

    <!-- KPIs -->
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php foreach ($stats as $card): ?>
            <?php
            $delta = (float) ($card['delta'] ?? 0);
            $isUp = $delta >= 0;
            $value = $card['value'] ?? 0;
            $prefix = $card['prefix'] ?? '';
            $tone = $card['tone'] ?? 'blue';
            $valueFormatted = is_numeric($value) ? number_format((float) $value, 0, ',', '.') : $value;
            ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="flex items-start justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-<?= esc($tone) ?>-100 text-<?= esc($tone) ?>-600 dark:bg-<?= esc($tone) ?>-900/40 dark:text-<?= esc($tone) ?>-300">
                        <i class="bi <?= esc($card['icon']) ?> text-lg"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium <?= $isUp ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' ?>">
                        <i class="bi <?= $isUp ? 'bi-arrow-up-right' : 'bi-arrow-down-right' ?>"></i>
                        <?= number_format(abs($delta), 1, ',', '.') ?>%
                    </span>
                </div>
                <p class="mt-4 text-sm text-slate-500 dark:text-slate-400"><?= esc($card['label']) ?></p>
                <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white">
                    <?= $prefix . $valueFormatted ?>
                </p>
            </div>
        <?php endforeach; ?>
    </section>

    <!-- Main grid -->
    <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Activity -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800 lg:col-span-1">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Atividade recente</h2>
                <button class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">
                    Ver tudo
                </button>
            </div>
            <ol class="mt-5 space-y-4">
                <?php foreach ($activity as $item): ?>
                    <?php $tone = $item['tone'] ?? 'blue'; ?>
                    <li class="ml-4">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-<?= esc($tone) ?>-100 text-<?= esc($tone) ?>-600 dark:bg-<?= esc($tone) ?>-900/40 dark:text-<?= esc($tone) ?>-300">
                                    <i class="bi <?= esc($item['icon']) ?>"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white"><?= esc($item['title']) ?></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400"><?= esc($item['time']) ?></p>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>

        <!-- Overview -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800 lg:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Resumo operacional</h2>
                <div class="inline-flex rounded-lg border border-slate-200 bg-slate-50 p-1 text-xs font-medium text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                    <button class="rounded-md px-3 py-1 text-blue-600 dark:text-blue-400">Usuarios</button>
                    <button class="rounded-md px-3 py-1 hover:text-slate-900 dark:hover:text-white">Receita</button>
                    <button class="rounded-md px-3 py-1 hover:text-slate-900 dark:hover:text-white">Engajamento</button>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Taxa de conversao</p>
                    <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">4.8%</p>
                    <div class="mt-4 h-2 rounded-full bg-slate-200 dark:bg-slate-700">
                        <div class="h-2 rounded-full bg-blue-500" style="width: 48%;"></div>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Cursos com alta demanda</p>
                    <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">12 cursos</p>
                    <div class="mt-4 h-2 rounded-full bg-slate-200 dark:bg-slate-700">
                        <div class="h-2 rounded-full bg-emerald-500" style="width: 72%;"></div>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Taxa de conclusao</p>
                    <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">68%</p>
                    <div class="mt-4 h-2 rounded-full bg-slate-200 dark:bg-slate-700">
                        <div class="h-2 rounded-full bg-purple-500" style="width: 68%;"></div>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Suporte respondido</p>
                    <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">92%</p>
                    <div class="mt-4 h-2 rounded-full bg-slate-200 dark:bg-slate-700">
                        <div class="h-2 rounded-full bg-amber-500" style="width: 92%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses + quick actions -->
    <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800 lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Cursos populares</h2>
                <a href="<?= site_url('/admin/dashboard/cursos') ?>" class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">Ver todos</a>
            </div>
            <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                <table class="w-full text-left text-sm text-slate-500 dark:text-slate-400">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-600 dark:bg-slate-900 dark:text-slate-300">
                        <tr>
                            <th class="px-4 py-3">Curso</th>
                            <th class="px-4 py-3">Estudantes</th>
                            <th class="px-4 py-3">Progresso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($popularCourses as $course): ?>
                            <?php $tone = $course['tone'] ?? 'blue'; ?>
                            <tr class="border-t border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                                <td class="px-4 py-4 font-medium text-slate-900 dark:text-white">
                                    <?= esc($course['name']) ?>
                                </td>
                                <td class="px-4 py-4"><?= number_format((int) ($course['students'] ?? 0), 0, ',', '.') ?></td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-full rounded-full bg-slate-200 dark:bg-slate-700">
                                            <div class="h-2 rounded-full bg-<?= esc($tone) ?>-500" style="width: <?= (int) ($course['progress'] ?? 0) ?>%;"></div>
                                        </div>
                                        <span class="text-xs text-slate-500 dark:text-slate-400"><?= (int) ($course['progress'] ?? 0) ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Acoes rapidas</h2>
            <div class="mt-4 grid grid-cols-2 gap-3">
                <?php foreach ($quickActions as $qa): ?>
                    <a href="<?= esc($qa['href']) ?>" class="group rounded-xl border border-slate-200 bg-slate-50 p-4 text-center transition hover:border-blue-500 hover:bg-blue-50 dark:border-slate-700 dark:bg-slate-900 dark:hover:bg-blue-900/20">
                        <i class="bi <?= esc($qa['icon']) ?> text-2xl text-blue-600 dark:text-blue-400"></i>
                        <p class="mt-2 text-sm font-medium text-slate-700 group-hover:text-blue-700 dark:text-slate-300 dark:group-hover:text-blue-300">
                            <?= esc($qa['label']) ?>
                        </p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
