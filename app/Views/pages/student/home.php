<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Painel do Estudante<?= $this->endSection() ?>

<?= $this->section('home_student') ?>
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
        background: radial-gradient(120% 120% at 0% 0%, #0c1427, var(--tw-card)) !important;
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

<!-- Banner -->
<div class="gradient-banner">
    <div>
        <h4>Olá, <?php echo $user->username ?>!</h4>
        <p class="text-light mb-0">
            Bem-vindo de volta ao seu painel de estudante. Acompanhe seu
            desempenho e engajamento dos alunos.
        </p>
    </div>
    <div class="d-flex gap-2 mt-4">
        <a
            href="/student/dashboard/meus_cursos"
            style="text-decoration: none"
            class="btn-modern btn-modern-primary">
            Ver Meus Cursos
        </a>
        <a
            href="/student/dashboard/cursos"
            style="text-decoration: none"
            class="btn-modern bg-light text-dark">
            Ver Todos Cursos
        </a>
    </div>
</div>

<!-- Coluna: Cursos em andamento -->
<div class="col-lg-5 tw-dark my-3">
    <div class="tw-card p-3 p-md-4 h-100">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="tw-title mb-0"><i class="bi bi-play-circle me-2"></i>Cursos em andamento</h5>
            <span class="badge badge-soft px-3 py-2"><?= count($activeCourseIds) ?> ativos</span>
        </div>

        <div class="d-flex flex-column gap-3">
            <?php foreach ($courses as $course): ?>
                <?php if (in_array($course->id_course,  $activeCourseIds)): ?>
                    <div class="p-3" style="background: var(--tw-soft); border:1px solid var(--tw-border); border-radius: .875rem;">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-semibold" style="color:#e5e7eb;"><?= $course->title_course ?></span>
                                    <span class="badge badge-soft ms-1"><?= $course->category ?? 'Curso' ?></span>
                                </div>
                                <div class="tw-sub small mb-2">Instrutor: <?= $course->name_instructor ?? 'N/A' ?></div>
                            </div>
                            <a href="/student/dashboard/meus_cursos" class="btn btn-sm btn-accent">
                                <i class="bi bi-arrow-right-circle me-1"></i>Continuar
                            </a>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between small tw-sub mb-1">
                                <span>Progresso</span>
                                <span>40%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width:40%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>