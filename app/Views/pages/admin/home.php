<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Dashboard do Admin<?= $this->endSection() ?>

<?= $this->section('home_admin') ?>
<?php
helper('number');

$userName = $userName ?? ($user->username ?? 'Administrador');

$stats = $stats ?? [
    ['label' => 'Usuarios ativos', 'value' => 1248, 'delta' => 12.4, 'icon' => 'bi-people', 'tone' => 'blue'],
    ['label' => 'Novas inscricoes', 'value' => 84, 'delta' => 4.1, 'icon' => 'bi-journal-check', 'tone' => 'emerald'],
    ['label' => 'Receita do mes', 'value' => 0, 'delta' => 0.0, 'icon' => 'bi-cash-coin', 'tone' => 'amber', 'prefix' => 'MZN '],
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

$charts = $charts ?? [
    'revenue_12m' => ['labels' => [], 'data' => []],
    'enrollments_14d' => ['labels' => [], 'data' => []],
];

$quickActions = $quickActions ?? [
    ['label' => 'Novo curso', 'href' => site_url('/admin/dashboard/cursos'), 'icon' => 'bi-plus-circle'],
    ['label' => 'Aprovar usuarios', 'href' => site_url('/admin/dashboard/estudantes'), 'icon' => 'bi-person-check'],
    ['label' => 'Relatorios', 'href' => '#', 'icon' => 'bi-file-earmark-text'],
    ['label' => 'Configuracoes', 'href' => site_url('/admin/dashboard/perfil'), 'icon' => 'bi-gear'],
];

$tonePalette = [
    'blue' => [
        'bg' => 'bg-blue-100',
        'text' => 'text-blue-600',
        'darkBg' => 'dark:bg-blue-900/40',
        'darkText' => 'dark:text-blue-300',
        'bar' => 'bg-blue-500',
    ],
    'emerald' => [
        'bg' => 'bg-emerald-100',
        'text' => 'text-emerald-600',
        'darkBg' => 'dark:bg-emerald-900/40',
        'darkText' => 'dark:text-emerald-300',
        'bar' => 'bg-emerald-500',
    ],
    'amber' => [
        'bg' => 'bg-amber-100',
        'text' => 'text-amber-600',
        'darkBg' => 'dark:bg-amber-900/40',
        'darkText' => 'dark:text-amber-300',
        'bar' => 'bg-amber-500',
    ],
    'purple' => [
        'bg' => 'bg-purple-100',
        'text' => 'text-purple-600',
        'darkBg' => 'dark:bg-purple-900/40',
        'darkText' => 'dark:text-purple-300',
        'bar' => 'bg-purple-500',
    ],
    'indigo' => [
        'bg' => 'bg-indigo-100',
        'text' => 'text-indigo-600',
        'darkBg' => 'dark:bg-indigo-900/40',
        'darkText' => 'dark:text-indigo-300',
        'bar' => 'bg-indigo-500',
    ],
    'rose' => [
        'bg' => 'bg-rose-100',
        'text' => 'text-rose-600',
        'darkBg' => 'dark:bg-rose-900/40',
        'darkText' => 'dark:text-rose-300',
        'bar' => 'bg-rose-500',
    ],
    'slate' => [
        'bg' => 'bg-slate-200',
        'text' => 'text-slate-700',
        'darkBg' => 'dark:bg-slate-700',
        'darkText' => 'dark:text-slate-200',
        'bar' => 'bg-slate-500',
    ],
];
?>

<div class="dash-page">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-slate-900 dark:text-white sm:text-2xl">
                Olá, <?= esc($userName) ?>
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Visão geral da plataforma</p>
        </div>
        <a href="<?= site_url('/admin/dashboard/financas') ?>" class="dash-btn dash-btn-primary self-start">
            <i class="bi bi-cash-coin"></i>
            Finanças
        </a>
    </div>

    <section class="grid grid-cols-2 gap-3 xl:grid-cols-4">
        <?php foreach ($stats as $card): ?>
            <?php
            $delta = (float) ($card['delta'] ?? 0);
            $isUp = $delta >= 0;
            $value = $card['value'] ?? 0;
            $prefix = $card['prefix'] ?? '';
            $toneKey = $card['tone'] ?? 'blue';
            $tone = $tonePalette[$toneKey] ?? $tonePalette['blue'];
            $valueFormatted = is_numeric($value)
                ? number_format((float) $value, $prefix ? 2 : 0, ',', '.')
                : $value;
            ?>
            <div class="dash-card !p-4">
                <div class="flex items-center justify-between gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-md <?= esc($tone['bg']) ?> <?= esc($tone['text']) ?> <?= esc($tone['darkBg']) ?> <?= esc($tone['darkText']) ?>">
                        <i class="bi <?= esc($card['icon']) ?>"></i>
                    </span>
                    <span class="text-[11px] font-medium <?= $isUp ? 'text-emerald-500' : 'text-rose-500' ?>">
                        <?= $isUp ? '+' : '-' ?><?= number_format(abs($delta), 1, ',', '.') ?>%
                    </span>
                </div>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">
                    <?= $prefix . $valueFormatted ?>
                </p>
                <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400"><?= esc($card['label']) ?></p>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="grid grid-cols-1 items-start gap-4 lg:grid-cols-2">
        <div class="dash-card">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Receita · 12 meses</h2>
            <div class="mt-3 h-[200px]">
                <canvas id="admin-dashboard-revenue-chart" class="!h-full !w-full"></canvas>
            </div>
        </div>
        <div class="dash-card">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Matrículas · 14 dias</h2>
            <div class="mt-3 h-[200px]">
                <canvas id="admin-dashboard-enrollments-chart" class="!h-full !w-full"></canvas>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 items-stretch gap-4 lg:grid-cols-2">
        <div class="dash-card min-w-0">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Cursos populares</h2>
                <a href="<?= site_url('/admin/dashboard/cursos') ?>" class="text-xs font-medium text-blue-600 dark:text-blue-400">Ver todos</a>
            </div>
            <div class="mt-4 space-y-4">
                <?php foreach ($popularCourses as $course): ?>
                    <?php
                    $toneKey = $course['tone'] ?? 'blue';
                    $tone = $tonePalette[$toneKey] ?? $tonePalette['blue'];
                    $pct = (int) ($course['progress'] ?? 0);
                    ?>
                    <div>
                        <div class="mb-1.5 flex items-center justify-between gap-3">
                            <p class="min-w-0 truncate text-sm font-medium text-slate-800 dark:text-slate-100"><?= esc($course['name']) ?></p>
                            <span class="shrink-0 text-xs text-slate-500 dark:text-slate-400"><?= number_format((int) ($course['students'] ?? 0), 0, ',', '.') ?> alunos</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-1.5 rounded-full <?= esc($tone['bar']) ?>" style="width: <?= max(0, min(100, $pct)) ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($popularCourses)): ?>
                    <p class="py-2 text-sm text-slate-500 dark:text-slate-400">Sem cursos para mostrar.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="dash-card flex min-w-0 flex-col">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Actividade</h2>
            <ul class="mt-3 divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach (array_slice($activity, 0, 5) as $item): ?>
                    <li class="flex items-start gap-3 py-2.5 first:pt-0">
                        <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-blue-500"></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-800 dark:text-slate-100"><?= esc($item['title']) ?></p>
                            <p class="mt-0.5 text-[11px] text-slate-500 dark:text-slate-400"><?= esc($item['time']) ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($activity)): ?>
                    <li class="py-2 text-sm text-slate-500 dark:text-slate-400">Sem actividade recente.</li>
                <?php endif; ?>
            </ul>
            <div class="mt-auto grid grid-cols-2 gap-2 border-t border-slate-200 pt-4 dark:border-slate-700">
                <?php foreach (array_slice($quickActions, 0, 4) as $qa): ?>
                    <a href="<?= esc($qa['href']) ?>" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-slate-200 px-3 py-2.5 text-center text-xs font-medium text-slate-700 transition hover:border-blue-500/40 hover:text-blue-600 dark:border-slate-600 dark:text-slate-200 dark:hover:border-blue-400/40 dark:hover:text-blue-300">
                        <i class="bi <?= esc($qa['icon']) ?>"></i>
                        <span><?= esc($qa['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('page_scripts') ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const charts = <?= json_encode($charts, JSON_UNESCAPED_UNICODE) ?>;
            const isDark = () => document.documentElement.classList.contains('dark');
            const chartText = () => (isDark() ? '#e2e8f0' : '#334155');
            const gridColor = () => (isDark() ? 'rgba(148,163,184,0.18)' : 'rgba(148,163,184,0.35)');

            let revenueChart = null;
            let enrollChart = null;

            const renderCharts = () => {
                const revenueCanvas = document.getElementById('admin-dashboard-revenue-chart');
                if (revenueChart) {
                    revenueChart.destroy();
                    revenueChart = null;
                }

                if (revenueCanvas && charts?.revenue_12m?.labels?.length) {
                    revenueChart = new Chart(revenueCanvas, {
                    type: 'line',
                    data: {
                        labels: charts.revenue_12m.labels,
                        datasets: [{
                            label: 'Receita (MZN)',
                            data: charts.revenue_12m.data || [],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.15)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { ticks: { color: chartText() }, grid: { display: false } },
                            y: { ticks: { color: chartText() }, grid: { color: gridColor() }, beginAtZero: true },
                        },
                        plugins: { legend: { display: false } }
                    }
                    });
                }

                const enrollCanvas = document.getElementById('admin-dashboard-enrollments-chart');
                if (enrollChart) {
                    enrollChart.destroy();
                    enrollChart = null;
                }

                if (enrollCanvas && charts?.enrollments_14d?.labels?.length) {
                    enrollChart = new Chart(enrollCanvas, {
                    type: 'bar',
                    data: {
                        labels: charts.enrollments_14d.labels,
                        datasets: [{
                            label: 'Matrículas',
                            data: charts.enrollments_14d.data || [],
                            backgroundColor: '#10b981',
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { ticks: { color: chartText() }, grid: { display: false } },
                            y: {
                                beginAtZero: true,
                                ticks: { color: chartText(), precision: 0 },
                                grid: { color: gridColor() },
                            },
                        },
                        plugins: { legend: { display: false } }
                    }
                    });
                }
            };

            renderCharts();
            document.addEventListener('themechange', renderCharts);
        })();
    </script>
<?= $this->endSection() ?>
