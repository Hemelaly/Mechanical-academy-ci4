<!DOCTYPE html>
<html lang="pt" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?></title>
    <script>
      (function () {
        try {
          var saved = localStorage.getItem('theme');
          var dark = saved ? saved === 'dark' : true;
          document.documentElement.classList.toggle('dark', dark);
        } catch (e) {
          document.documentElement.classList.add('dark');
        }
      })();
    </script>
    <link rel="stylesheet" href="<?= base_url('assets/css/output.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard-academy.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://player.vimeo.com/api/player.js"></script>
    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon.png') ?>" type="image/x-icon">
    <?= $this->renderSection('page_styles') ?>
    <style>
        body { font-family: 'Sora', system-ui, sans-serif !important; }
    </style>
</head>

<body class="academy-player bg-slate-100 text-slate-800 overflow-hidden transition-colors duration-300 dark:bg-[#07090d] dark:text-slate-100">
    <div class="flex h-screen min-h-0 flex-col">
        <header class="academy-nav flex shrink-0 items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 py-2.5 transition-colors duration-300 dark:border-white/10 dark:bg-[#0c1017]">
            <div class="flex min-w-0 items-center gap-3">
                <a href="<?= esc($playerBackUrl ?? site_url('student/dashboard/inscricoes')) ?>"
                   class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-slate-200 text-slate-600 hover:bg-slate-100 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5 dark:hover:text-white"
                   title="Voltar">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white"><?= esc($playerTitle ?? 'Aula') ?></p>
                    <?php if (! empty($playerSubtitle)): ?>
                        <p class="truncate text-xs text-slate-500 dark:text-white/45"><?= esc($playerSubtitle) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <button id="theme-toggle" type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-600 hover:bg-slate-100 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/5"
                    aria-label="Trocar tema"
                    title="Trocar tema">
                    <i id="theme-toggle-icon" class="bi bi-sun"></i>
                </button>
                <button id="player-toc-toggle" type="button"
                    class="inline-flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/5 lg:hidden">
                    <i class="bi bi-list-ul"></i>
                    Aulas
                </button>
            </div>
        </header>

        <main class="min-h-0 flex-1 overflow-y-auto bg-slate-100 dark:bg-[#07090d]">
            <?= $this->renderSection('lessons') ?>
        </main>
    </div>

    <script src="<?= base_url('assets/js/theme.js') ?>"></script>
    <script>window.ANALYTICS_COLLECT_URL = <?= json_encode(site_url('analytics/collect')) ?>;</script>
    <script src="<?= base_url('assets/js/analytics-tracker.js') ?>" defer></script>
    <?= $this->renderSection('page_scripts') ?>
</body>

</html>
