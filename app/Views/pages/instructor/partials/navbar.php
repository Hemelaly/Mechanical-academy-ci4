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
            <button class="relative inline-flex overflow-visible px-[12px] py-1.5 cursor-pointer items-center justify-center rounded-md border border-slate-200 dark:border-white/10 bg-white dark:bg-[#11151c] text-slate-700 dark:text-slate-400 text-lg hover:bg-slate-50 dark:hover:bg-white/5 dropdown-toggle" aria-label="Notificações">
                <i class="bi bi-bell"></i>
                <span id="instructor-notifications-dot" class="absolute -top-1 -right-1 hidden h-3 w-3 pointer-events-none">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex h-3 w-3 rounded-full bg-red-500"></span>
                </span>
            </button>

            <div class="absolute right-0 top-full mt-2 w-[min(20rem,calc(100vw-1.5rem))] rounded-2xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 z-50 hidden dropdown-menu transform origin-top-right transition-all duration-200 ease-out opacity-0 scale-95">
                <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="font-semibold text-slate-800 dark:text-white">Notificações</h3>
                        <span id="instructor-notifications-count" class="text-xs bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-2 py-1 rounded-full hidden">0 novas</span>
                    </div>
                </div>

                <div id="instructor-notifications-list" class="max-h-96 overflow-y-auto"></div>

                <div class="p-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 rounded-b-2xl">
                    <div class="flex items-center justify-between gap-2">
                        <a href="<?= site_url('/instructor/dashboard/financas') ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                            Ver finanças
                        </a>
                        <button id="instructor-notifications-mark-read" type="button" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                            Marcar como lidas
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <a href="<?= site_url('/instructor/dashboard/perfil') ?>"
            class="flex shrink-0 items-center gap-3 rounded-md px-1.5 py-1.5 text-xs hover:bg-slate-100 dark:hover:bg-white/5">
            <span
                class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-200 text-[13px] font-semibold text-slate-700 dark:bg-slate-700 dark:text-white">
                <?= esc(mb_strtoupper(mb_substr((string) ($user->username ?? 'U'), 0, 2))) ?>
            </span>
            <div class="sidebar-label hidden sm:block text-left">
                <p class="text-xs font-medium text-slate-800 dark:text-white"><?= esc($user->username) ?></p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Ver Perfil</p>
            </div>
        </a>
    </div>
</header>

<script>
    (function () {
        const endpoint = <?= json_encode(site_url('instructor/dashboard/notifications/data')) ?>;
        const markEndpoint = <?= json_encode(site_url('instructor/dashboard/notifications/read')) ?>;
        const csrfName = <?= json_encode(csrf_token()) ?>;
        let csrfHash = <?= json_encode(csrf_hash()) ?>;

        const dot = document.getElementById('instructor-notifications-dot');
        const countBadge = document.getElementById('instructor-notifications-count');
        const list = document.getElementById('instructor-notifications-list');
        const markRead = document.getElementById('instructor-notifications-mark-read');

        if (!list) return;

        const toneClass = (tone) => {
            switch (tone) {
                case 'emerald': return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300';
                case 'blue': return 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300';
                case 'amber': return 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300';
                default: return 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300';
            }
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

        const render = (items, unread) => {
            if (dot) dot.classList.toggle('hidden', !(unread > 0));
            if (countBadge) {
                if (unread > 0) {
                    countBadge.classList.remove('hidden');
                    countBadge.textContent = unread + (unread === 1 ? ' nova' : ' novas');
                } else {
                    countBadge.classList.add('hidden');
                }
            }

            if (!items.length) {
                list.innerHTML = '<div class="p-4 text-sm text-slate-500 dark:text-slate-400">Sem notificações recentes.</div>';
                return;
            }

            list.innerHTML = items.map((it) => {
                const unreadCls = it.unread ? 'bg-indigo-50/60 dark:bg-indigo-950/20' : '';
                const link = it.link ? escapeHtml(it.link) : '';
                const wrapperStart = link
                    ? `<a href="${link}" class="block p-4 border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors duration-150 ${unreadCls}">`
                    : `<div class="p-4 border-b border-slate-100 dark:border-slate-700 ${unreadCls}">`;
                const wrapperEnd = link ? '</a>' : '</div>';

                return `
                    ${wrapperStart}
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-full ${toneClass(it.tone)} flex items-center justify-center flex-shrink-0">
                                <i class="bi ${escapeHtml(it.icon || 'bi-bell')}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 dark:text-white truncate">${escapeHtml(it.title)}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">${escapeHtml(it.body)}</p>
                                <span class="inline-block mt-2 text-xs text-slate-400">${escapeHtml(it.created_at)}</span>
                            </div>
                        </div>
                    ${wrapperEnd}
                `;
            }).join('');
        };

        const load = async () => {
            try {
                const res = await fetch(endpoint, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                const data = await res.json();
                render(Array.isArray(data.items) ? data.items : [], Number(data.unread || 0));
            } catch (_) {
                list.innerHTML = '<div class="p-4 text-sm text-slate-500 dark:text-slate-400">Não foi possível carregar as notificações.</div>';
            }
        };

        if (markRead) {
            markRead.addEventListener('click', async () => {
                try {
                    const body = new FormData();
                    body.append(csrfName, csrfHash);
                    const res = await fetch(markEndpoint, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                        body,
                    });
                    const data = await res.json().catch(() => ({}));
                    if (data.csrf) csrfHash = data.csrf;
                    await load();
                } catch (_) {}
            });
        }

        load();
        setInterval(load, 45000);
    })();
</script>
