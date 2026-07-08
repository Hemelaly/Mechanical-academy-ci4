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
            </div>
        </div>

        <div id="admin-notifications-page-list" class="divide-y divide-slate-100 dark:divide-slate-700"></div>

        <div class="flex flex-col gap-3 border-t border-slate-200 p-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400 md:flex-row md:items-center md:justify-between">
            <div id="admin-notifications-pagination-info">Página 1 / 1</div>
            <div class="flex gap-2">
                <button id="admin-notifications-prev" type="button" class="btn btn-outline btn-sm">Anterior</button>
                <button id="admin-notifications-next" type="button" class="btn btn-outline btn-sm">Próxima</button>
            </div>
        </div>
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
            const refreshBtn = document.getElementById('admin-notifications-refresh');
            const markReadBtn = document.getElementById('admin-notifications-mark-read-page');
            const prevBtn = document.getElementById('admin-notifications-prev');
            const nextBtn = document.getElementById('admin-notifications-next');
            const pageInfo = document.getElementById('admin-notifications-pagination-info');

            const perPage = 5;
            let currentPage = 1;
            let totalPages = 1;

            const lastSeenKey = 'admin_notifications_last_seen';
            const getLastSeen = () => localStorage.getItem(lastSeenKey) || '';
            const setLastSeenNow = () => localStorage.setItem(lastSeenKey, new Date().toISOString());

            const readIdsKey = 'admin_notifications_read_ids';
            const getReadIds = () => {
                try { return JSON.parse(localStorage.getItem(readIdsKey) || '{}') || {}; } catch { return {}; }
            };
            const setReadIds = (map) => localStorage.setItem(readIdsKey, JSON.stringify(map || {}));
            const clearReadIds = () => localStorage.removeItem(readIdsKey);

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

            const levelBadgeClass = (level) => {
                const lv = String(level || '').toLowerCase();
                if (['error', 'critical', 'alert', 'emergency'].includes(lv)) return 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200';
                if (lv === 'warning') return 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200';
                if (lv === 'debug') return 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200';
                if (lv === 'notice') return 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200';
                return 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200';
            };

            const formatWhen = (iso) => {
                if (!iso) return '';
                const d = new Date(iso);
                if (Number.isNaN(d.getTime())) return '';
                const now = new Date();
                const isSameDay = (a, b) =>
                    a.getFullYear() === b.getFullYear() &&
                    a.getMonth() === b.getMonth() &&
                    a.getDate() === b.getDate();
                const diffMs = now - d;
                const diffMin = Math.floor(diffMs / 60000);
                if (diffMin <= 0) return 'agora';
                const pad = (n) => String(n).padStart(2, '0');
                const time = `${pad(d.getHours())}:${pad(d.getMinutes())}`;

                const yesterday = new Date(now);
                yesterday.setDate(now.getDate() - 1);
                if (isSameDay(d, yesterday)) return `ontem às ${time}`;
                if (diffMin < 60) return `há ${diffMin}min`;
                const diffH = Math.floor(diffMin / 60);
                if (diffH < 24) return `há ${diffH}h`;
                const diffD = Math.floor(diffH / 24);
                if (diffD < 7) {
                    const weekdays = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];
                    return `${weekdays[d.getDay()]} às ${time}`;
                }
                return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${time}`;
            };

            const render = (items) => {
                if (!list) return;
                if (!items || !items.length) {
                    list.innerHTML = `<div class="p-6 text-sm text-slate-500 dark:text-slate-400">Sem notificações recentes.</div>`;
                    return;
                }

                const readIds = getReadIds();
                list.innerHTML = items.map((it) => {
                    const id = Number(it.id || 0);
                    const isUnread = Boolean(it.is_unread) && !readIds[String(id)];
                    const wrapperCls = isUnread
                        ? 'bg-indigo-50/60 dark:bg-slate-900/40 border-l-4 border-indigo-500 pl-3'
                        : 'bg-transparent border-l-4 border-transparent';
                    const titleCls = isUnread ? 'font-semibold' : 'font-medium';
                    const stateBadge = isUnread
                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200'
                        : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200';
                    const when = formatWhen(it.created_at_iso || '');

                    return `
                        <div class="p-4 ${wrapperCls}">
                            <div class="flex gap-3 items-start">
                                <div class="h-10 w-10 rounded-full ${toneClass(it.tone)} flex items-center justify-center flex-shrink-0">
                                    <i class="bi ${escapeHtml(it.icon || 'bi-activity')}"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="text-sm ${titleCls} text-slate-900 dark:text-white">${escapeHtml(it.title || 'Evento')}</p>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">${escapeHtml(when || it.time || '')}</span>
                                    </div>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold ${levelBadgeClass(it.level)}">${escapeHtml(it.level || 'info')}</span>
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold ${stateBadge}">${isUnread ? 'Nova' : 'Lida'}</span>
                                        <span>#${Number(it.id || 0)}</span>
                                    </div>
                                </div>
                                <button type="button" class="admin-notification-item-read inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white ${isUnread ? '' : 'opacity-40 pointer-events-none'}" data-id="${id}" data-iso="${escapeHtml(it.created_at_iso || '')}" title="Marcar como lida">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');
            };

            const refresh = () => {
                if (!getLastSeen()) setLastSeenNow();

                const url = new URL(endpoint, window.location.origin);
                url.searchParams.set('limit', String(perPage));
                url.searchParams.set('page', String(currentPage));
                url.searchParams.set('since', getLastSeen());
                const level = String(levelSelect?.value || '');
                if (level) url.searchParams.set('level', level);

                fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then((data) => {
                        const items = data?.items || [];
                        currentPage = Number(data?.page || currentPage) || 1;
                        totalPages = Number(data?.total_pages || 1) || 1;

                        const unreadServer = Number(data?.unread || 0);
                        const sinceIso = String(data?.since_iso || '');
                        const readIds = getReadIds();
                        let readSinceCount = 0;
                        if (sinceIso) {
                            for (const iso of Object.values(readIds)) {
                                if (typeof iso === 'string' && iso > sinceIso) readSinceCount++;
                            }
                        }
                        const unread = Math.max(0, unreadServer - readSinceCount);
                        if (summary) summary.textContent = `Novas: ${unread} • Mostrando: ${items.length}`;
                        if (pageInfo) pageInfo.textContent = `Página ${currentPage} / ${totalPages}`;
                        if (prevBtn) prevBtn.disabled = currentPage <= 1;
                        if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
                        render(items);
                    })
                    .catch(() => {
                        if (summary) summary.textContent = 'Falha ao carregar notificações.';
                        render([]);
                    });
            };

            list?.addEventListener('click', (ev) => {
                const btn = ev.target?.closest?.('.admin-notification-item-read');
                if (!btn) return;
                const id = Number(btn.getAttribute('data-id') || 0);
                if (!id) return;
                const iso = String(btn.getAttribute('data-iso') || '') || new Date().toISOString();
                const readIds = getReadIds();
                readIds[String(id)] = iso;
                setReadIds(readIds);
                refresh();
            });

            refreshBtn?.addEventListener('click', refresh);
            levelSelect?.addEventListener('change', refresh);
            prevBtn?.addEventListener('click', () => {
                currentPage = Math.max(1, currentPage - 1);
                refresh();
            });
            nextBtn?.addEventListener('click', () => {
                currentPage = Math.min(totalPages, currentPage + 1);
                refresh();
            });
            markReadBtn?.addEventListener('click', () => {
                setLastSeenNow();
                clearReadIds();
                currentPage = 1;
                refresh();
            });

            document.addEventListener('DOMContentLoaded', () => {
                refresh();
                setInterval(refresh, 15000);
            });

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') refresh();
            });
            window.addEventListener('focus', refresh);
        })();
    </script>
<?= $this->endSection() ?>
