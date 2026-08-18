<?php

$user = service('auth')->user();

?>

<header
    class="academy-nav flex h-14 shrink-0 items-center justify-between gap-2 border-b border-slate-200/80 bg-white/90 px-3 backdrop-blur-sm transition-colors duration-200 dark:border-white/10 dark:bg-[#0c1017]/80 sm:px-4">
    <div class="flex min-w-0 items-center gap-2">

        <button
            id="collapse-desktop"
            class="hidden h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-500 transition hover:bg-slate-100 dark:border-white/10 dark:text-white dark:hover:bg-white/5 lg:inline-flex"
            title="Colapsar sidebar">
            <i class="bi bi-list"></i>
        </button>

        <button
            id="open-sidebar"
            class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-white/10 dark:bg-[#11151c] dark:text-slate-100 dark:hover:bg-white/5 lg:hidden"
            aria-label="Abrir menu">
            <i class="bi bi-list"></i>
        </button>

        <div class="hidden min-w-0 md:block">
            <input
                type="search"
                placeholder="Pesquisar..."
                class="h-9 w-44 rounded-md border border-slate-200 bg-slate-50 px-3 text-xs text-slate-700 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-white/10 dark:bg-[#11151c] dark:text-white xl:w-64" />
        </div>
    </div>

    <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
        <button
            id="theme-toggle"
            class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-white/10 dark:bg-[#11151c] dark:text-slate-400 dark:hover:bg-white/5"
            aria-label="Trocar tema"
            title="Trocar tema">
            <i id="theme-toggle-icon" class="bi bi-sun"></i>
        </button>

        <div class="relative">
            <!-- <button class="relative inline-flex px-[12px] py-1.5 cursor-pointer items-center justify-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-400 text-lg hover:bg-slate-50 dark:hover:bg-slate-800 dropdown-toggle">
                <i class="bi bi-bell"></i>
                <span class="absolute -top-0 -right-0 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
            </button> -->

            <!-- Dropdown Menu -->
            <div class="absolute right-0 top-full mt-2 w-80 rounded-2xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 z-50 hidden dropdown-menu transform origin-top-right transition-all duration-200 ease-out opacity-0 scale-95">
                <!-- Cabeçalho do Dropdown -->
                <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-slate-800 dark:text-white">Notificações</h3>
                        <span class="text-xs bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-2 py-1 rounded-full">12 novas</span>
                    </div>
                </div>

                <!-- Lista de Notificações -->
                <div class="max-h-96 overflow-y-auto">
                    <!-- Notificação 1 -->
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors duration-150">
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-house text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 dark:text-white truncate">
                                    Nova visita agendada
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    T3 Sommerschield - Hoje às 15:30
                                </p>
                                <span class="inline-block mt-2 text-xs text-blue-600 dark:text-blue-400">
                                    Há 5 minutos
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Notificação 2 -->
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors duration-150">
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-cash-coin text-emerald-600 dark:text-emerald-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 dark:text-white truncate">
                                    Pagamento recebido
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Marta Zimba - 0,00 MZN
                                </p>
                                <span class="inline-block mt-2 text-xs text-emerald-600 dark:text-emerald-400">
                                    Há 1 hora
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Notificação 3 -->
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors duration-150">
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900 flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-tools text-amber-600 dark:text-amber-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 dark:text-white truncate">
                                    Manutenção solicitada
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Moradia Matola - Torneira com vazamento
                                </p>
                                <span class="inline-block mt-2 text-xs text-amber-600 dark:text-amber-400">
                                    Há 2 horas
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Notificação 4 -->
                    <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors duration-150">
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-chat-dots text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 dark:text-white truncate">
                                    Nova mensagem
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Carlos Manuel - "Gostaria de agendar uma visita..."
                                </p>
                                <span class="inline-block mt-2 text-xs text-purple-600 dark:text-purple-400">
                                    Há 3 horas
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rodapé do Dropdown -->
                <div class="p-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 rounded-b-2xl">
                    <a href="../notificacoes.php" class="w-full text-center text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium py-2 transition-colors duration-150">
                        Ver todas as notificações
                    </a>
                </div>
            </div>
        </div>

        <a href="<?= site_url('/student/dashboard/perfil') ?>"
            class="flex shrink-0 items-center gap-2 rounded-md px-1 py-1 text-xs hover:bg-slate-100 dark:hover:bg-white/5">
            <span
                class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-200 text-[12px] font-semibold text-slate-700 dark:bg-slate-700 dark:text-white">
                <?= esc(mb_strtoupper(mb_substr((string) ($user->username ?? 'U'), 0, 2))) ?>
            </span>
            <div class="sidebar-label hidden min-w-0 text-left sm:block">
                <p class="truncate text-xs font-medium text-slate-800 dark:text-white"><?= esc($user->username) ?></p>
                <p class="truncate text-[11px] text-slate-500 dark:text-slate-400">Perfil</p>
            </div>
        </a>
    </div>
</header>
