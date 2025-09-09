<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Meus Cursos<?= $this->endSection() ?>

<?= $this->section('my_courses') ?>
<!-- Cursos -->
<div class="container-fluid my-4">
  <h4 class="fw-bold mb-4">Cursos</h4>

  <!-- Filtros + Ações -->
  <div class="row d-flex justify-content-between align-items-center mb-4">
    <!-- Botões de Filtro -->
    <div class="col-lg-6 d-flex align-items-center mb-2">
      <div class="btn-group" role="group" aria-label="Filtros de cursos">
        <button type="button" class="btn btn-outline-primary active filter-btn" data-filter="all">Todos</button>
        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="Ativo">Ativos</button>
        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="Rascunho">Rascunhos</button>
        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="Arquivado">Arquivados</button>
      </div>
    </div>

    <!-- Ações -->
    <div class="col-lg-6 d-flex align-items-center gap-2 justify-content-lg-end mb-2">
      <input
        type="text"
        id="searchInput"
        class="form-control rounded-pill"
        placeholder="Procurar cursos..."
        style="max-width: 250px" />
      <a href="novo_curso" class="btn btn-primary rounded-pill fw-semibold btn-sm">
        <i class="bi bi-plus-circle me-2"></i> Criar Novo Curso
      </a>
    </div>
  </div>

  <!-- Grid de Cursos -->
  <div class="row d-flex g-4" id="coursesContainer">
    <?php foreach ($courses as $course): ?>
      <div class="col-md-4 course-card" data-status="<?= $course->status_course ?>" data-title="<?= strtolower($course->title_course) ?>">
        <div class="card bg-modern-dark border-0 shadow-sm h-100">
          <div class="position-relative">
            <img
              src="<?= base_url('assets/instructor/img/courses/' . $course->image_course) ?>"
              class="card-img-top"
              alt="Curso <?= $course->title_course ?>" />
            <span class="badge bg-success position-absolute top-0 end-0 m-2"><?= $course->status_course ?></span>
          </div>
          <div class="card-body text-white">
            <h5 class="fw-bold"><?= $course->title_course ?></h5>
            <p class="text-muted small mb-3"><?= $course->description_course ?></p>
            <div class="d-flex gap-3 text-muted small mb-3">
              <span><i class="bi bi-people me-1"></i>234</span>
              <span><i class="bi bi-clock me-1"></i>12h</span>
              <span><i class="bi bi-check2-circle me-1"></i>Avançado</span>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-primary btn-sm rounded-pill">Editar</button>
              <button class="btn btn-danger btn-sm rounded-pill">Eliminar</button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach ?>
  </div>

  <!-- Script de Filtro -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const filterButtons = document.querySelectorAll(".filter-btn");
      const courseCards = document.querySelectorAll(".course-card");
      const searchInput = document.getElementById("searchInput");

      // Filtro por status
      filterButtons.forEach(btn => {
        btn.addEventListener("click", () => {
          // Ativar botão selecionado
          filterButtons.forEach(b => b.classList.remove("active"));
          btn.classList.add("active");

          const filter = btn.getAttribute("data-filter");
          courseCards.forEach(card => {
            const status = card.getAttribute("data-status");
            if (filter === "all" || status === filter) {
              card.style.display = "block";
            } else {
              card.style.display = "none";
            }
          });
        });
      });

      // Filtro por pesquisa
      searchInput.addEventListener("keyup", () => {
        const search = searchInput.value.toLowerCase();
        courseCards.forEach(card => {
          const title = card.getAttribute("data-title");
          if (title.includes(search)) {
            card.style.display = "block";
          } else {
            card.style.display = "none";
          }
        });
      });
    });
  </script>
</div>
<?= $this->endSection() ?>