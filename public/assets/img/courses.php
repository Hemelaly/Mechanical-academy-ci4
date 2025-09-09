<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Todos Cursos<?= $this->endSection() ?>

<?= $this->section('all_courses') ?>

<style>
    /* Tema dark inspirado no Tailwind */
    .tw-dark {
        --tw-bg: #0b1220;
        /* slate-950/900 */
        --tw-card: #0f172a;
        /* slate-900 */
        --tw-card-2: #111827;
        /* gray-900 */
        --tw-border: #1f2937;
        /* gray-800 */
        --tw-soft: #0b1324;
        --tw-text: #cbd5e1;
        /* slate-300 */
        --tw-text-dim: #94a3b8;
        /* slate-400 */
        --tw-accent: #38bdf8;
        /* sky-400 */
        --tw-accent-2: #22d3ee;
        /* cyan-400 */
        --tw-success: #10b981;
        /* emerald-500 */
        --tw-warning: #f59e0b;
        /* amber-500 */
        --tw-danger: #ef4444;
        /* red-500 */
        color: var(--tw-text);
    }

    .tw-dark .tw-card {
        background: radial-gradient(120% 120% at 0% 0%, #0c1427, var(--tw-card));
        border: 1px solid var(--tw-border);
        border-radius: 1rem;
        /* ~ rounded-2xl */
        box-shadow: 0 10px 25px rgba(0, 0, 0, .45), inset 0 1px 0 rgba(255, 255, 255, .02);
    }

    .tw-dark .tw-card:hover {
        border-color: #2a3a52;
    }

    .tw-dark .tw-title {
        color: #e2e8f0;
        /* slate-200 */
        letter-spacing: .2px;
    }

    .tw-dark .tw-sub {
        color: var(--tw-text-dim);
    }

    .tw-dark .btn-tw {
        background: linear-gradient(180deg, #0e1a33, #0b1324);
        color: var(--tw-text);
        border: 1px solid var(--tw-border);
        border-radius: .875rem;
    }

    .tw-dark .btn-tw:hover {
        border-color: #354964;
        color: #f1f5f9;
    }

    .tw-dark .btn-accent {
        background: linear-gradient(180deg, #1b3c5a, #132a41);
        border-color: #265d86;
        color: #e6f6ff;
    }

    .tw-dark .badge-soft {
        background: rgba(56, 189, 248, .12);
        color: #7dd3fc;
        border: 1px solid rgba(125, 211, 252, .18);
        border-radius: 999px;
        font-weight: 500;
    }

    .tw-dark .progress {
        height: .6rem;
        background: #0a1529;
        border: 1px solid #132036;
        border-radius: 999px;
    }

    .tw-dark .progress-bar {
        background: linear-gradient(90deg, var(--tw-accent), var(--tw-accent-2));
    }

    .tw-dark .search-input {
        background: #0a1529;
        border: 1px solid #132036;
        color: var(--tw-text);
        border-radius: .875rem;
    }

    .tw-dark .search-input::placeholder {
        color: #5b708e;
    }

    .tw-dark .tw-grid-gap {
        row-gap: 1rem;
    }
</style>

<div class="tw-dark py-4 py-md-5">
    <div class="container-xxl">

        <!-- Header + Ações -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
            <div class="mb-3 mb-md-0">
                <h2 class="tw-title mb-1">Cursos</h2>
                <div class="tw-sub">Acompanha o teu progresso e explora novos conteúdos</div>
            </div>
            <div class="d-flex gap-2">
                <input type="text" class="form-control search-input" placeholder="Pesquisar cursos...">
                <button class="btn btn-tw d-flex"><i class="bi bi-sliders me-2"></i>Filtros</button>
            </div>
        </div>

        <!-- Coluna: Cursos em andamento -->
        <div class="col-lg-5 my-3">
            <div class="tw-card p-3 p-md-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="tw-title mb-0"><i class="bi bi-play-circle me-2"></i>Cursos em andamento</h5>
                    <span class="badge badge-soft px-3 py-2">2 ativos</span>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div class="p-3" style="background: var(--tw-soft); border:1px solid var(--tw-border); border-radius: .875rem;">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-semibold" style="color:#e5e7eb;">Curso de PHP</span>
                                    <span class="badge badge-soft ms-1">Backend</span>
                                </div>
                                <div class="tw-sub small mb-2">Instrutor: João Silva</div>
                            </div>
                            <a href="#" class="btn btn-sm btn-accent">
                                <i class="bi bi-arrow-right-circle me-1"></i>Continuar
                            </a>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between small tw-sub mb-1">
                                <span>Progresso</span>
                                <span>40%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 40%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3" style="background: var(--tw-soft); border:1px solid var(--tw-border); border-radius: .875rem;">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-semibold" style="color:#e5e7eb;">Curso de JavaScript</span>
                                    <span class="badge badge-soft ms-1">Frontend</span>
                                </div>
                                <div class="tw-sub small mb-2">Instrutor: Maria Santos</div>
                            </div>
                            <a href="#" class="btn btn-sm btn-accent">
                                <i class="bi bi-arrow-right-circle me-1"></i>Continuar
                            </a>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between small tw-sub mb-1">
                                <span>Progresso</span>
                                <span>70%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 70%;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Coluna: Todos os cursos disponíveis -->
        <div class="col-12">
            <div class="tw-card p-3 p-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="tw-title mb-0"><i class="bi bi-collection me-2"></i>Todos os cursos disponíveis</h5>
                    <div class="tw-sub small">6 cursos</div>
                </div>

                <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3 tw-grid-gap">
                    <?php foreach($courses as $key => $course): ?>
                    <div class="col">
                        <div class="p-3 h-100 d-flex flex-column justify-content-between" style="background: var(--tw-soft); border:1px solid var(--tw-border); border-radius: .875rem;">
                            <div>
                                <div class="image">
                                    <img src="<?= base_url('assets/instru') ?>" alt="">
                                </div>
                                <div class="d-flex align-items-start justify-content-between gap-3">
                                    <div>
                                        <div class="fw-semibold mb-1" style="color:#e5e7eb;">Curso de Laravel</div>
                                        <div class="tw-sub small mb-2">Instrutor: Pedro Lima</div>
                                    </div>
                                    <span class="badge badge-soft">Avançado</span>
                                </div>
                                <div class="tw-sub small">Aprenda Laravel para construir aplicações web robustas…</div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-3 pt-2" style="border-top:1px dashed #1b2941;">
                                <div class="fw-semibold">MT 2.500,00</div>
                                <div class="d-flex gap-2">
                                    <a href="#" class="btn btn-sm btn-tw"><i class="bi bi-eye me-1"></i>Ver</a>
                                    <a href="#" class="btn btn-sm btn-accent"><i class="bi bi-plus-circle me-1"></i>Inscrever</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach ?>

                    <!-- <div class="col">
              <div class="p-3 h-100 d-flex flex-column justify-content-between" style="background: var(--tw-soft); border:1px solid var(--tw-border); border-radius: .875rem;">
                <div>
                  <div class="d-flex align-items-start justify-content-between gap-3">
                    <div>
                      <div class="fw-semibold mb-1" style="color:#e5e7eb;">Curso de Python</div>
                      <div class="tw-sub small mb-2">Instrutor: Ana Costa</div>
                    </div>
                    <span class="badge badge-soft">Iniciante</span>
                  </div>
                  <div class="tw-sub small">Aprenda os fundamentos de Python para análise de dados…</div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3 pt-2" style="border-top:1px dashed #1b2941;">
                  <div class="fw-semibold">Gratuito</div>
                  <div class="d-flex gap-2">
                    <a href="#" class="btn btn-sm btn-tw"><i class="bi bi-eye me-1"></i>Ver</a>
                    <a href="#" class="btn btn-sm btn-accent"><i class="bi bi-plus-circle me-1"></i>Inscrever</a>
                  </div>
                </div>
              </div>
            </div>

            <div class="col">
              <div class="p-3 h-100 d-flex flex-column justify-content-between" style="background: var(--tw-soft); border:1px solid var(--tw-border); border-radius: .875rem;">
                <div>
                  <div class="d-flex align-items-start justify-content-between gap-3">
                    <div>
                      <div class="fw-semibold mb-1" style="color:#e5e7eb;">Curso de React</div>
                      <div class="tw-sub small mb-2">Instrutor: Carlos Mendes</div>
                    </div>
                    <span class="badge badge-soft">Intermédio</span>
                  </div>
                  <div class="tw-sub small">Construa interfaces modernas e reativas com ReactJS…</div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3 pt-2" style="border-top:1px dashed #1b2941;">
                  <div class="fw-semibold">MT 1.800,00</div>
                  <div class="d-flex gap-2">
                    <a href="#" class="btn btn-sm btn-tw"><i class="bi bi-eye me-1"></i>Ver</a>
                    <a href="#" class="btn btn-sm btn-accent"><i class="bi bi-plus-circle me-1"></i>Inscrever</a>
                  </div>
                </div>
              </div>
            </div> -->
                    <!-- Repete cards para outros cursos -->
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<?= $this->endSection() ?>