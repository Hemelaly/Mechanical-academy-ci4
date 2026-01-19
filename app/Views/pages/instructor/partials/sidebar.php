<?php

use Faker\Provider\Base;

?>

<!-- BACKDROP MOBILE -->
<div id="sidebar-backdrop" class="fixed inset-0 z-30 bg-black/40 hidden lg:hidden">
</div>

<!-- SIDEBAR -->
<aside id="sidebar" class="fixed inset-y-0 transition-all duration-500 ease-in-out left-0 z-40 flex flex-col bg-slate-900 text-slate-200 dark:text-slate-100 w-70 transform -translate-x-full transition-transform duration-200 lg:translate-x-0 lg:static lg:w-70 border-r border-slate-200 dark:border-slate-800 bg-gradient-to-br from-white to-slate-50 dark:from-slate-800 dark:to-slate-900">
    <!-- TOPO: LOGO + BOTÕES -->
    <div class="flex h-16 items-center justify-between px-4 pt-5">
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
                ✕
            </button>
        </div>
    </div>

    <!-- SCROLL ÁREA -->
    <div class="flex-1 overflow-y-auto py-3 prevent-overflow">

        <!-- SEÇÃO DASHBOARD -->
        <div class="px-3 mt-6">
            <p
                class="sidebar-label mb-1 px-2 text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500">
                Menu
            </p>

            <nav class="space-y-1">

                <?php foreach ($sidebarLinks as $link): ?>
                    <?php
                    $currentUrl = current_url();
                    $isActive = str_ends_with($currentUrl, $link['url']);

                    ?>

                    <!-- LINKS -->
                    <a
                        href="<?= site_url($link['url']) ?>"
                        id="side-link"
                        class="side-link flex <?= $isActive ? 'active bg-slate-200/60 dark:bg-slate-700/60 text-slate-800 dark:text-white font-semibold' : 'text-slate-500 dark:text-slate-400' ?> items-center rounded-lg px-2 py-2 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 hover:text-blue-500 transition">
                        <span class="flex h-8 w-8 items-center justify-center">
                            <i class="bi <?= $link['icon'] ?>"></i>
                        </span>
                        <div class="sidebar-label flex flex-col ml-2">
                            <span class="text-xs font-medium"><?= $link['label'] ?></span>
                        </div>
                    </a>

                <?php endforeach; ?>

                <!-- LINKS FIXOS -->

                <div class="absolute bottom-2">
                    <a
                        href="#"
                        id="logoutBtn"
                        data-href="/logout/"
                        class="side-link flex w-full <?= $isActive ? 'active' : '' ?> items-center rounded-lg px-2 py-2 text-red-500 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 hover:text-white transition">
                        <span class="flex h-8 w-8 items-center justify-center">
                            <i class="bi bi-box-arrow-left"></i>
                        </span>
                        <div class="sidebar-label flex flex-col ml-2">
                            <span class="text-xs font-medium">Sair da conta</span>
                        </div>
                    </a>

                    <a
                        href="/"
                        id="side-link"
                        class="side-link flex <?= $isActive ? 'active bg-slate-200/60 dark:bg-slate-700/60 text-blue-600 dark:text-blue-400 font-semibold' : 'text-slate-500 dark:text-slate-400' ?> items-center rounded-lg px-2 py-2 hover:bg-slate-200/60 dark:hover:bg-slate-700/60 hover:text-white transition">
                        <span class="flex h-8 w-8 items-center justify-center">
                            <i class="bi bi-box-arrow-left"></i>
                        </span>
                        <div class="sidebar-label flex flex-col ml-2">
                            <span class="text-xs font-medium">Ir para página inicial</span>
                        </div>
                    </a>
                </div>
            </nav>
        </div>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const links = document.querySelectorAll('#sidebar .side-link');

        // página atual sem query string
        const currentPage = window.location.pathname.split('/').pop().split('?')[0];

        links.forEach(link => {

            const href = link.getAttribute('href');
            if (!href) return;

            // Pega apenas o arquivo final do link (ex: meusCursos.php)
            const cleanHref = href.split('/').pop().split('?')[0];

            if (cleanHref === currentPage) {
                link.classList.add(
                    'bg-slate-200/60',
                    'dark:bg-slate-700/60',
                    'text-blue-600',
                    'dark:text-blue-400',
                    'font-semibold'
                );
                link.classList.remove(
                    'text-slate-500',
                    'dark:text-slate-400'
                );
            }

            link.addEventListener('click', () => {
                links.forEach(l =>
                    l.classList.remove(
                        'bg-slate-200/60',
                        'dark:bg-slate-700/60',
                        'text-blue-600',
                        'dark:text-blue-400',
                        'font-semibold'
                    )
                );
                links.forEach(l =>
                    l.classList.add(
                        'text-slate-500',
                        'dark:text-slate-400'
                    )
                );
                link.classList.add(
                    'bg-slate-200/60',
                    'dark:bg-slate-700/60',
                    'text-blue-600',
                    'dark:text-blue-400',
                    'font-semibold'
                );
                link.classList.remove(
                    'text-slate-500',
                    'dark:text-slate-400'
                );
            });

        });

    });

    document.getElementById('logoutBtn').addEventListener('click', function(event) {
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
