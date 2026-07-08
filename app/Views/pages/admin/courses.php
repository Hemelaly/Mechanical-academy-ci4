<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Dashboard - Cursos<?= $this->endSection() ?>

<?= $this->section('courses') ?>
<?php
helper('number');

$metrics = $metrics ?? [];
$filters = $filters ?? ['q' => '', 'status' => '', 'page' => 1, 'per_page' => 10];
$pagination = $pagination ?? ['total' => 0, 'total_pages' => 1, 'page' => 1, 'per_page' => 10];
$charts = $charts ?? ['status_counts' => [], 'top_courses' => [], 'top_revenue' => []];

$statusCounts = $charts['status_counts'] ?? ['Ativo' => 0, 'Rascunho' => 0, 'Arquivado' => 0];
$topCourses = $charts['top_courses'] ?? [];
$topRevenue = $charts['top_revenue'] ?? [];
?>

<div class="space-y-6">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Cursos</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Gerencie cursos, estatísticas e status.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= site_url('admin/dashboard/cursos/export') ?>" class="btn btn-outline">
                <i class="bi bi-download"></i>
                Exportar CSV
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total de cursos</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white"><?= number_format((int)($metrics['total_courses'] ?? 0), 0, ',', '.') ?></p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300">
                    <i class="bi bi-collection text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Cursos ativos</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white"><?= number_format((int)($metrics['active_courses'] ?? 0), 0, ',', '.') ?></p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300">
                    <i class="bi bi-check2-circle text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Estudantes matriculados</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white"><?= number_format((int)($metrics['total_enrolled'] ?? 0), 0, ',', '.') ?></p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300">
                    <i class="bi bi-people text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Receita do mês</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white">MZN <?= number_format((float)($metrics['revenue_month'] ?? 0), 2, ',', '.') ?></p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                    <i class="bi bi-cash-coin text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Charts -->
    <section class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Status dos cursos</h2>
            </div>
            <div class="mt-4 h-52">
                <canvas id="admin-courses-status-chart" class="h-full w-full"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-3 gap-2 text-xs text-slate-500 dark:text-slate-400">
                <div><span class="font-medium text-slate-700 dark:text-slate-200">Ativo:</span> <?= (int)($statusCounts['Ativo'] ?? 0) ?></div>
                <div><span class="font-medium text-slate-700 dark:text-slate-200">Rascunho:</span> <?= (int)($statusCounts['Rascunho'] ?? 0) ?></div>
                <div><span class="font-medium text-slate-700 dark:text-slate-200">Arquivado:</span> <?= (int)($statusCounts['Arquivado'] ?? 0) ?></div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Top cursos (matrículas ativas)</h2>
            </div>
            <div class="mt-4 h-52">
                <canvas id="admin-courses-top-chart" class="h-full w-full"></canvas>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Top faturação</h2>
                <span class="text-xs text-slate-500 dark:text-slate-400">Aprovado</span>
            </div>
            <div class="mt-4 h-52">
                <canvas id="admin-courses-revenue-chart" class="h-full w-full"></canvas>
            </div>
        </div>
    </section>

    <section class="hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Top faturação (aprovado)</h2>
            <span class="text-xs text-slate-500 dark:text-slate-400">Total por curso</span>
        </div>
        <div class="mt-4">
            <canvas id="admin-courses-revenue-chart-legacy" height="220"></canvas>
        </div>
    </section>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4">
        <form method="get" action="<?= current_url() ?>" class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Buscar</label>
                <div class="relative mt-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="bi bi-search text-slate-400"></i>
                    </div>
                    <input name="q" value="<?= esc($filters['q'] ?? '') ?>" type="text" class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 pl-10 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="Título, instrutor ou ID">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Status</label>
                <select name="status" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="">Todos</option>
                    <?php foreach (['Ativo', 'Rascunho', 'Arquivado'] as $opt): ?>
                        <option value="<?= esc($opt) ?>" <?= ($filters['status'] ?? '') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <input type="hidden" name="per_page" value="<?= (int)($filters['per_page'] ?? 10) ?>">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="bi bi-funnel"></i>
                    Filtrar
                </button>
                <a class="btn btn-outline btn-icon" href="<?= current_url() ?>" title="Limpar filtros">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl">
        <div class="relative overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="text-xs uppercase text-slate-600 bg-slate-50 dark:bg-slate-700 dark:text-slate-300">
                    <tr>
                        <th class="px-6 py-3">Curso</th>
                        <th class="px-6 py-3">Instrutor</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Matrículas</th>
                        <th class="px-6 py-3">Faturação</th>
                        <th class="px-6 py-3">Criado</th>
                        <th class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses ?? [])): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Nenhum curso encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (($courses ?? []) as $c): ?>
                            <?php
                            $status = (string)($c['status_course'] ?? '');
                            $badge = 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200';
                            if ($status === 'Ativo') $badge = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200';
                            if ($status === 'Rascunho') $badge = 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200';
                            if ($status === 'Arquivado') $badge = 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200';
                            ?>
                            <tr class="border-t border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-slate-900 dark:text-white"><?= esc($c['title_course'] ?? '') ?></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">#<?= (int)($c['id_course'] ?? 0) ?></p>
                                </td>
                                <td class="px-6 py-4"><?= esc($c['instructor_name'] ?? 'â€”') ?></td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium <?= esc($badge) ?>">
                                        <?= esc($status ?: 'â€”') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4"><?= number_format((int)($c['enrolled'] ?? 0), 0, ',', '.') ?></td>
                                <td class="px-6 py-4 font-medium text-slate-700 dark:text-slate-200">
                                    MZN <?= number_format((float)($c['revenue_total'] ?? 0), 2, ',', '.') ?>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 dark:text-slate-400"><?= esc($c['created_at'] ?? '') ?></td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" class="btn btn-outline btn-sm admin-course-toggle" data-modal-target="course-status-modal" data-modal-toggle="course-status-modal" data-id="<?= (int)($c['id_course'] ?? 0) ?>" data-status="<?= esc($status) ?>">
                                        <i class="bi bi-arrow-repeat"></i>
                                        Alterar status
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400 md:flex-row md:items-center md:justify-between">
            <div>
                Total: <span class="font-medium text-slate-700 dark:text-slate-200"><?= number_format((int)($pagination['total'] ?? 0), 0, ',', '.') ?></span>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php
                $p = (int)($pagination['page'] ?? 1);
                $tp = (int)($pagination['total_pages'] ?? 1);
                $base = current_url() . '?' . http_build_query([
                    'q' => (string)($filters['q'] ?? ''),
                    'status' => (string)($filters['status'] ?? ''),
                    'per_page' => (int)($filters['per_page'] ?? 10),
                ]);
                $mk = static fn(int $page) => $base . '&page=' . $page;
                ?>
                <a class="btn btn-outline btn-sm <?= $p <= 1 ? 'pointer-events-none opacity-50' : '' ?>" href="<?= esc($mk(max(1, $p - 1))) ?>">Anterior</a>
                <span class="px-2 py-1.5 text-xs">Página <?= $p ?> / <?= $tp ?></span>
                <a class="btn btn-outline btn-sm <?= $p >= $tp ? 'pointer-events-none opacity-50' : '' ?>" href="<?= esc($mk(min($tp, $p + 1))) ?>">Próxima</a>
            </div>
        </div>
    </div>
</div>

<!-- Flowbite Modal: Alterar status -->
<div id="course-status-modal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto overflow-x-hidden p-4">
    <div class="relative w-full max-w-md">
        <div class="relative rounded-2xl bg-white shadow dark:bg-slate-800">
            <div class="flex items-start justify-between rounded-t border-b border-slate-200 p-4 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Alterar status</h3>
                <button type="button" class="ml-auto inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white" data-modal-hide="course-status-modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="space-y-4 p-4">
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    Selecione o novo status do curso.
                </div>
                <input type="hidden" id="course-status-id" value="">
                <div>
                    <label for="course-status-select" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Status</label>
                    <select id="course-status-select" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <option value="Ativo">Ativo</option>
                        <option value="Rascunho">Rascunho</option>
                        <option value="Arquivado">Arquivado</option>
                    </select>
                </div>
                <div id="course-status-feedback" class="hidden rounded-lg bg-rose-50 p-3 text-sm text-rose-700 dark:bg-rose-900/30 dark:text-rose-200"></div>
            </div>
            <div class="flex items-center justify-end gap-2 rounded-b border-t border-slate-200 p-4 dark:border-slate-700">
                <button type="button" class="btn btn-outline" data-modal-hide="course-status-modal">Cancelar</button>
                <button type="button" id="course-status-save" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="admin-courses-toast" class="fixed right-4 top-4 z-[60] hidden w-full max-w-sm rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-700 shadow-lg dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
    <div class="flex items-start gap-3">
        <div id="admin-courses-toast-icon" class="mt-0.5 text-emerald-600 dark:text-emerald-400">
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="flex-1">
            <p id="admin-courses-toast-title" class="font-medium">Sucesso</p>
            <p id="admin-courses-toast-text" class="mt-0.5 text-slate-500 dark:text-slate-400">Atualizado.</p>
        </div>
        <button type="button" id="admin-courses-toast-close" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700">
            <i class="bi bi-x"></i>
        </button>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('page_scripts') ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const statusCounts = <?= json_encode($statusCounts, JSON_UNESCAPED_UNICODE) ?>;
            const topCourses = <?= json_encode($topCourses, JSON_UNESCAPED_UNICODE) ?>;
            const topRevenue = <?= json_encode($topRevenue, JSON_UNESCAPED_UNICODE) ?>;
            const toggleEndpoint = <?= json_encode(site_url('admin/dashboard/cursos/toggle-status')) ?>;
            const csrfName = <?= json_encode(csrf_token()) ?>;
            let csrfHash = <?= json_encode(csrf_hash()) ?>;

            const isDark = () => document.documentElement.classList.contains('dark');
            const chartText = () => (isDark() ? '#e2e8f0' : '#334155');
            const gridColor = () => (isDark() ? 'rgba(148,163,184,0.18)' : 'rgba(148,163,184,0.35)');

            let statusChart = null;
            let topChart = null;
            let revChart = null;

            const renderCharts = () => {
                const statusCtx = document.getElementById('admin-courses-status-chart');
                if (statusChart) {
                    statusChart.destroy();
                    statusChart = null;
                }

                if (statusCtx) {
                    statusChart = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Ativo', 'Rascunho', 'Arquivado'],
                        datasets: [{
                            data: [statusCounts.Ativo || 0, statusCounts.Rascunho || 0, statusCounts.Arquivado || 0],
                            backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { labels: { color: chartText() } }
                        }
                    }
                    });
                }

                const topCtx = document.getElementById('admin-courses-top-chart');
                if (topChart) {
                    topChart.destroy();
                    topChart = null;
                }

                if (topCtx) {
                    const labels = topCourses.map(r => String(r.title || '').slice(0, 28));
                    const values = topCourses.map(r => Number(r.students || 0));
                    topChart = new Chart(topCtx, {
                    type: 'bar',
                    data: { labels, datasets: [{ label: 'Matrículas', data: values, backgroundColor: '#3b82f6', borderRadius: 10 }] },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { ticks: { color: chartText() }, grid: { color: gridColor() } },
                            y: { ticks: { color: chartText() }, grid: { display: false } },
                        },
                        plugins: { legend: { display: false } }
                    }
                    });
                }

                const revCtx = document.getElementById('admin-courses-revenue-chart');
                if (revChart) {
                    revChart.destroy();
                    revChart = null;
                }

                if (revCtx) {
                    const labels = topRevenue.map(r => String(r.title || '').slice(0, 28));
                    const values = topRevenue.map(r => Number(r.revenue || 0));
                    revChart = new Chart(revCtx, {
                    type: 'bar',
                    data: { labels, datasets: [{ label: 'Faturação (MZN)', data: values, backgroundColor: '#f59e0b', borderRadius: 10 }] },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { ticks: { color: chartText() }, grid: { color: gridColor() } },
                            y: { ticks: { color: chartText() }, grid: { display: false } },
                        },
                        plugins: { legend: { display: false } }
                    }
                    });
                }
            };

            renderCharts();
            document.addEventListener('themechange', renderCharts);

            const postAction = (url, payload) => {
                payload[csrfName] = csrfHash;
                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams(payload).toString()
                }).then(async (res) => {
                    const data = await res.json();
                    if (data.csrf) csrfHash = data.csrf;
                    if (!res.ok) throw data;
                    return data;
                });
            };

            const toast = document.getElementById('admin-courses-toast');
            const toastTitle = document.getElementById('admin-courses-toast-title');
            const toastText = document.getElementById('admin-courses-toast-text');
            const toastIcon = document.getElementById('admin-courses-toast-icon');
            const toastClose = document.getElementById('admin-courses-toast-close');
            let toastTimer = null;

            const showToast = (type, title, text) => {
                if (!toast) return;
                if (toastTimer) clearTimeout(toastTimer);
                toast.classList.remove('hidden');
                if (toastTitle) toastTitle.textContent = title || (type === 'error' ? 'Erro' : 'Sucesso');
                if (toastText) toastText.textContent = text || '';
                if (toastIcon) {
                    toastIcon.className = 'mt-0.5 ' + (type === 'error' ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400');
                    toastIcon.innerHTML = type === 'error' ? '<i class="bi bi-exclamation-triangle"></i>' : '<i class="bi bi-check-circle"></i>';
                }
                toastTimer = setTimeout(() => toast.classList.add('hidden'), 3500);
            };

            toastClose?.addEventListener('click', () => toast?.classList.add('hidden'));

            const modalIdInput = document.getElementById('course-status-id');
            const modalSelect = document.getElementById('course-status-select');
            const modalSave = document.getElementById('course-status-save');
            const modalFeedback = document.getElementById('course-status-feedback');

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.admin-course-toggle');
                if (!btn) return;
                const id = String(btn.dataset.id || '').trim();
                const currentStatus = String(btn.dataset.status || 'Ativo');
                if (modalIdInput) modalIdInput.value = id;
                if (modalSelect) modalSelect.value = currentStatus;
                if (modalFeedback) modalFeedback.classList.add('hidden');
            });

            modalSave?.addEventListener('click', () => {
                const id = String(modalIdInput?.value || '').trim();
                const status = String(modalSelect?.value || '').trim();
                if (!id || !status) return;

                modalSave.disabled = true;
                postAction(toggleEndpoint, { id, status })
                    .then((data) => {
                        showToast('success', 'Atualizado', data.message || 'Status atualizado.');
                        document.querySelector('[data-modal-hide="course-status-modal"]')?.click();
                        setTimeout(() => window.location.reload(), 450);
                    })
                    .catch((err) => {
                        const msg = err?.message || 'Falha ao atualizar status.';
                        if (modalFeedback) {
                            modalFeedback.textContent = msg;
                            modalFeedback.classList.remove('hidden');
                        }
                        showToast('error', 'Erro', msg);
                    })
                    .finally(() => {
                        modalSave.disabled = false;
                    });
            });
        })();
    </script>
<?= $this->endSection() ?>

