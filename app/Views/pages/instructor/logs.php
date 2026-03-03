<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Dashboard - Logs<?= $this->endSection() ?>

<?= $this->section('logs') ?>
<div class="min-w-0 space-y-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Logs de Atividade</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Acompanhe suas acoes recentes no painel.</p>
    </div>

    <div class="min-w-0 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl">
        <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
                <div class="md:col-span-2">
                    <label for="logs-search" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Buscar</label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="bi bi-search text-slate-400"></i>
                        </div>
                        <input id="logs-search" type="text" class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 pl-10 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="Evento, mensagem, rota...">
                    </div>
                </div>
                <div>
                    <label for="logs-level" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nivel</label>
                    <select id="logs-level" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <option value="">Todos</option>
                        <option value="debug">Debug</option>
                        <option value="info">Info</option>
                        <option value="notice">Notice</option>
                        <option value="warning">Warning</option>
                        <option value="error">Error</option>
                        <option value="critical">Critical</option>
                        <option value="alert">Alert</option>
                        <option value="emergency">Emergency</option>
                    </select>
                </div>
                <div>
                    <label for="logs-date-from" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Data inicial</label>
                    <input id="logs-date-from" type="date" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label for="logs-date-to" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Data final</label>
                    <input id="logs-date-to" type="date" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label for="logs-per-page" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Por pagina</label>
                    <select id="logs-per-page" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <a id="logs-export" href="#" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700">
                    <i class="bi bi-download"></i>
                    Exportar CSV
                </a>
            </div>
        </div>

        <div class="relative w-full max-w-full overflow-x-auto">
            <table id="instructor-logs-table" class="w-full min-w-full text-left text-sm text-slate-500 dark:text-slate-400">
                <thead class="text-xs uppercase text-slate-600 bg-slate-50 dark:bg-slate-700 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">Data</th>
                        <th scope="col" class="px-6 py-3">Nivel</th>
                        <th scope="col" class="px-6 py-3">Evento</th>
                        <th scope="col" class="px-6 py-3">Mensagem</th>
                        <th scope="col" class="px-6 py-3">Rota</th>
                        <th scope="col" class="px-6 py-3">IP</th>
                        <th scope="col" class="px-6 py-3 text-right">Contexto</th>
                    </tr>
                </thead>
                <tbody id="logs-table-body"></tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400 md:flex-row md:items-center md:justify-between">
            <div id="logs-summary">Carregando...</div>
            <div id="logs-pagination" class="flex flex-wrap gap-2"></div>
        </div>
    </div>
</div>

<div id="logContextModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl max-w-4xl w-full">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-slate-700">
            <h4 class="text-sm font-semibold text-slate-800 dark:text-white">Contexto do log</h4>
            <button type="button" id="logContextClose" class="text-slate-500 hover:text-slate-700 dark:text-slate-300">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="p-4">
            <pre id="logContextBody" class="w-full max-h-[70vh] overflow-auto rounded-xl bg-slate-100 dark:bg-slate-900 p-4 text-xs text-slate-700 dark:text-slate-200 whitespace-pre-wrap break-all"></pre>
        </div>
    </div>
</div>

<script>
    (function () {
        const endpoint = <?= json_encode(site_url('instructor/dashboard/logs/data')) ?>;
        const exportEndpoint = <?= json_encode(site_url('instructor/dashboard/logs/export')) ?>;
        const tableBody = document.getElementById('logs-table-body');
        const summary = document.getElementById('logs-summary');
        const pagination = document.getElementById('logs-pagination');
        const search = document.getElementById('logs-search');
        const level = document.getElementById('logs-level');
        const dateFrom = document.getElementById('logs-date-from');
        const dateTo = document.getElementById('logs-date-to');
        const perPage = document.getElementById('logs-per-page');
        const exportLink = document.getElementById('logs-export');
        const modal = document.getElementById('logContextModal');
        const modalClose = document.getElementById('logContextClose');
        const modalBody = document.getElementById('logContextBody');

        const state = {
            page: 1,
            per_page: Number(perPage.value || 10),
            q: '',
            level: '',
            date_from: '',
            date_to: '',
        };

        const escapeHtml = (value) => {
            if (value === null || value === undefined) return '';
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        const levelBadge = (value) => {
            const normalized = String(value || '').toLowerCase();
            const map = {
                debug: 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
                info: 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200',
                notice: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-200',
                warning: 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-200',
                error: 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200',
                critical: 'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-200',
                alert: 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900 dark:text-fuchsia-200',
                emergency: 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200'
            };
            const cls = map[normalized] || map.info;
            const label = normalized ? normalized.toUpperCase() : 'INFO';
            return `<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ${cls}">${label}</span>`;
        };

        const formatDate = (value) => {
            if (!value) return '-';
            const date = new Date(String(value).replace(' ', 'T'));
            if (Number.isNaN(date.getTime())) return escapeHtml(value);
            return date.toLocaleString('pt-BR');
        };

        const renderPagination = (paginationData, onPage) => {
            const totalPages = Math.max(1, Number(paginationData.total_pages || 1));
            const currentPage = Math.max(1, Number(paginationData.page || 1));
            const pages = [];
            const maxButtons = 5;
            let start = Math.max(1, currentPage - 2);
            let end = Math.min(totalPages, start + maxButtons - 1);
            if (end - start < maxButtons - 1) start = Math.max(1, end - maxButtons + 1);
            for (let i = start; i <= end; i += 1) pages.push(i);

            const button = (label, page, disabled, active) => {
                const base = 'rounded-lg border px-3 py-1.5 text-sm font-medium transition';
                const activeClass = active
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-200 dark:border-slate-600';
                const disabledClass = disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
                return `<button class="${base} ${activeClass} ${disabledClass}" data-page="${page}" ${disabled ? 'disabled' : ''}>${label}</button>`;
            };

            pagination.innerHTML = [
                button('Anterior', currentPage - 1, currentPage === 1, false),
                ...pages.map(page => button(page, page, false, page === currentPage)),
                button('Proxima', currentPage + 1, currentPage === totalPages, false)
            ].join('');

            pagination.onclick = (event) => {
                const btn = event.target.closest('button[data-page]');
                if (!btn) return;
                const nextPage = Number(btn.dataset.page);
                if (!Number.isFinite(nextPage) || nextPage < 1 || nextPage > totalPages) return;
                onPage(nextPage);
            };
        };

        const renderSummary = (paginationData) => {
            const total = Number(paginationData.total || 0);
            const page = Math.max(1, Number(paginationData.page || 1));
            const pageSize = Number(paginationData.per_page || 10);
            const start = total === 0 ? 0 : (page - 1) * pageSize + 1;
            const end = Math.min(total, page * pageSize);
            summary.textContent = `Mostrando ${start}-${end} de ${total} logs`;
        };

        const buildFilterQuery = () => {
            const query = new URLSearchParams();
            if (state.q) query.set('q', state.q);
            if (state.level) query.set('level', state.level);
            if (state.date_from) query.set('date_from', state.date_from);
            if (state.date_to) query.set('date_to', state.date_to);
            return query;
        };

        const updateExportUrl = () => {
            const query = buildFilterQuery().toString();
            exportLink.href = query ? `${exportEndpoint}?${query}` : exportEndpoint;
        };

        const loadLogs = () => {
            tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-6 text-center text-slate-500">Carregando...</td></tr>';
            const url = new URL(endpoint, window.location.origin);
            url.searchParams.set('page', state.page);
            url.searchParams.set('per_page', state.per_page);
            buildFilterQuery().forEach((value, key) => {
                url.searchParams.set(key, value);
            });
            updateExportUrl();

            fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then((res) => res.json())
                .then((data) => {
                    const items = Array.isArray(data.items) ? data.items : [];
                    if (!items.length) {
                        tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-6 text-center text-slate-500">Nenhum log encontrado.</td></tr>';
                    } else {
                        tableBody.innerHTML = items.map((item) => {
                            const context = item.context_pretty || '';
                            const method = item.method_audit_log ? String(item.method_audit_log).toUpperCase() : '';
                            const uri = item.uri_audit_log || '-';
                            return `
                                <tr class="border-b border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                                    <td class="px-6 py-4 whitespace-nowrap">${formatDate(item.created_at)}</td>
                                    <td class="px-6 py-4">${levelBadge(item.level_audit_log)}</td>
                                    <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-200">${escapeHtml(item.event_audit_log || '-')}</td>
                                    <td class="px-6 py-4">${escapeHtml(item.message_audit_log || '-')}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-slate-500 dark:text-slate-400">${escapeHtml(method)}</div>
                                        <div class="text-xs">${escapeHtml(uri)}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">${escapeHtml(item.ip_address_audit_log || '-')}</td>
                                    <td class="px-6 py-4 text-right">
                                        <button type="button" class="log-context-btn inline-flex items-center gap-2 px-3 py-1.5 bg-slate-700 hover:bg-slate-800 text-white font-medium rounded-lg text-xs" data-context="${escapeHtml(context)}" ${context ? '' : 'disabled'}>
                                            <i class="bi bi-eye"></i>Ver
                                        </button>
                                    </td>
                                </tr>`;
                        }).join('');
                    }

                    renderSummary(data.pagination || {});
                    renderPagination(data.pagination || {}, (nextPage) => {
                        state.page = nextPage;
                        loadLogs();
                    });
                })
                .catch(() => {
                    tableBody.innerHTML = '<tr><td colspan="7" class="px-6 py-6 text-center text-slate-500">Erro ao carregar logs.</td></tr>';
                    summary.textContent = 'Erro ao carregar logs.';
                    pagination.innerHTML = '';
                });
        };

        document.getElementById('instructor-logs-table')?.addEventListener('click', (event) => {
            const button = event.target.closest('.log-context-btn');
            if (!button) return;
            const context = button.getAttribute('data-context') || '';
            const normalized = context
                .replace(/&lt;/g, '<')
                .replace(/&gt;/g, '>')
                .replace(/&quot;/g, '"')
                .replace(/&#039;/g, "'")
                .replace(/&amp;/g, '&');
            modalBody.textContent = normalized || 'Sem contexto adicional.';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalBody.textContent = '';
        };

        modalClose?.addEventListener('click', closeModal);
        modal?.addEventListener('click', (event) => {
            if (event.target === modal) closeModal();
        });

        let searchTimer = null;
        search.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                state.q = search.value.trim();
                state.page = 1;
                loadLogs();
            }, 300);
        });

        level.addEventListener('change', () => {
            state.level = level.value;
            state.page = 1;
            loadLogs();
        });

        dateFrom.addEventListener('change', () => {
            state.date_from = dateFrom.value;
            state.page = 1;
            loadLogs();
        });

        dateTo.addEventListener('change', () => {
            state.date_to = dateTo.value;
            state.page = 1;
            loadLogs();
        });

        perPage.addEventListener('change', () => {
            state.per_page = Number(perPage.value || 10);
            state.page = 1;
            loadLogs();
        });

        updateExportUrl();
        loadLogs();
    })();
</script>

<?= $this->endSection() ?>
