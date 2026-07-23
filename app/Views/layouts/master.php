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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.youtube.com/iframe_api"></script>
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon.png') ?>" type="image/x-icon">

    <?= $this->renderSection('page_styles') ?>

    <style>
        body { font-family: 'Sora', system-ui, sans-serif !important; }
    </style>
</head>

<body class="academy-shell bg-slate-50 text-slate-800 dark:bg-[#07090d] dark:text-slate-100 transition-colors duration-300 overflow-hidden prevent-overflow">

    <div class="flex h-screen min-h-0 flex-container">

        <?php if (isset($user)): ?>

            <?php if ($user->role === 'student'): ?>

                <?= $this->include('pages/student/partials/sidebar') ?>

                <div id="main-content" class="flex min-w-0 min-h-0 flex-1 flex-col transition-all duration-200 prevent-overflow academy-main">

                    <?= $this->include('pages/student/partials/navbar') ?>

                    <main class="flex min-w-0 min-h-0 flex-1 flex-col overflow-y-auto p-5 md:p-8 space-y-6 w-full max-w-full overflow-x-hidden pb-10">

                        <?= $this->renderSection('home_admin') ?>
                        <?= $this->renderSection('home_student') ?>
                        <?= $this->renderSection('my_courses') ?>
                        <?= $this->renderSection('jitsi') ?>
                        <?= $this->renderSection('all_courses') ?>
                        <?= $this->renderSection('profile') ?>
                        <?= $this->renderSection('lessons') ?>
                        <?= $this->renderSection('certificates') ?>

                    </main>

                    <?= $this->include('pages/student/partials/footer') ?>

                </div>

            <?php elseif ($user->role === 'instructor'): ?>

                <?= $this->include('pages/instructor/partials/sidebar') ?>

                <div id="main-content" class="flex min-w-0 min-h-0 flex-1 flex-col transition-all duration-200 prevent-overflow academy-main">

                    <?= $this->include('pages/instructor/partials/navbar') ?>

                    <main class="flex min-w-0 min-h-0 flex-1 flex-col overflow-y-auto p-5 md:p-8 space-y-6 w-full max-w-full overflow-x-hidden pb-10">

                        <?= $this->renderSection('home_instructor') ?>
                        <?= $this->renderSection('my_courses') ?>
                        <?= $this->renderSection('add_course') ?>
                        <?= $this->renderSection('edit_course') ?>
                        <?= $this->renderSection('profile') ?>
                        <?= $this->renderSection('financial') ?>
                        <?= $this->renderSection('jitsi') ?>
                        <?= $this->renderSection('students') ?>
                        <?= $this->renderSection('logs') ?>
                        <?= $this->renderSection('lessons') ?>
                        <?= $this->renderSection('certificates') ?>

                    </main>
                </div>

            <?php else: ?>

                <?= $this->include('pages/admin/partials/sidebar') ?>

                <div id="main-content" class="flex min-w-0 min-h-0 flex-1 flex-col transition-all duration-200 prevent-overflow academy-main">

                    <?= $this->include('pages/admin/partials/navbar') ?>

                    <main class="flex min-w-0 min-h-0 flex-1 flex-col overflow-y-auto p-5 md:p-8 space-y-6 w-full max-w-full overflow-x-hidden pb-10">

                        <?= $this->renderSection('home_admin') ?>
                        <?= $this->renderSection('courses') ?>
                        <?= $this->renderSection('all_courses') ?>
                        <?= $this->renderSection('students') ?>
                        <?= $this->renderSection('instructors') ?>
                        <?= $this->renderSection('profile') ?>
                        <?= $this->renderSection('financial') ?>
                        <?= $this->renderSection('analytics') ?>
                        <?= $this->renderSection('lessons') ?>
                        <?= $this->renderSection('certificates') ?>

                    </main>

                    <?= $this->include('pages/student/partials/footer') ?>

                </div>

            <?php endif ?>

        <?php endif ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3" defer></script>
    <script src="<?= base_url('assets/js/flowbite-datatables.js') ?>" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js" defer></script>
    <script src="<?= base_url('assets/js/theme.js') ?>"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <script>window.ANALYTICS_COLLECT_URL = <?= json_encode(site_url('analytics/collect')) ?>;</script>
    <script src="<?= base_url('assets/js/analytics-tracker.js') ?>" defer></script>
    <?= view('partials/posthog') ?>

    <?= $this->renderSection('page_scripts') ?>

    <?php if (session('success')): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: <?= json_encode(session('success')) ?>,
                background: '#11151c',
                color: '#f3f6fb',
                confirmButtonColor: '#0d6efd'
            });
        </script>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: <?= json_encode(session('error')) ?>,
                background: '#11151c',
                color: '#f3f6fb',
                confirmButtonColor: '#0d6efd'
            });
        </script>
    <?php endif; ?>

</body>

</html>
