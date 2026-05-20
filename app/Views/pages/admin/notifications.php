<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Dashboard - Notificações<?= $this->endSection() ?>

<?= $this->section('home_admin') ?>
<div class="space-y-6">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Notificações</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Auditoria e eventos recentes do sistema.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button id="admin-notifications-mark-read-page" type="button" class="btn btn-outline">
                <i class="bi bi-check2-all"></i>
                Marcar como lidas
            </button>
            <button id="admin-notifications-refresh" type="button" class="btn btn-primary">
                <i class="bi bi-arrow-repeat"></i>
                Atualizar
            </button>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <div class="flex flex-col gap-3 border-b border-slate-200 p-4 dark:border-slate-700 md:flex-row md:items-center md:justify-between">
            <div class="text-sm text-slate-500 dark:text-slate-400">
                <span id="admin-notifications-summary">Carregando...</span>
            </div>
            <div class="flex items-center gap-2">
                <select id="admin-notifications-level" class="rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                    <option value="">Todos níveis</option>
                    <option value="error">Erro</option>
                    <option value="warning">Aviso</option>
                    <option value="info">Info</option>
                    <option value="debug">Debug</option>
                </select>
                <select id="admin-notifications-limit" class="rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                </select>
            </div>
        </div>

        <div id="admin-notifications-page-list" class="divide-y divide-slate-100 dark:divide-slate-700"></div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('page_scripts') ?>
    <script>
        (function () {
            const endpoint = <?= json_encode(site_url('admin/dashboard/notifications/data')) ?>;
            const list = document.getElementById('admin-notifications-page-list');
            const summary = document.getElementById('admin-notifications-summary');
            const levelSelect = document.getElementById('admin-notifications-level');
            const limitSelect = document.getElementById('admin-notifications-limit');
            const refreshBtn = document.getElementById('admin-notifications-refresh');
            const markReadBtn = document.getElementById('admin-notifications-mark-read-page');

            const lastSeenKey = 'admin_notifications_last_seen';
            const getLastSeen = () => localStorage.getItem(lastSeenKey) || '';
            const setLastSeenNow = () => localStorage.setItem(lastSeenKey, new Date().toISOString());

            const escapeHtml = (value) => {
                if (value === null || value === undefined) return '';
                return String(value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            const toneClass = (tone) => {
                switch (tone) {
                    case 'rose': return 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300';
                    case 'amber': return 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300';
                    case 'emerald': return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300';
                    case 'indigo': return 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300';
                    case 'slate': return 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200';
                    default: return 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300';
                }
            };

            const render = (items) => {
                if (!list) return;
                if (!items || !items.length) {
                    list.innerHTML = `<div class="p-6 text-sm text-slate-500 dark:text-slate-400">Sem notificações recentes.</div>`;
                    return;
                }

                list.innerHTML = items.map((it) => `
                    <div class="p-4">
                        <div class="flex gap-3">
                            <div class="h-10 w-10 rounded-full ${toneClass(it.tone)} flex items-center justify-center flex-shrink-0">
                                <i class="bi ${escapeHtml(it.icon || 'bi-activity')}"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">${escapeHtml(it.title || 'Evento')}</p>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">${escapeHtml(it.time || '')}</span>
                                </div>
                                <div class="mt-2 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                    <span class="rounded-full border border-slate-200 px-2 py-0.5 dark:border-slate-700">${escapeHtml(it.level || 'info')}</span>
                                    <span>#${Number(it.id || 0)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            };

            const refresh = () => {
                if (!getLastSeen()) setLastSeenNow();

                const url = new URL(endpoint, window.location.origin);
                url.searchParams.set('limit', String(Number(limitSelect?.value || 20)));
                url.searchParams.set('since', getLastSeen());

                fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then((data) => {
                        let items = data?.items || [];
                        const level = String(levelSelect?.value || '');
                        if (level) {
                            items = items.filter((i) => String(i.level || '').toLowerCase() === level);
                        }

                        const unread = Number(data?.unread || 0);
                        if (summary) summary.textContent = `Novas: ${unread} • Mostrando: ${items.length}`;
                        render(items);
                    })
                    .catch(() => {
                        if (summary) summary.textContent = 'Falha ao carregar notificações.';
                        render([]);
                    });
            };

            refreshBtn?.addEventListener('click', refresh);
            levelSelect?.addEventListener('change', refresh);
            limitSelect?.addEventListener('change', refresh);
            markReadBtn?.addEventListener('click', () => {
                setLastSeenNow();
                refresh();
            });

            document.addEventListener('DOMContentLoaded', refresh);
        })();
    </script>
<?= $this->endSection() ?>

