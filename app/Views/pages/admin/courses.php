<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Dashboard - Cursos<?= $this->endSection() ?>

<?= $this->section('courses') ?>

<?php

// dd($courses);

/**
 * ESPERADO DO CONTROLLER (exemplos):
 * $metrics = [
 *   'total' => 142, 'ativos' => 118, 'rascunhos' => 16, 'arquivados' => 8,
 *   'receita_mes' => 24580, 'novos_alunos_mes' => 327
 * ];
 * $filters = [
 *   'q' => 'js', 'status' => 'ativo', 'categoria' => 'programacao', 'ordem' => 'recentes'
 * ];
 * $courses = [
 *   [
 *     'id'=>1,'thumb'=>'/uploads/courses/js.jpg','titulo'=>'JavaScript Moderno',
 *     'categoria'=>'Programação','aulas'=>52,'alunos'=>1245,'preco'=>199.9,
 *     'status'=>'ativo','atualizado_em'=>'2025-10-12 14:25:00','instrutor'=>'Ana Souza',
 *     'progresso'=>85
 *   ],
 *   // ...
 * ];
 * $pagerHtml = ''; // opcional: HTML do pager
 */
helper(['number', 'text']);

$metrics = $metrics ?? ['total' => 0, 'ativos' => 0, 'rascunhos' => 0, 'arquivados' => 0, 'receita_mes' => 0, 'novos_alunos_mes' => 0];
$filters = $filters ?? ['q' => '', 'status' => '', 'categoria' => '', 'ordem' => 'recentes'];
$courses = $courses ?? [];
?>

<style>
    .mx-card {
        background: linear-gradient(180deg, rgba(255, 255, 255, .08), rgba(255, 255, 255, .05));
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 18px;
    }

    /* Tabela no mesmo bg dos cards (dentro de .mx-card) */
    .mx-card .table {
        --bs-table-color: #fff;
        --bs-table-bg: transparent;
        /* fundo da célula */
        --bs-table-striped-bg: rgba(255, 255, 255, .05);
        --bs-table-hover-bg: rgba(255, 255, 255, .06);
        --bs-border-color: rgba(255, 255, 255, .12);
        /* cor das bordas */
    }

    /* Cabeçalho e corpo sem chapado */
    .mx-card .table thead tr,
    .mx-card .table thead th,
    .mx-card .table tbody tr {
        background-color: transparent !important;
    }

    /* Opcional: deixar a célula “th” com borda mais discreta */
    .mx-card .table thead th {
        border-bottom-color: rgba(255, 255, 255, .12);
    }

    /* Opcional: ícones/botões na tabela com contraste ok */
    .mx-card .table .btn-outline-secondary {
        --bs-btn-color: #cfd3da;
        --bs-btn-border-color: rgba(255, 255, 255, .25) !important;
        --bs-btn-hover-bg: rgba(255, 255, 255, .08) !important;
        --bs-btn-hover-border-color: rgba(255, 255, 255, .35) !important;
    }

    .mx-kpi {
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .mx-kpi:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, .25);
    }

    .table> :not(caption)>*>* {
        vertical-align: middle;
    }

    .mx-thumb {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 10px;
    }

    .mx-badge {
        border: 1px solid rgba(255, 255, 255, .2);
        background: rgba(255, 255, 255, .06);
    }

    .mx-toolbar {
        gap: .5rem;
        flex-wrap: wrap;
    }

    .mx-grid .card {
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 16px;
        transition: transform .12s ease;
    }

    .mx-grid .card:hover {
        transform: translateY(-3px);
    }

    .form-select,
    .form-control {
        background-color: transparent;
    }

    .table-dark {
        background: linear-gradient(180deg, rgba(255, 255, 255, .08), rgba(255, 255, 255, .05)) !important;
    }
</style>

<!-- Header / Toolbar -->
<header class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 mb-1">Gestão de Cursos</h1>
        <p class="text-muted mb-0">Crie, edite e acompanhe o desempenho dos seus cursos.</p>
    </div>
    <div class="d-flex mx-toolbar">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-upload me-2"></i>Importar/Exportar
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= route_to('admin.courses.import') ?>">Importar CSV</a></li>
                <li><a class="dropdown-item" href="<?= route_to('admin.courses.export') ?>">Exportar CSV</a></li>
            </ul>
        </div>
        <a href="<?= route_to('admin.courses.new') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2" aria-hidden="true"></i>Novo Curso
        </a>
    </div>
</header>

<!-- KPIs -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="mx-card p-3 mx-kpi h-100">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Total</span><i class="bi bi-collection" aria-hidden="true"></i>
            </div>
            <div class="display-6 fw-semibold"><?= number_to_amount(count($courses), 2, 'pt_BR') ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mx-card p-3 mx-kpi h-100">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Ativos</span><i class="bi bi-check2-circle text-success" aria-hidden="true"></i>
            </div>
            <div class="display-6 fw-semibold"><?= number_to_amount($activeCourses, 2, 'pt_BR') ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mx-card p-3 mx-kpi h-100">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Receita (mês)</span><i class="bi bi-currency-dollar" aria-hidden="true"></i>
            </div>
            <div class="h2 fw-semibold"><?= number_to_amount($metrics['receita_mes'] ?? 0, 2, 'pt_BR') ?> MZN</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mx-card p-3 mx-kpi h-100">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Novos alunos</span><i class="bi bi-person-plus" aria-hidden="true"></i>
            </div>
            <div class="display-6 fw-semibold"><?= number_to_amount($metrics['novos_alunos_mes'] ?? 0, 2, 'pt_BR') ?></div>
        </div>
    </div>
</div>

<!-- Filtros e busca -->
<form action="<?= current_url() ?>" method="get" class="mx-card p-3 mb-3">
    <?= csrf_field() ?>
    <div class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label">Buscar</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="<?= esc($filters['q']) ?>" class="form-control" placeholder="Título, instrutor, ID...">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Todos</option>
                <option value="ativo" <?= $filters['status'] === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                <option value="rascunho" <?= $filters['status'] === 'rascunho' ? 'selected' : '' ?>>Rascunho</option>
                <option value="arquivado" <?= $filters['status'] === 'arquivado' ? 'selected' : '' ?>>Arquivado</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label">Categoria</label>
            <select name="categoria" class="form-select">
                <option value="">Todas</option>
                <!-- popule dinamicamente -->
                <option value="programacao" <?= $filters['categoria'] === 'programacao' ? 'selected' : '' ?>>Programação</option>
                <option value="design" <?= $filters['categoria'] === 'design' ? 'selected' : '' ?>>Design</option>
                <option value="dados" <?= $filters['categoria'] === 'dados' ? 'selected' : '' ?>>Dados</option>
            </select>
        </div>
        <div class="col-6 col-md-1">
            <label class="form-label">Ordenar por</label>
            <select name="ordem" class="form-select">
                <option value="recentes" <?= $filters['ordem'] === 'recentes' ? 'selected' : '' ?>>Mais recentes</option>
                <option value="populares" <?= $filters['ordem'] === 'populares' ? 'selected' : '' ?>>Mais populares</option>
                <option value="melhor_nota" <?= $filters['ordem'] === 'melhor_nota' ? 'selected' : '' ?>>Melhor avaliação</option>
                <option value="preco_alto" <?= $filters['ordem'] === 'preco_alto' ? 'selected' : '' ?>>Preço: maior</option>
                <option value="preco_baixo" <?= $filters['ordem'] === 'preco_baixo' ? 'selected' : '' ?>>Preço: menor</option>
            </select>
        </div>
        <div class="col-6 col-md-2 d-grid">
            <button class="btn btn-primary"><i class="bi bi-funnel me-2"></i>Filtrar</button>
        </div>
        <div class="col-6 col-md-0 d-none d-md-block"></div>
    </div>
</form>

<!-- Barra de ações em massa e alternância de visualização -->
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
    <div class="btn-group">
        <button class="btn btn-outline-secondary btn-sm" id="bulkPublish" disabled>
            <i class="bi bi-check2-circle me-1"></i>Publicar
        </button>
        <button class="btn btn-outline-secondary btn-sm" id="bulkArchive" disabled>
            <i class="bi bi-archive me-1"></i>Arquivar
        </button>
        <button class="btn btn-outline-danger btn-sm" id="bulkDelete" disabled>
            <i class="bi bi-trash me-1"></i>Excluir
        </button>
    </div>

    <div class="btn-group" role="group" aria-label="Visualização">
        <input type="radio" class="btn-check" name="viewMode" id="viewTable" checked>
        <label class="btn btn-outline-secondary btn-sm" for="viewTable"><i class="bi bi-list-ul me-1"></i>Tabela</label>
        <input type="radio" class="btn-check" name="viewMode" id="viewGrid">
        <label class="btn btn-outline-secondary btn-sm" for="viewGrid"><i class="bi bi-grid-3x3-gap me-1"></i>Grade</label>
    </div>
</div>

<!-- Lista (Tabela) -->
<section id="tableView" class="mx-card p-0 overflow-hidden">
    <?php if (empty($courses)): ?>
        <div class="p-4 text-center text-muted">
            <p class="mb-2">Nenhum curso encontrado.</p>
            <a href="<?= route_to('admin.courses.new') ?>" class="btn btn-primary btn-sm">Criar primeiro curso</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-dark align-middle mb-0">
                <thead class="">
                    <tr>
                        <th style="width:40px">
                            <input class="form-check-input" type="checkbox" id="checkAll" aria-label="Selecionar todos">
                        </th>
                        <th>Curso</th>
                        <th class="text-center">Aulas</th>
                        <th class="text-center">Alunos</th>
                        <th class="text-center">Preço</th>
                        <th>Instrutor</th>
                        <th>Atualizado</th>
                        <th>Status</th>
                        <th style="width:72px">Acções</th>
                    </tr>
                </thead>
                <tbody class="">
                    <?php foreach ($courses as $key => $c): ?>
                        <tr class="">
                            <td>
                                <input class="form-check-input row-check" type="checkbox" value="<?= (int)$c->id_course ?>">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= esc('/assets/instructor/img/courses/' . $c->image_course ?? '/assets/img/placeholder.webp') ?>" alt="><?= esc($c->image_course) ?>" class="mx-thumb me-3">
                                    <div>
                                        <a href="<?= route_to('admin.courses.edit', $c->id_course) ?>" class="fw-semibold text-decoration-none"><?= esc($c->title_course) ?></a>
                                        <div class="small text-muted">
                                            <!-- <span class="badge rounded-pill mx-badge me-1"></span> -->
                                            <span class="text-muted">ID #<?= (int)$c->id_course ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center"><?= esc($totalLessons ?? 0) ?></td>
                            <td class="text-center"><?= number_format(($enrolledCounts[$c->id_course] ?? 0), 0, 'pt_BR') ?></td>
                            <td class="text-center"><?= number_format($c->price_course ?? 0, 2) ?> MZN</td>
                            <td><?= esc($courses2->instructor_name ?? '—') ?></td>
                            <td>
                                <span class="small text-muted"><?= esc(date('d/m/Y H:i', strtotime($c->updated_at ?? 'now'))) ?></span>
                            </td>
                            <td>
                                <?php
                                $status = $c->status_course ?? 'rascunho';
                                $map = ['ativo' => 'success', 'rascunho' => 'secondary', 'arquivado' => 'warning'];
                                $label = ucfirst($status);
                                ?>
                                <span class="badge bg-<?= $map[$status] ?? 'secondary' ?>"><?= esc($label) ?></span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= route_to('admin.courses.preview', $c->id_course) ?>" class="btn btn-outline-secondary" title="Pré-visualizar">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= route_to('admin.courses.edit', $c->id_course) ?>" class="btn btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-delete" data-id="<?= (int)$c->id_course ?>" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <small class="text-muted">
                <?php if (!empty($pageMeta) && $pageMeta['total'] > 0): ?>
                    Mostrando <?= number_format($pageMeta['first']) ?>–<?= number_format($pageMeta['last']) ?>
                    de <?= number_format($pageMeta['total']) ?> cursos
                <?php else: ?>
                    Nenhum curso encontrado
                <?php endif; ?>
            </small>

            <div><?= $pagerHtml ?? '' ?></div>
        </div>

    <?php endif; ?>
</section>

<!-- Grade -->
<section id="gridView" class="d-none mx-grid">
    <?php if (empty($courses)): ?>
        <div class="mx-card p-4 text-center text-muted my-3">Nenhum curso para exibir.</div>
    <?php else: ?>
        <div class="row g-3 mt-1">
            <?php foreach ($courses as $c): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card text-white h-100">
                        <img src="<?= esc('/assets/instructor/img/courses/' . $c->image_course ?? '/assets/img/placeholder.webp') ?>" class="card-img-top" alt="Capa do curso" style="height:180px;object-fit:cover;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0 me-2"><?= esc($c->title_course) ?></h5>
                                <?php
                                $status = $c->status_course ?? 'rascunho';
                                $map = ['ativo' => 'success', 'rascunho' => 'secondary', 'arquivado' => 'warning'];
                                ?>
                                <span class="badge bg-<?= $map[$status] ?? 'secondary' ?>"><?= ucfirst($status) ?></span>
                            </div>
                            <p class="card-text small text-muted mb-2">
                                <i class="bi bi-journal-text me-1"></i><?= (int)($totalLessons ?? 0) ?> aulas ·
                                <i class="bi bi-people me-1 ms-2"></i><?= number_to_amount(($enrolledCounts[$c->id_course] ?? 0)) ?> alunos
                            </p>
                            <!-- <div class="d-flex align-items-center gap-2 small text-muted mb-3">
                                <div class="progress flex-grow-1" style="height:6px;">
                                    <div class="progress-bar" role="progressbar" style="width: 47%"></div>
                                </div>
                                <span>47%</span>
                            </div> -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="fw-semibold"><?= number_format($c->price_course ?? 0, 2) ?> MZN</div>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-secondary" href="<?= route_to('admin.courses.edit', $c->id_course) ?>"><i class="bi bi-pencil"></i></a>
                                    <button type="button" class="btn btn-outline-danger btn-delete" data-id="<?= (int)$c->id_course ?>"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer small text-muted">
                            Instrutor: <?= esc($courses2->instructor_name ?? '—') ?> · Atualizado: <?= esc(date('d/m/Y', strtotime($c->updated_at ?? 'now'))) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Modal excluir -->
<div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="<?= route_to('admin.courses.delete') ?>" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteLabel">Excluir curso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir o curso <strong id="delCourseId">#</strong>? Esta ação não pode ser desfeita.
                <input type="hidden" name="id" id="delInputId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Excluir</button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script>
    // Alternar tabela/grade
    const viewTable = document.getElementById('viewTable');
    const viewGrid = document.getElementById('viewGrid');
    const tableView = document.getElementById('tableView');
    const gridView = document.getElementById('gridView');
    viewTable?.addEventListener('change', () => {
        tableView.classList.remove('d-none');
        gridView.classList.add('d-none');
    });
    viewGrid?.addEventListener('change', () => {
        gridView.classList.remove('d-none');
        tableView.classList.add('d-none');
    });

    // Seleção em massa
    const checkAll = document.getElementById('checkAll');
    const rowChecks = document.querySelectorAll('.row-check');
    const bulkBtns = [document.getElementById('bulkPublish'), document.getElementById('bulkArchive'), document.getElementById('bulkDelete')];

    function updateBulk() {
        const any = Array.from(rowChecks).some(c => c.checked);
        bulkBtns.forEach(b => b && (b.disabled = !any));
    }
    checkAll?.addEventListener('change', () => {
        rowChecks.forEach(c => c.checked = checkAll.checked);
        updateBulk();
    });
    rowChecks.forEach(c => c.addEventListener('change', updateBulk));

    // Modal excluir
    const modal = document.getElementById('modalDelete');
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            document.getElementById('delCourseId').textContent = `#${id}`;
            document.getElementById('delInputId').value = id;
            const m = new bootstrap.Modal(modal);
            m.show();
        });
    });

    // Ações em massa (exemplo post simples)
    function postBulk(action) {
        const ids = Array.from(rowChecks).filter(c => c.checked).map(c => c.value);
        if (!ids.length) return;
        const form = document.createElement('form');
        form.method = 'post';
        form.action = action;
        form.innerHTML = '<?= csrf_field() ?>' + ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('');
        document.body.appendChild(form);
        form.submit();
    }
    document.getElementById('bulkPublish')?.addEventListener('click', () => postBulk('<?= route_to('admin.courses.bulkPublish') ?>'));
    document.getElementById('bulkArchive')?.addEventListener('click', () => postBulk('<?= route_to('admin.courses.bulkArchive') ?>'));
    document.getElementById('bulkDelete')?.addEventListener('click', () => postBulk('<?= route_to('admin.courses.bulkDelete') ?>'));
</script>

<?= $this->endSection() ?>