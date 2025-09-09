<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Meus Cursos<?= $this->endSection() ?>

<?= $this->section('my_courses') ?>
<section class="pb-3 pt-3 text-light" id="courses">
  <div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold">Meus Cursos</h2>
        <p class="text-secondary mb-0">Gerencie e acompanhe o progresso dos seus cursos</p>
      </div>
      <a href="/student/dashboard/cursos/" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Explorar Novos Cursos
      </a>
    </div>

    <!-- Filtros -->
    <div class="mb-4">
      <button class="btn btn-light active" data-filter="all">
        <i class="fas fa-list me-1"></i> Todos
      </button>
      <button class="btn btn-outline-light" data-filter="in-progress">
        <i class="fas fa-play me-1"></i> Em Andamento
      </button>
      <button class="btn btn-outline-light" data-filter="completed">
        <i class="fas fa-check me-1"></i> Concluídos
      </button>
      <button class="btn btn-outline-light" data-filter="not-started">
        <i class="fas fa-clock me-1"></i> Não Iniciados
      </button>
    </div>

    <!-- Courses Grid -->
    <div class="row g-4">
      <!-- Curso 1 -->
       <?php foreach($courses as $key => $course): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card bg-modern-dark text-light h-100 shadow rounded-3">
          <img src="<?= base_url('assets/instructor/img/courses/1757228671_c4566d7e0a0704f83924.jpeg') ?>" class="card-img-top" alt="<?= $course->title_course ?>">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title d-flex justify-content-between align-items-center">
              <?= $course->title_course ?>
              <span class="badge bg-warning text-dark">
                <i class="fas fa-star me-1"></i> 4.8
              </span>
            </h5>
            <p class="small text-muted mb-1">Por: Prof. <?= $course->name_instructor ?></p>
            <p class="card-text flex-grow-1"><?= $course->description_course ?></p>

            <!-- Progresso -->
            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <small>Progresso</small>
                <small>85%</small>
              </div>
              <div class="progress bg-dark" style="height: 6px;">
                <div class="progress-bar bg-info" style="width: 85%;"></div>
              </div>
              <small class="text-muted">8 de 10 módulos concluídos</small>
            </div>

            <!-- Ações -->
            <div class="d-flex gap-2">
              <a href="/student/dashboard/ver_aulas/<?= $course->id_course ?>" class="btn btn-info flex-fill">
                <i class="fas fa-play me-1"></i> Continuar
              </a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach ?>

    </div>
  </div>
</section>
<?= $this->endSection() ?>