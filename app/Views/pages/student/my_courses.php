<?php
// dd($courses);
?>

<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Meus Cursos<?= $this->endSection() ?>

<?= $this->section('my_courses') ?>

<!-- Main Content -->
<div class="min-h-screen bg-slate-50 dark:bg-slate-900">
  <div class="container mx-auto">

    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
        <div class="flex-1">
          <h1 class="text-2xl lg:text-2xl font-bold text-slate-800 dark:text-white mb-3">
            Meus Cursos
          </h1>
          <p class="text-sm text-slate-600 dark:text-slate-400 max-w-2xl">
            Gerencie e acompanhe o progresso dos seus cursos. Continue sua jornada de aprendizado de onde parou.
          </p>
        </div>

        <a href="/student/dashboard/cursos/"
          class="group inline-flex items-center gap-3 bg-gradient-to-br from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 px-6 py-3.5 text-white font-semibold rounded-2xl transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
          <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
            <i class="bi bi-plus text-sm"></i>
          </div>
          <span>Explorar Novos Cursos</span>
        </a>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total de Cursos</p>
              <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">
                <?= count($courses ?? []) ?>
              </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
              <i class="bi bi-book text-blue-600 dark:text-blue-400 text-sm"></i>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Em Andamento</p>
              <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">
                <?= count(array_filter($courses ?? [], function ($course) use ($progress) {
                  return $course->status_enrollment == 'Ativa' && $progress->{$course->id_course}->progress > 0 && $progress->{$course->id_course}->progress < 100;
                })) ?>
              </p>
            </div>
            <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900 rounded-xl flex items-center justify-center">
              <i class="bi bi-play-circle text-amber-600 dark:text-amber-400 text-sm"></i>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Concluídos</p>
              <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">
                <?= count(array_filter($courses ?? [], function ($course) use ($progress) {
                  return $course->status_enrollment == 'Ativa' && $progress->{$course->id_course}->progress == 100;
                })) ?>
              </p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center">
              <i class="bi bi-check-circle text-green-600 dark:text-green-400 text-sm"></i>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Progresso Médio</p>
              <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">
                <?php
                $totalProgress = 0;
                $activeCourses = array_filter($courses ?? [], function ($course) {
                  return $course->status_enrollment == 'Ativa';
                });
                foreach ($activeCourses as $course) {
                  $totalProgress += $progress->{$course->id_course}->progress;
                }
                echo count($activeCourses) > 0 ? round($totalProgress / count($activeCourses)) : 0;
                ?>%
              </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center">
              <i class="bi bi-bar-chart-line text-purple-600 dark:text-purple-400 text-sm"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros -->
    <div class="flex flex-wrap gap-3 mb-8">
      <button class="filter-btn active inline-flex items-center gap-2 px-5 py-2.5 bg-blue-500 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl hover:bg-blue-600"
        data-filter="all">
        <i class="bi bi-layers text-sm"></i>
        Todos os Cursos
      </button>
      <button class="filter-btn inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 font-medium rounded-xl transition-all duration-200 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-blue-300 dark:hover:border-blue-600"
        data-filter="in-progress">
        <i class="bi bi-play-circle text-amber-500"></i>
        Em Andamento
      </button>
      <button class="filter-btn inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 font-medium rounded-xl transition-all duration-200 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-green-300 dark:hover:border-green-600"
        data-filter="completed">
        <i class="bi bi-check-circle text-green-500"></i>
        Concluídos
      </button>
      <button class="filter-btn inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 font-medium rounded-xl transition-all duration-200 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-500"
        data-filter="not-started">
        <i class="bi bi-clock text-slate-500"></i>
        Não Iniciados
      </button>
    </div>

    <!-- Courses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <?php if ($courses && count(array_filter($courses, function ($course) {
        return $course->status_enrollment == 'Ativa';
      })) > 0): ?>
        <?php foreach ($courses as $course): ?>
          <?php if ($course->status_enrollment == 'Ativa'): ?>
            <div class="course-card bg-white dark:bg-slate-800 rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-600 group">

              <!-- Course Image -->
              <div class="relative overflow-hidden">
                <img src="<?= base_url('assets/instructor/img/courses/' . $course->image_course) ?>"
                  alt="<?= $course->title_course ?>"
                  class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-500">

                <!-- Overlay with Rating -->
                <div class="absolute top-4 right-4">
                  <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-black/80 text-white text-sm font-semibold rounded-full backdrop-blur-sm">
                    <i class="fas fa-star text-yellow-400"></i>
                    <span>9.8</span>
                  </span>
                </div>

                <!-- Progress Overlay -->
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
                  <div class="flex justify-between items-center text-white text-sm mb-2">
                    <span class="font-medium">Progresso</span>
                    <span class="font-bold"><?= (int) $progress->{$course->id_course}->progress ?>%</span>
                  </div>
                  <div class="w-full h-2 bg-white/30 rounded-full overflow-hidden">
                    <div class="h-2 bg-gradient-to-r from-sky-400 to-cyan-400 rounded-full transition-all duration-500"
                      style="width: <?= (int) $progress->{$course->id_course}->progress ?>%"></div>
                  </div>
                </div>
              </div>

              <!-- Course Content -->
              <div class="p-6">
                <div class="flex items-start justify-between mb-3">
                  <div class="flex-1">
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                      <?= esc($course->title_course) ?>
                    </h3>
                    <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400 text-sm">
                      <i class="bi bi-person-workspace"></i>
                      <span>Prof. <?= esc($course->username) ?></span>
                    </div>
                  </div>
                </div>

                <p class="text-slate-600 dark:text-slate-400 text-sm mb-4 line-clamp-2">
                  <?= esc($course->description_course) ?>
                </p>

                <!-- Category Badge -->
                <div class="flex items-center justify-between mb-4">
                  <span class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-full">
                    <i class="fas fa-tag"></i>
                    <?= esc($course->category ?? 'Curso') ?>
                  </span>

                  <!-- Status Indicator -->
                  <?php $courseProgress = (int) $progress->{$course->id_course}->progress; ?>
                  <?php if ($courseProgress == 0): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-full">
                      <i class="fas fa-clock"></i>
                      Não Iniciado
                    </span>
                  <?php elseif ($courseProgress == 100): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-400 text-xs font-medium rounded-full">
                      <i class="fas fa-check"></i>
                      Concluído
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-400 text-xs font-medium rounded-full">
                      <i class="fas fa-play"></i>
                      Em Andamento
                    </span>
                  <?php endif; ?>
                </div>

                <!-- Action Button -->
                <a href="<?= site_url('student/dashboard/ver_aulas/' . $course->resumeLessonId) ?>?autoplay=1"
                  class="group/btn w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-blue-500/25">
                  <?php if (!empty($course->resumeLessonId) && $courseProgress > 0): ?>
                    <i class="fas fa-play-circle group-hover/btn:scale-110 transition-transform"></i>
                    Continuar Assistindo
                  <?php else: ?>
                    <i class="fas fa-play group-hover/btn:scale-110 transition-transform"></i>
                    Começar Curso
                  <?php endif; ?>
                </a>
              </div>
            </div>
          <?php endif ?>
        <?php endforeach ?>
      <?php else: ?>
        <!-- Empty State -->
        <div class="col-span-1 md:col-span-2 xl:col-span-3">
          <div class="text-center py-16">
            <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-6">
              <i class="fas fa-book-open text-slate-400 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 dark:text-slate-300 mb-3">
              Nenhum curso encontrado
            </h3>
            <p class="text-slate-500 dark:text-slate-500 text-sm mb-8 max-w-md mx-auto">
              Você não está matriculado em nenhum curso ativo no momento.
            </p>
            <a href="/student/dashboard/cursos/"
              class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-semibold rounded-2xl transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
              <i class="fas fa-compass"></i>
              Explorar Cursos Disponíveis
            </a>
          </div>
        </div>
      <?php endif ?>
    </div>
  </div>
</div>

<!-- JavaScript -->
<script>
  // Filter functionality
  const filterButtons = document.querySelectorAll('.filter-btn');
  const courseCards = document.querySelectorAll('.course-card');

  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      // Update active button
      filterButtons.forEach(b => {
        b.classList.remove('active', 'bg-blue-500', 'text-white', 'shadow-lg');
        b.classList.add('bg-white', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300', 'border');
      });

      btn.classList.add('bg-blue-500', 'text-white', 'shadow-lg');
      btn.classList.remove('bg-white', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300', 'border');

      const filter = btn.getAttribute('data-filter');

      // Filter courses (simplified - you can implement actual filtering logic here)
      courseCards.forEach(card => {
        const progress = parseInt(card.querySelector('.bg-gradient-to-r')?.style.width) || 0;

        let shouldShow = true;
        switch (filter) {
          case 'in-progress':
            shouldShow = progress > 0 && progress < 100;
            break;
          case 'completed':
            shouldShow = progress === 100;
            break;
          case 'not-started':
            shouldShow = progress === 0;
            break;
          default: // 'all'
            shouldShow = true;
        }

        card.style.display = shouldShow ? 'block' : 'none';
      });
    });
  });

  // SweetAlert for notifications
  document.addEventListener('DOMContentLoaded', function() {
    <?php if (session()->has('swal')):
      $s = session()->get('swal'); ?>
      Swal.fire({
        icon: '<?= esc($s['icon']) ?>',
        title: '<?= esc($s['title']) ?>',
        text: '<?= esc($s['text']) ?>',
        confirmButtonText: 'OK',
        background: '<?= $s['icon'] === 'error' ? '#fef2f2' : ($s['icon'] === 'success' ? '#f0fdf4' : '#ffffff') ?>',
        color: '#1f2937'
      });
    <?php endif; ?>
  });

  // Add CSS for line clamping
  const style = document.createElement('style');
  style.textContent = `
    .line-clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
  `;
  document.head.appendChild(style);
</script>

<?= $this->endSection() ?>