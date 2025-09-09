<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Meus Cursos<?= $this->endSection() ?>

<?= $this->section('my_courses') ?>
<!-- CSS opcional para dar efeito hover/active mais elegante -->
<style>
  .filter-btn {
    transition: all 0.3s;
    border-radius: 999px;
    /* deixa os botões em formato pill */
  }

  .filter-btn:hover {
    background-color: #9e42c3ff;
    color: #fff;
    border-color: #9e42c3ff;
  }

  .filter-btn.active {
    background-color: #9e42c3ff;
    color: #fff;
    border-color: #9e42c3ff;
  }
</style>

<!-- Cursos -->
<div class="container-fluid my-4">
  <h4 class="fw-bold mb-4">Cursos</h4>

  <!-- Filtros + Ações -->
  <div class="row d-flex justify-content-between align-items-center mb-4">
    <!-- Botões de Filtro -->
    <div class="col-lg-6 d-flex flex-wrap gap-2 mb-2">
      <button type="button" class="btn btn-outline-primary filter-btn active" data-filter="all">Todos</button>
      <button type="button" class="btn btn-outline-primary filter-btn" data-filter="Ativo">Ativos</button>
      <button type="button" class="btn btn-outline-primary filter-btn" data-filter="Rascunho">Rascunhos</button>
      <button type="button" class="btn btn-outline-primary filter-btn" data-filter="Arquivado">Arquivados</button>
    </div>

    <!-- Ações -->
    <div class="col-lg-6 d-flex align-items-center gap-2 justify-content-lg-end mb-2">
      <input
        type="text"
        id="searchInput"
        class="form-control rounded-pill"
        placeholder="Procurar cursos..."
        style="max-width: 250px" />
      <a href="novo_curso" class="btn btn-primary rounded-pill fw-semibold btn-sm d-flex">
        <i class="bi bi-plus-circle me-2"></i> Criar Novo Curso
      </a>
    </div>
  </div>

  <!-- Grid de Cursos -->
  <div class="row d-flex g-4" id="coursesContainer">
    <?php foreach ($courses as $course): ?>
      <div class="col-md-4 course-card" data-status="<?= $course->status_course ?>" data-title="<?= strtolower($course->title_course) ?>">
        <div class="card bg-modern-dark border-0 shadow-sm">
          <div class="position-relative">
            <div class="imagem" style="width: 100%; height: 100%;">
              <img
                src="<?= base_url('assets/instructor/img/courses/' . $course->image_course) ?>"
                class="img-fluid rounded"
                alt="Curso <?= $course->title_course ?>" width="100%" height="100%" />
            </div>
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
              <a href="/instructor/dashboard/meus_cursos/editar/<?= $course->id_course ?>" class="btn btn-outline-primary btn-sm rounded-pill">Editar</a>
              <form action="/instructor/dashboard/meus_cursos/deletar/<?= $course->id_course ?>" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar este curso?');">
                <button type="submit" class="btn btn-danger btn-sm rounded-pill">Eliminar</button>
              </form>
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