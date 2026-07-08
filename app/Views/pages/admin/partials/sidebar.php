<!-- BACKDROP MOBILE -->
<div id="sidebar-backdrop" class="fixed inset-0 z-30 bg-black/40 hidden lg:hidden">
</div>

<!-- SIDEBAR -->
<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-80 -translate-x-full flex-col border-r border-slate-200 bg-gradient-to-br from-white to-slate-50 text-slate-700 transition-transform duration-200 ease-in-out dark:border-slate-800 dark:from-slate-800 dark:to-slate-900 dark:text-slate-100 lg:static lg:translate-x-0 lg:w-80">
    <!-- TOPO: LOGO + BOTÕES -->
    <div class="flex h-16 items-center justify-between px-4">
        <div class="gap-2">
            <div class="w-11">
                <img id="favicon" class="hidden" src="<?= base_url('assets/img/favicon.png') ?>" alt="">
            </div>

            <div id="logo-text" class="flex flex-col">
                <!-- Logo para tema claro -->
                <span id="logo-light" class="font-semibold leading-tight w-36">
                    <img src="<?= base_url('assets/img/logo.png') ?>" alt="">
                </span>

                <!-- Logo para tema escuro -->
                <span id="logo-dark" class="font-semibold leading-tight w-36 hidden">
                    <img src="<?= base_url('assets/img/logo-blue.png') ?>" alt="">
                </span>
            </div>

        </div>

        <div class="flex items-center gap-2">
            <!-- FECHAR MOBILE -->
            <button
                id="close-sidebar"
                class="inline-flex lg:hidden h-8 w-8 items-center justify-center rounded-lg hover:bg-slate-800">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    <!-- SCROLL AREA -->
    <div class="flex-1 overflow-y-auto py-3 prevent-overflow">

        <!-- SECAO DASHBOARD -->
        <div class="px-3 mt-6">
            <p
                class="sidebar-label mb-1 px-2 text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500">
                Menu
            </p>

            <nav class="space-y-1">

                <?php foreach ($sidebarLinks as $link): ?>
                    <?php
                    $currentUrl = rtrim(current_url(), '/');
                    $linkUrl = rtrim(site_url($link['url']), '/');
                    $isActive = $currentUrl === $linkUrl;

                    ?>

                    <!-- LINKS -->
                    <a
                        href="<?= site_url($link['url']) ?>"
                        class="side-link flex <?= $isActive ? 'active bg-slate-200/60 dark:bg-slate-700/60 text-slate-800 dark:text-white font-semibold' : 'text-slate-500 dark:text-slate-400' ?> items-center rounded-lg px-2 py-2 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 hover:text-blue-500 transition">
                        <span class="relative flex h-8 w-8 items-center justify-center">
                            <i class="bi <?= $link['icon'] ?>"></i>
                            <?php if (($link['url'] ?? '') === '/admin/dashboard/notificacoes'): ?>
                                <span id="admin-notifications-sidebar-badge" class="absolute -top-1 -right-1 hidden inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 text-[10px] font-semibold leading-none text-white shadow ring-2 ring-white dark:ring-slate-900">
                                    0
                                </span>
                            <?php endif; ?>
                        </span>
                        <div class="sidebar-label ml-2 flex flex-1 items-center gap-2">
                            <span class="text-xs font-medium"><?= $link['label'] ?></span>
                        </div>
                    </a>

                <?php endforeach; ?>
            </nav>
        </div>
    </div>

    <!-- Ações fixas (rodapé) -->
    <?php $isHome = rtrim(current_url(), '/') === rtrim(site_url('/'), '/'); ?>
    <div class="px-3 py-3 flex flex-col-reverse gap-1">
        <a
            href="#"
            id="logoutBtn"
            data-href="/logout/"
            class="side-link flex w-full items-center rounded-lg px-2 py-2 text-red-600 hover:bg-slate-200/60 dark:text-rose-400 dark:hover:bg-slate-700/60 transition">
            <span class="flex h-8 w-8 items-center justify-center">
                <i class="bi bi-box-arrow-left"></i>
            </span>
            <div class="sidebar-label ml-2 flex flex-1 items-center gap-2">
                <span class="text-xs font-medium">Sair da conta</span>
            </div>
        </a>

        <a
            href="/"
            class="side-link flex w-full <?= $isHome ? 'active bg-slate-200/60 dark:bg-slate-700/60 text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-500 dark:text-slate-400' ?> items-center rounded-lg px-2 py-2 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 hover:text-blue-500 transition">
            <span class="flex h-8 w-8 items-center justify-center">
                <i class="bi bi-house-door"></i>
            </span>
            <div class="sidebar-label ml-2 flex flex-1 items-center gap-2">
                <span class="text-xs font-medium">Ir para página inicial</span>
            </div>
        </a>
    </div>
</aside>

<script>
    document.getElementById('logoutBtn')?.addEventListener('click', function(event) {
        event.preventDefault(); // impede sair direto

        const url = this.getAttribute('data-href');

        Swal.fire({
            title: "Tem certeza?",
            text: "Deseja realmente sair da sua conta?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sim, sair",
            cancelButtonText: "Cancelar",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url; // agora sim faz o logout
            }
        });
    });
</script>
