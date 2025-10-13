<?php

// dd($courses);

?>

<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Meus Cursos<?= $this->endSection() ?>

<?= $this->section('my_courses') ?>
<!-- CSS -->
<style>
  .filter-btn {
    background-color: transparent;
    border: 1px solid #4c1d95;
    color: #a78bfa;
    padding: 8px 16px;
    border-radius: 999px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
  }

  .filter-btn i {
    font-size: 14px;
  }

  .filter-btn:hover {
    background-color: #8b5cf6;
    border-color: #8b5cf6;
    color: #fff;
  }

  .filter-btn.active {
    background-color: #8b5cf6;
    border-color: #8b5cf6;
    color: #fff;
  }
</style>

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
    <div class="mb-4 d-flex flex-wrap gap-2">
      <button class="filter-btn active" data-filter="all">
        <i class="fas fa-list me-1"></i> Todos
      </button>
      <button class="filter-btn" data-filter="in-progress">
        <i class="fas fa-play me-1"></i> Em Andamento
      </button>
      <button class="filter-btn" data-filter="completed">
        <i class="fas fa-check me-1"></i> Concluídos
      </button>
      <button class="filter-btn" data-filter="not-started">
        <i class="fas fa-clock me-1"></i> Não Iniciados
      </button>
    </div>

    <!-- Courses Grid -->
    <div class="row g-4">
      <!-- Curso 1 -->
      <?php if ($courses): ?>
        <?php foreach ($courses as $key => $course): ?>
          <?php if ($course->status_enrollment == 'Ativa'): ?>
            <div class="col-md-6 col-lg-4">
              <div class="card bg-modern-dark text-light h-100 shadow rounded-3">
                <img src="<?= base_url('assets/instructor/img/courses/' . $course->image_course) ?>" class="card-img-top" alt="<?= $course->title_course ?>">
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title d-flex justify-content-between align-items-center">
                    <?= $course->title_course ?>
                    <span class="badge bg-warning text-dark">
                      <i class="fas fa-star me-1"></i> 9.8
                    </span>
                  </h5>
                  <p class="small text-muted mb-1">Por: Prof. <?= $course->username ?></p>
                  <p class="card-text flex-grow-1"><?= $course->description_course ?></p>

                  <!-- Progresso -->
                  <!-- <div class="mb-3">
                    <div class="d-flex justify-content-between">
                      <small>Progresso</small>
                      <small>85%</small>
                    </div>
                    <div class="progress bg-dark" style="height: 6px;">
                      <div class="progress-bar bg-info" style="width: 0%;"></div>
                    </div>
                    <small class="text-muted">0 de 0 módulos concluídos</small>
                  </div> -->

                  <!-- Ações -->
                  <div class="d-flex gap-2">
                    <a href="/student/dashboard/ver_aulas/<?= $course->firstLessonId ?>" class="btn btn-info flex-fill">
                      <i class="fas fa-play me-1"></i> Continuar
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endif ?>
        <?php endforeach ?>
      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> Você não está matriculado em nenhum curso ativo. <a href="/student/dashboard/cursos/" class="alert-link">Explore nossos cursos</a>.
          </div>
        </div>
      <?php endif ?>
    </div>
  </div>

  <!-- JS simples para alternar active -->
  <script>
    const filterButtons = document.querySelectorAll('.filter-btn');

    filterButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        filterButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const filter = btn.getAttribute('data-filter');
        // Aqui você pode adicionar lógica JS para filtrar seus cursos
        console.log('Filtrar por:', filter);
      });
    });

    document.addEventListener('DOMContentLoaded', function() {
      <?php if (session()->has('swal')):
        $s = session()->get('swal'); ?>
        Swal.fire({
          icon: '<?= esc($s['icon']) ?>',
          title: '<?= esc($s['title']) ?>',
          text: '<?= esc($s['text']) ?>',
          confirmButtonText: 'OK'
        });
      <?php endif; ?>
    });
  </script>
</section>
<?= $this->endSection() ?>