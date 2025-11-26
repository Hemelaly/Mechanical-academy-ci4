<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/output.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Stack+Sans+Text:wght@200..700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://www.youtube.com/iframe_api"></script>
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon.png') ?>" width="100%" type="image/x-icon">

    <style>
        body {
            font-family: 'Stack Sans Text', sans-serif !important;
        }
    </style>
</head>

<body class="bg-slate-50 dark:bg-slate-900 transition-all duration-500 ease-in-out overflow-x-hidden prevent-overflow">

    <div class="flex h-screen flex-container">

        <?php if (isset($user->role)): ?>

            <?php if ($user->role === 'student'): ?>

                <?= $this->include('pages/student/partials/sidebar') ?>

                <!-- CONTEÚDO PRINCIPAL -->
                <div id="main-content" class="flex flex-1 flex-col transition-all duration-200 prevent-overflow">

                    <?= $this->include('pages/student/partials/navbar') ?>

                    <!-- CONTEÚDO -->
                    <main class="flex-1 overflow-y-auto p-6 md:p-10 space-y-6 w-full max-w-full overflow-x-hidden">

                        <?= $this->renderSection('home_student') ?>

                        <?= $this->renderSection('my_courses') ?>

                        <?= $this->renderSection('all_courses') ?>

                        <?= $this->renderSection('profile') ?>

                        <?= $this->renderSection('lessons') ?>

                    </main>
                </div>

            <?php elseif ($user->role === 'instructor'): ?>

                <?= $this->include('pages/instructor/partials/sidebar') ?>

            <?php else: ?>

                <?= $this->include('pages/admin/partials/sidebar') ?>

            <?php endif ?>

        <?php endif ?>

    </div>

    <script src="<?= base_url('assets/js/main.js') ?>"></script>

</body>

</html>