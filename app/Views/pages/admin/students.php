<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Dashboard - Estudantes<?= $this->endSection() ?>

<?= $this->section('students') ?>
<?php
/**
 * ESPERADO DO CONTROLLER (exemplos):
 * $metrics = [
 *   'total'=>2789, 'ativos'=>2410, 'suspensos'=>74, 'novos_mes'=>327
 * ];
 * $filters = [
 *   'q'=>'', 'status'=>'', 'ordem'=>'recentes', 'curso_id'=>'', 'data_ini'=>'', 'data_fim'=>''
 * ];
 * $students = [
 *   [
 *     'id'=>101, 'nome'=>'Maria Silva', 'email'=>'maria@email.com',
 *     'avatar'=>'/uploads/avatars/maria.jpg',
 *     'cursos'=>5, 'progresso'=>72, 'ultimo_acesso'=>'2025-10-29 18:40:00',
 *     'status'=>'ativo'
 *   ],
 *   // ...
 * ];
 * $cursosSelect = [
 *   ['id'=>1,'titulo'=>'JavaScript Moderno'],
 *   ['id'=>2,'titulo'=>'UX/UI Design'],
 * ];
 * $pagerHtml = ''; // opcional
 */
helper(['number', 'text']);

$metrics       = $metrics       ?? ['total' => 0, 'ativos' => 0, 'suspensos' => 0, 'novos_mes' => 0];
$filters       = $filters       ?? ['q' => '', 'status' => '', 'ordem' => 'recentes', 'curso_id' => '', 'data_ini' => '', 'data_fim' => ''];
$students      = $students      ?? [];
$cursosSelect  = $cursosSelect  ?? [];
?>

<style>
    .mx-card {
        background: linear-gradient(180deg, rgba(255, 255, 255, .08), rgba(255, 255, 255, .05));
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 18px;
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

    .mx-avatar {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 50%;
    }

    .mx-chip {
        border: 1px solid rgba(255, 255, 255, .2);
        background: rgba(255, 255, 255, .06);
        border-radius: 999px;
        padding: .15rem .55rem;
    }

    .mx-toolbar {
        gap: .5rem;
        flex-wrap: wrap;
    }

    .form-select,
    .form-control {
        background-color: transparent;
    }
</style>

<!-- Header / Toolbar -->
<header class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 mb-1">Estudantes</h1>
        <p class="text-muted mb-0">Gerencie inscrições, status e progresso dos alunos.</p>
    </div>
    <div class="d-flex mx-toolbar">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-upload me-2"></i>Importar/Exportar
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= route_to('admin.students.import') ?>">Importar CSV</a></li>
                <li><a class="dropdown-item" href="<?= route_to('admin.students.export') ?>">Exportar CSV</a></li>
            </ul>
        </div>
        <a href="<?= route_to('admin.students.new') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-2" aria-hidden="true"></i>Novo Estudante
        </a>
    </div>
</header>

<!-- KPIs -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="mx-card p-3 mx-kpi h-100">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Total</span><i class="bi bi-people" aria-hidden="true"></i>
            </div>
            <div class="display-6 fw-semibold"><?= number_to_amount($metrics['total'], 2, 'pt_BR') ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mx-card p-3 mx-kpi h-100">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Ativos</span><i class="bi bi-check2-circle text-success" aria-hidden="true"></i>
            </div>
            <div class="display-6 fw-semibold"><?= number_to_amount($metrics['ativos'], 2, 'pt_BR') ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mx-card p-3 mx-kpi h-100">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Suspensos</span><i class="bi bi-slash-circle text-warning" aria-hidden="true"></i>
            </div>
            <div class="display-6 fw-semibold"><?= number_to_amount($metrics['suspensos'], 2, 'pt_BR') ?></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="mx-card p-3 mx-kpi h-100">
            <div class="d-flex justify-content-between">
                <span class="text-muted">Novos (mês)</span><i class="bi bi-person-check" aria-hidden="true"></i>
            </div>
            <div class="display-6 fw-semibold"><?= number_to_amount($metrics['novos_mes'], 2, 'pt_BR') ?></div>
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
                <input type="text" name="q" value="<?= esc($filters['q']) ?>" class="form-control" placeholder="Nome, e-mail, ID...">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Todos</option>
                <option value="ativo" <?= $filters['status'] === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                <option value="pendente" <?= $filters['status'] === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                <option value="suspenso" <?= $filters['status'] === 'suspenso' ? 'selected' : '' ?>>Suspenso</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label">Curso</label>
            <select name="curso_id" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($cursosSelect as $opt): ?>
                    <option value="<?= (int)$opt['id'] ?>" <?= (string)$filters['curso_id'] === (string)$opt['id'] ? 'selected' : '' ?>>
                        <?= esc($opt['titulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-1">
            <label class="form-label">Ordenar</label>
            <select name="ordem" class="form-select">
                <option value="recentes" <?= $filters['ordem'] === 'recentes' ? 'selected' : '' ?>>Recentes</option>
                <option value="nome_az" <?= $filters['ordem'] === 'nome_az' ? 'selected' : '' ?>>Nome (A-Z)</option>
                <option value="mais_cursos" <?= $filters['ordem'] === 'mais_cursos' ? 'selected' : '' ?>>+ Cursos</option>
                <option value="maior_prog" <?= $filters['ordem'] === 'maior_prog' ? 'selected' : '' ?>>Maior Progresso</option>
            </select>
        </div>
        <div class="col-6 col-md-2 d-grid">
            <button class="btn btn-primary"><i class="bi bi-funnel me-2"></i>Filtrar</button>
        </div>
    </div>
    <div class="row g-2 mt-2">
        <div class="col-6 col-md-2">
            <label class="form-label">De</label>
            <input type="date" class="form-control" name="data_ini" value="<?= esc($filters['data_ini']) ?>">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label">Até</label>
            <input type="date" class="form-control" name="data_fim" value="<?= esc($filters['data_fim']) ?>">
        </div>
    </div>
</form>

<!-- Ações em massa -->
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
    <div class="btn-group">
        <button class="btn btn-outline-secondary btn-sm" id="bulkEmail" disabled>
            <i class="bi bi-envelope me-1"></i>Enviar e-mail
        </button>
        <button class="btn btn-outline-warning btn-sm" id="bulkSuspend" disabled>
            <i class="bi bi-slash-circle me-1"></i>Suspender
        </button>
        <button class="btn btn-outline-success btn-sm" id="bulkActivate" disabled>
            <i class="bi bi-check2-circle me-1"></i>Ativar
        </button>
        <button class="btn btn-outline-danger btn-sm" id="bulkDelete" disabled>
            <i class="bi bi-trash me-1"></i>Excluir
        </button>
    </div>

    <div class="btn-group" role="group" aria-label="Export">
        <a class="btn btn-outline-secondary btn-sm" href="<?= route_to('admin.students.export') ?>">
            <i class="bi bi-download me-1"></i>Exportar lista
        </a>
    </div>
</div>

<!-- Tabela -->
<section class="mx-card p-0 overflow-hidden">
    <?php if (empty($students)): ?>
        <div class="p-4 text-center text-muted">
            <p class="mb-2">Nenhum estudante encontrado.</p>
            <a href="<?= route_to('admin.students.new') ?>" class="btn btn-primary btn-sm">Adicionar estudante</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width:40px"><input class="form-check-input" type="checkbox" id="checkAll" aria-label="Selecionar todos"></th>
                        <th>Estudante</th>
                        <th>E-mail</th>
                        <th class="text-center">Cursos</th>
                        <th class="text-center">Progresso</th>
                        <th>Último acesso</th>
                        <th>Status</th>
                        <th style="width:110px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td><input class="form-check-input row-check" type="checkbox" value="<?= (int)$s['id'] ?>"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?= esc($s['avatar'] ?? '/assets/img/avatar-placeholder.png') ?>" class="mx-avatar me-3" alt="Avatar">
                                    <div>
                                        <a href="<?= route_to('admin.students.show', $s['id']) ?>" class="fw-semibold text-decoration-none"><?= esc($s['nome']) ?></a>
                                        <div class="small text-muted">ID #<?= (int)$s['id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><a href="mailto:<?= esc($s['email']) ?>" class="text-decoration-none"><?= esc($s['email']) ?></a></td>
                            <td class="text-center"><span class="mx-chip"><?= (int)($s['cursos'] ?? 0) ?></span></td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px;">
                                        <div class="progress-bar" role="progressbar" style="width: <?= (int)($s['progresso'] ?? 0) ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?= (int)($s['progresso'] ?? 0) ?>%</small>
                                </div>
                            </td>
                            <td><span class="small text-muted"><?= esc(date('d/m/Y H:i', strtotime($s['ultimo_acesso'] ?? 'now'))) ?></span></td>
                            <td>
                                <?php
                                $status = $s['status'] ?? 'pendente';
                                $map = ['ativo' => 'success', 'pendente' => 'secondary', 'suspenso' => 'warning'];
                                ?>
                                <span class="badge bg-<?= $map[$status] ?? 'secondary' ?>"><?= ucfirst($status) ?></span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= route_to('admin.students.show', $s['id']) ?>" class="btn btn-outline-secondary" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= route_to('admin.students.edit', $s['id']) ?>" class="btn btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-delete" data-id="<?= (int)$s['id'] ?>" title="Excluir">
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
            <small class="text-muted">Mostrando <?= count($students) ?> de <?= number_to_amount($metrics['total'] ?? count($students), 0, 'pt_BR') ?> estudantes</small>
            <div><?= $pagerHtml ?? '' ?></div>
        </div>
    <?php endif; ?>
</section>

<!-- Modal excluir -->
<div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="<?= route_to('admin.students.delete') ?>" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteLabel">Excluir estudante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir o estudante <strong id="delStudentId">#</strong>? Esta ação não pode ser desfeita.
                <input type="hidden" name="id" id="delInputId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Excluir</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal e-mail em massa -->
<div class="modal fade" id="modalEmail" tabindex="-1" aria-labelledby="modalEmailLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="<?= route_to('admin.students.bulkEmail') ?>" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalEmailLabel">Enviar e-mail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="ids" id="emailIds">
                <div class="mb-3">
                    <label class="form-label">Assunto</label>
                    <input type="text" name="assunto" class="form-control" required>
                </div>
                <div class="mb-0">
                    <label class="form-label">Mensagem</label>
                    <textarea name="mensagem" class="form-control" rows="5" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script>
    // Seleção em massa
    const checkAll = document.getElementById('checkAll');
    const rowChecks = document.querySelectorAll('.row-check');
    const bulkBtns = [
        document.getElementById('bulkEmail'),
        document.getElementById('bulkSuspend'),
        document.getElementById('bulkActivate'),
        document.getElementById('bulkDelete')
    ];

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
    const modalDelete = document.getElementById('modalDelete');
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            document.getElementById('delStudentId').textContent = `#${id}`;
            document.getElementById('delInputId').value = id;
            new bootstrap.Modal(modalDelete).show();
        });
    });

    // Coleta IDs selecionados
    function selectedIds() {
        return Array.from(rowChecks).filter(c => c.checked).map(c => c.value);
    }

    // E-mail em massa
    const modalEmail = document.getElementById('modalEmail');
    document.getElementById('bulkEmail')?.addEventListener('click', () => {
        document.getElementById('emailIds').value = selectedIds().join(',');
        new bootstrap.Modal(modalEmail).show();
    });

    // Post helper para ações em massa
    function postBulk(action) {
        const ids = selectedIds();
        if (!ids.length) return;
        const form = document.createElement('form');
        form.method = 'post';
        form.action = action;
        form.innerHTML = '<?= csrf_field() ?>' + ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('');
        document.body.appendChild(form);
        form.submit();
    }
    document.getElementById('bulkSuspend')?.addEventListener('click', () => postBulk('<?= route_to('admin.students.bulkSuspend') ?>'));
    document.getElementById('bulkActivate')?.addEventListener('click', () => postBulk('<?= route_to('admin.students.bulkActivate') ?>'));
    document.getElementById('bulkDelete')?.addEventListener('click', () => postBulk('<?= route_to('admin.students.bulkDelete') ?>'));
</script>

<?= $this->endSection() ?>