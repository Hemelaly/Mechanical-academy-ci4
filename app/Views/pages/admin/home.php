<!-- app/Views/pages/home.php -->
<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>
Dashboard do Admin
<?= $this->endSection() ?>

<?= $this->section('home_admin') ?>
<!-- Welcome Section -->
<div class="welcome-section">
    <div class="welcome-content">
        <h1 class="fw-normal">Olá, João! 👋</h1>
        <p class="welcome-subtitle">Bem-vindo de volta! Continue sua jornada de aprendizado.</p>

        <div class="welcome-buttons">
            <a href="./course.html" class="btn btn-light me-3 text-primary">
                <i class="bi bi-play-fill me-2"></i>
                Continuar aprendendo
            </a>
            <a href="./all_courses.html" class="btn btn-outline-light my-sm-5">
                <i class="bi bi-arrow-right me-2"></i>
                Ver Todos Cursos
            </a>
        </div>
    </div>

    <div class="stats-cards">
        <div class="stat-card stat-orange">
            <div class="stat-icon">
                <i class="bi bi-fire"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">7</div>
                <div class="stat-label">Dias consecutivos</div>
            </div>
        </div>

        <div class="stat-card stat-green">
            <div class="stat-icon">
                <i class="bi bi-trophy"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">1,250</div>
                <div class="stat-label">XP Total</div>
            </div>
        </div>

        <div class="stat-card stat-yellow">
            <div class="stat-icon">
                <i class="bi bi-star-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">12</div>
                <div class="stat-label">Nível Atual</div>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="bg-card rounded-4 p-4 border border-custom-color h-100">
            <div class="d-flex flex-column gap-3 mb-2">
                <i class="bi bi-people fs-3 text-custom-secondary"></i>
                <h6 class="h4 text-custom-secondary">Cursos em Andamento</h6>
            </div>
            <div class="card-body">
                <div class="metric-number">3,782</div>
                <div class="metric-change positive">
                    <i class="bi bi-arrow-up"></i>
                    11.01%
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="bg-card rounded-4 p-4 border border-custom-color h-100">
            <div class="d-flex flex-column gap-3 mb-2">
                <i class="bi bi-trophy fs-3 text-custom-secondary"></i>
                <h6 class="h4 text-custom-secondary">Conquistas</h6>
            </div>
            <div class="card-body">
                <div class="metric-number">5,359</div>
                <div class="metric-change negative">
                    <i class="bi bi-arrow-down"></i>
                    9.05%
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="bg-card rounded-4 p-4 border border-custom-color h-100 dashboard-card">
            <div class="d-flex flex-column gap-3 mb-2">
                <i class="bi bi-clock fs-3 text-custom-secondary"></i>
                <h6 class="h4 text-custom-secondary">Tempo de Estudo</h6>
            </div>
            <div class="card-body">
                <div class="metric-number">5,359</div>
                <div class="metric-change negative">
                    <i class="bi bi-arrow-down"></i>
                    9.05%
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="bg-card rounded-4 p-4 border border-custom-color h-100 dashboard-card">
            <div class="d-flex flex-column gap-3 mb-2">
                <i class="bi bi-clock fs-3 text-custom-secondary"></i>
                <h6 class="h4 text-custom-secondary">Tempo de Estudo</h6>
            </div>
            <div class="card-body">
                <div class="metric-number">5,359</div>
                <div class="metric-change negative">
                    <i class="bi bi-arrow-down"></i>
                    9.05%
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Courses Section -->
<div class="courses-section">
    <div class="row">
        <!-- Aqui ficam os graficos -->
    </div>
</div>
<?= $this->endSection() ?>