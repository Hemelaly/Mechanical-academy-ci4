<?php

$user = service('auth')->user();

?>

<header
    class="flex py-3 items-center justify-between border-b border-white bg-white dark:bg-slate-800/50 dark:border-slate-800 px-4 shadow-sm transition-all duration-500 ease-in-out">
    <div class="flex items-center gap-2">

        <!-- COLAPSAR DESKTOP -->
        <button
            id="collapse-desktop"
            class="hidden lg:inline-flex w-11 h-11 transition-all duration-500 ease-in-out items-center border border-slate-300 text-slate-500 dark:border-slate-700 justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-white"
            title="Colapsar sidebar">
            <i class="bi bi-list"></i>
        </button>

        <!-- ABRIR SIDEBAR NO MOBILE -->
        <button
            id="open-sidebar"
            class="inline-flex lg:hidden h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800">
            &#9776;
        </button>

        <div class="hidden md:flex items-center gap-2">
            <input
                type="text"
                placeholder="Pesquisar..."
                class="w-72 rounded-lg border border-slate-300 bg-slate-50 px-3 py-3.5 text-xs text-slate-700 outline-none focus:border-blue-500 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white" />
        </div>
    </div>

    <div class="flex items-center gap-2">
        <button
            id="theme-toggle"
            class="inline-flex px-[11px] py-1.5 items-center cursor-pointer justify-center rounded-full border border-slate-200 dark:border-slate-700
                            bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-400 text-lg hover:bg-slate-50 dark:hover:bg-slate-800"
            aria-label="Trocar tema">
            <i id="theme-toggle-icon" class="bi bi-moon-stars"></i>
        </button>

        <div class="relative">
            <button class="relative inline-flex overflow-visible px-[12px] py-1.5 cursor-pointer items-center justify-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-400 text-lg hover:bg-slate-50 dark:hover:bg-slate-800 dropdown-toggle">
                <i class="bi bi-bell"></i>
                <span id="admin-notifications-badge" class="absolute -right-1 -top-1 hidden min-w-5 rounded-full bg-rose-600 px-1.5 py-0.5 text-[10px] font-semibold leading-none text-white shadow">
                    0
                </span>
            </button>

            <!-- Dropdown Menu -->
            <div class="absolute right-0 top-full mt-2 w-80 rounded-2xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 z-50 hidden dropdown-menu transform origin-top-right transition-all duration-200 ease-out opacity-0 scale-95">
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
            class="flex w-full items-center gap-3 rounded-lg py-2 text-xs text-slate-200 pointer">
            <img
                src="<?= base_url($user->img ?? 'assets/img/user-default.png') ?>"
                alt="Foto de perfil de <?= esc($user->username) ?>"
                class="h-10 w-10 rounded-full object-cover border border-slate-200 dark:border-slate-700" />
            <div class="sidebar-label hidden sm:block text-left">
                <p class="text-xs font-medium text-slate-800 dark:text-white"><?= $user->username ?></p>
                <p class="text-[11px] text-slate-400">Ver Perfil</p>
            </div>
        </a>
    </div>
</header>

<script>
    (function () {
        const endpoint = <?= json_encode(site_url('admin/dashboard/notifications/data')) ?>;
        const badge = document.getElementById('admin-notifications-badge');
        const countBadge = document.getElementById('admin-notifications-count');
        const list = document.getElementById('admin-notifications-list');
        const markRead = document.getElementById('admin-notifications-mark-read');

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

        const render = (items) => {
            if (!list) return;
            if (!items || !items.length) {
                list.innerHTML = `<div class="p-4 text-sm text-slate-500 dark:text-slate-400">Sem notificações recentes.</div>`;
                return;
            }

            list.innerHTML = items.map((it) => {
                const cls = toneClass(it.tone);
                return `
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-900/60 transition-colors duration-150">
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-full ${cls} flex items-center justify-center flex-shrink-0">
                                <i class="bi ${escapeHtml(it.icon || 'bi-activity')}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 dark:text-white">
                                    ${escapeHtml(it.title || 'Evento')}
                                </p>
                                <span class="inline-block mt-2 text-xs text-slate-500 dark:text-slate-400">
                                    ${escapeHtml(it.time || '')}
                                </span>
                            </div>
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
                    const unread = Number(data?.unread || 0);
                    if (badge) {
                        badge.classList.toggle('hidden', unread <= 0);
                        badge.textContent = unread > 99 ? '99+' : String(unread);
                    }
                    if (countBadge) {
                        countBadge.classList.toggle('hidden', unread <= 0);
                        countBadge.textContent = `${unread} novas`;
                    }
                    render(data?.items || []);
                })
                .catch(() => {
                    render([]);
                });
        };

        markRead?.addEventListener('click', () => {
            setLastSeenNow();
            refresh();
        });

        // Primeira carga + polling leve
        document.addEventListener('DOMContentLoaded', () => {
            if (!getLastSeen()) setLastSeenNow();
            refresh();
            setInterval(refresh, 60000);
        });
    })();
</script>
