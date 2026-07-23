<?php

$user = service('auth')->user();

?>

<header
    class="academy-nav flex shrink-0 py-3 items-center justify-between border-b border-slate-200/80 bg-white/90 backdrop-blur-sm dark:border-white/10 dark:bg-[#0c1017]/80 px-4 transition-colors duration-300">
    <div class="flex items-center gap-2">

        <!-- COLAPSAR DESKTOP -->
        <button
            id="collapse-desktop"
            class="hidden lg:inline-flex w-10 h-10 transition-all duration-300 ease-in-out items-center border border-slate-200 text-slate-500 dark:border-white/10 justify-center rounded-md hover:bg-slate-100 dark:hover:bg-white/5 dark:text-white"
            title="Colapsar sidebar">
            <i class="bi bi-list"></i>
        </button>

        <!-- ABRIR SIDEBAR NO MOBILE -->
        <button
            id="open-sidebar"
            class="inline-flex lg:hidden h-9 w-9 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-white/10 dark:bg-[#11151c] dark:text-slate-100 dark:hover:bg-white/5">
            &#9776;
        </button>

        <div class="hidden md:flex items-center gap-2">
            <input
                type="text"
                placeholder="Pesquisar..."
                class="w-72 rounded-md border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-700 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-white/10 dark:bg-[#11151c] dark:text-white" />
        </div>
    </div>

    <div class="flex items-center gap-2">
        <button
            id="theme-toggle"
            class="inline-flex px-[11px] py-1.5 items-center cursor-pointer justify-center rounded-md border border-slate-200 dark:border-white/10
                            bg-white dark:bg-[#11151c] text-slate-700 dark:text-slate-400 text-lg hover:bg-slate-50 dark:hover:bg-white/5"
            aria-label="Trocar tema"
            title="Trocar tema">
            <i id="theme-toggle-icon" class="bi bi-sun"></i>
        </button>

        <div class="relative">
            <button class="relative inline-flex overflow-visible px-[12px] py-1.5 cursor-pointer items-center justify-center rounded-md border border-slate-200 dark:border-white/10 bg-white dark:bg-[#11151c] text-slate-700 dark:text-slate-400 text-lg hover:bg-slate-50 dark:hover:bg-white/5 dropdown-toggle">
                <i class="bi bi-bell"></i>
                <span id="admin-notifications-dot" class="absolute -top-1 -right-1 hidden h-3 w-3 pointer-events-none">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex h-3 w-3 rounded-full bg-red-500"></span>
                </span>
            </button>

            <!-- Dropdown Menu -->
            <div class="absolute right-0 top-full mt-2 w-[min(20rem,calc(100vw-1.5rem))] rounded-2xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 z-50 hidden dropdown-menu transform origin-top-right transition-all duration-200 ease-out opacity-0 scale-95">
                <!-- Cabeçalho do Dropdown -->
                <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-slate-800 dark:text-white">Notificações</h3>
                        <span id="admin-notifications-count" class="text-xs bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-2 py-1 rounded-full hidden">0 novas</span>
                    </div>
                </div>

                <!-- Lista de Notificações -->
                <div id="admin-notifications-list" class="max-h-96 overflow-y-auto"></div>

                <!-- Rodapé do Dropdown -->
                <div class="p-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 rounded-b-2xl">
                    <div class="flex items-center justify-between gap-2">
                        <a href="<?= site_url('/admin/dashboard/notificacoes') ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                            Ver todas
                        </a>
                        <button id="admin-notifications-mark-read" type="button" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                            Marcar como lidas
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <a href="<?= site_url('/admin/dashboard/perfil') ?>"
            class="flex shrink-0 items-center gap-3 rounded-md px-1.5 py-1.5 text-xs hover:bg-slate-100 dark:hover:bg-white/5">
            <img
                src="<?= base_url($user->img ?? 'assets/img/user-default.png') ?>"
                alt="Foto de perfil de <?= esc($user->username) ?>"
                class="h-9 w-9 rounded-full object-cover border border-slate-200 dark:border-white/10" />
            <div class="sidebar-label hidden sm:block text-left">
                <p class="text-xs font-medium text-slate-800 dark:text-white"><?= esc($user->username) ?></p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Ver Perfil</p>
            </div>
        </a>
    </div>
</header>

<script>
    (function () {
        const endpoint = <?= json_encode(site_url('admin/dashboard/notifications/data')) ?>;
        const dot = document.getElementById('admin-notifications-dot');
        const countBadge = document.getElementById('admin-notifications-count');
        const list = document.getElementById('admin-notifications-list');
        const markRead = document.getElementById('admin-notifications-mark-read');
        const sidebarBadge = document.getElementById('admin-notifications-sidebar-badge');

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
                list.innerHTML = `<div class="p-4 text-sm text-slate-500 dark:text-slate-400">Sem notificações recentes.</div>`;
                return;
            }

            const readIds = getReadIds();
            list.innerHTML = items.map((it) => {
                const cls = toneClass(it.tone);
                const id = Number(it.id || 0);
                const isUnread = Boolean(it.is_unread) && !readIds[String(id)];
                const wrapperCls = isUnread
                    ? 'bg-indigo-50/60 dark:bg-slate-900/40 border-l-4 border-indigo-500 pl-3'
                    : 'bg-transparent border-l-4 border-transparent';
                const titleCls = isUnread ? 'font-semibold' : 'font-medium';
                const when = formatWhen(it.created_at_iso || '');
                return `
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-900/60 transition-colors duration-150 ${wrapperCls}">
                        <div class="flex gap-3 items-start">
                            <div class="w-10 h-10 rounded-full ${cls} flex items-center justify-center flex-shrink-0">
                                <i class="bi ${escapeHtml(it.icon || 'bi-activity')}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="truncate-2 text-sm leading-snug ${titleCls} text-slate-800 dark:text-white">
                                    ${escapeHtml(it.title || 'Evento')}
                                </p>
                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold ${levelBadgeClass(it.level)}">${escapeHtml(it.level || 'info')}</span>
                                    ${isUnread ? `<span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">Nova</span>` : ``}
                                    <span class="inline-block">${escapeHtml(when || it.time || '')}</span>
                                </div>
                            </div>
                            <button type="button" class="admin-notification-item-read inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white ${isUnread ? '' : 'opacity-40 pointer-events-none'}" data-id="${id}" data-iso="${escapeHtml(it.created_at_iso || '')}" title="Marcar como lida">
                                <i class="bi bi-check2"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        };

        const refresh = () => {
            const url = new URL(endpoint, window.location.origin);
            const since = getLastSeen();
            if (since) url.searchParams.set('since', since);
            url.searchParams.set('limit', '10');

            fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then((data) => {
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
                    if (dot) dot.classList.toggle('hidden', unread <= 0);
                    if (countBadge) {
                        countBadge.classList.toggle('hidden', unread <= 0);
                        countBadge.textContent = `${unread} novas`;
                    }
                    if (sidebarBadge) {
                        sidebarBadge.classList.toggle('hidden', unread <= 0);
                        sidebarBadge.textContent = unread > 99 ? '99+' : String(unread);
                    }
                    render(data?.items || []);
                })
                .catch(() => {
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

        markRead?.addEventListener('click', () => {
            setLastSeenNow();
            clearReadIds();
            refresh();
        });

        // Primeira carga + polling leve
        document.addEventListener('DOMContentLoaded', () => {
            if (!getLastSeen()) setLastSeenNow();
            refresh();
            setInterval(refresh, 15000);
        });

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') refresh();
        });
        window.addEventListener('focus', refresh);
    })();
</script>
