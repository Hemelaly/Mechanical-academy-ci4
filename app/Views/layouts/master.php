<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Outfit:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://www.youtube.com/iframe_api"></script>
</head>

<body>
    <!-- Conteúdo -->
    <main class="container-fluid">
        <?php if (isset($user->role)): ?>
            <?php if ($user->role === 'admin'): ?>

                <div class="row">
                    <!-- Sidebar -->
                    <div class="col-12 col-md-3 col-lg-2 sidebar d-flex flex-column p-sm-3">
                        <?= $this->include('pages/admin/partials/sidebar') ?>
                    </div>

                    <!-- Conteúdo -->
                    <div class="col-12 col-md-9 col-lg-10 p-4">
                        <?= $this->include('pages/admin/partials/navbar') ?>

                        <?= $this->renderSection('home_admin') ?>
                    </div>
                </div>

            <?php elseif ($user->role === 'instructor'): ?>

                <div class="row">
                    <!-- Sidebar -->
                    <div class="col-12 col-md-3 col-lg-2 sidebar d-flex flex-column p-sm-3">
                        <?= $this->include('pages/instructor/partials/sidebar') ?>
                    </div>

                    <!-- Conteúdo -->
                    <div class="col-12 col-md-9 col-lg-10 p-4">
                        <?= $this->include('pages/instructor/partials/navbar') ?>

                        <?= $this->renderSection('home_instructor') ?>

                        <?= $this->renderSection('my_courses') ?>

                        <?= $this->renderSection('add_course') ?>

                        <?= $this->renderSection('edit_course') ?>

                        <?= $this->renderSection('students') ?>

                        <?= $this->renderSection('financial') ?>

                        <?= $this->renderSection('profile') ?>
                    </div>
                </div>

            <?php elseif ($user->role === 'student'): ?>

                <div class="row">
                    <!-- Sidebar -->
                    <div class="col-12 col-md-3 col-lg-2 sidebar d-flex flex-column p-sm-3">
                        <?= $this->include('pages/student/partials/sidebar') ?>
                    </div>

                    <!-- Conteúdo -->
                    <div class="col-12 col-md-9 col-lg-10 p-4">
                        <?= $this->include('pages/student/partials/navbar') ?>

                        <?= $this->renderSection('home_student') ?>

                        <?= $this->renderSection('my_courses') ?>

                        <?= $this->renderSection('all_courses') ?>

                        <?= $this->renderSection('checkout') ?>

                        <?= $this->renderSection('lessons') ?>

                        <?= $this->renderSection('profile') ?>
                    </div>
                </div>

            <?php else: ?>
                <p class="text-danger">Role não reconhecido.</p>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (session()->has('swal')):
                $s = session()->get('swal'); ?>
                Swal.fire({
                    icon: '<?= esc($s['icon']) ?>',
                    title: '<?= esc($s['title']) ?>',
                    <?php if (!empty($s['text'])): ?>
                        text: '<?= esc($s['text']) ?>',
                    <?php endif; ?>,
                    confirmButtonText: 'OK',
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>