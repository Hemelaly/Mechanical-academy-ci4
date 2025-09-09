<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Mechanical Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Outfit Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/style.css" rel="stylesheet" />
    <link rel="stylesheet" href="./assets/css/sidebar.css" />
    <link rel="stylesheet" href="./assets/css/profile.css" />
</head>

<body>

    <?php include 'functions.php' ?>
    <?php echo getSidebar('Perfil') ?>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item">
                    <a href="index.html" class="text-decoration-none text-muted">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>
                <li class="breadcrumb-item active text-light" aria-current="page">Profile</li>
            </ol>
        </nav>

        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-light mb-0">Profile</h2>
        </div>

        <!-- Profile Section -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-dark border-secondary mb-4">
                    <div
                        class="card-header bg-transparent border-secondary d-flex justify-content-between align-items-center">
                        <h5 class="text-light mb-0">Profile</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="position-relative">
                                    <img src="./assets/img/user.jpg" class="rounded-circle" width="80" height="80"
                                        alt="Profile Picture">
                                    <span
                                        class="position-absolute bottom-0 end-0 bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 24px; height: 24px;">
                                        <i class="bi bi-camera-fill text-white" style="font-size: 0.75rem;"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="text-light mb-1">Musharof Chowdhury</h4>
                                <p class="text-muted mb-1">Team Manager</p>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    Arizona, United States
                                </p>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-light rounded-5 px-4 py-2">
                                        <i class="bi bi-pencil me-1"></i>
                                        Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information Section -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-dark border-secondary mb-4">
                    <div
                        class="card-header bg-transparent border-secondary d-flex justify-content-between align-items-center">
                        <h5 class="text-light mb-0">Personal Information</h5>
                        <button class="btn btn-outline-light rounded-5 px-4 py-2">
                            <i class="bi bi-pencil me-1"></i>
                            Edit
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">First Name</label>
                                    <p class="text-light mb-0">Musharof</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Last Name</label>
                                    <p class="text-light mb-0">Chowdhury</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Email address</label>
                                    <p class="text-light mb-0">randomuser@pimjo.com</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Phone</label>
                                    <p class="text-light mb-0">+09 363 398 46</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Bio</label>
                                    <p class="text-light mb-0">Team Manager</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Section -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-dark border-secondary">
                    <div
                        class="card-header bg-transparent border-secondary d-flex justify-content-between align-items-center">
                        <h5 class="text-light mb-0">Address</h5>
                        <button class="btn btn-outline-light rounded-5 px-4 py-2">
                            <i class="bi bi-pencil me-1"></i>
                            Edit
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Country</label>
                                    <p class="text-light mb-0">United States</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">City/State</label>
                                    <p class="text-light mb-0">Arizona, United States</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Postal Code</label>
                                    <p class="text-light mb-0">ERT 2689</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted small">TAX ID</label>
                                    <p class="text-light mb-0">AS4563984</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="./assets/js/sidebar.js"></script>
</body>

</html>