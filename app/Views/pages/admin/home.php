<!-- app/Views/pages/home.php -->
<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Dashboard do Admin<?= $this->endSection() ?>

<?= $this->section('home_admin') ?>

<?php
/**
 * ESPERADO DO CONTROLLER (exemplos):
 * $userName = 'João';
 * $periodoLabel = 'Últimos 30 dias';
 * $stats = [
 *   ['icon' => 'bi-people',          'label' => 'Total de Usuários', 'value' => 3782,  'delta' => 11.01],
 *   ['icon' => 'bi-play-circle',     'label' => 'Cursos Ativos',     'value' => 142,   'delta' => 5.3],
 *   ['icon' => 'bi-clock-history',   'label' => 'Tempo Médio',       'value' => '42min','delta' => -9.05],
 *   ['icon' => 'bi-currency-dollar', 'label' => 'Receita',           'value' => 24580, 'delta' => 15.8, 'prefix' => 'R$ '],
 * ];
 * $activity = [
 *   ['icon'=>'bi-person-plus','variant'=>'primary','title'=>'Novo usuário registrado — Maria Silva','time'=>'Há 5 minutos'],
 *   ['icon'=>'bi-credit-card','variant'=>'success','title'=>'Pagamento confirmado — Curso de JavaScript Avançado','time'=>'Há 32 minutos'],
 *   ['icon'=>'bi-chat-dots','variant'=>'warning','title'=>'Novo comentário no curso de UX Design','time'=>'Há 1 hora'],
 *   ['icon'=>'bi-award','variant'=>'info','title'=>'Conquista: 50 usuários completaram o curso','time'=>'Há 2 horas'],
 * ];
 * $popularCourses = [
 *   ['icon'=>'bi-code-slash','variant'=>'primary','name'=>'JavaScript Moderno','progress'=>85,'students'=>1245],
 *   ['icon'=>'bi-palette','variant'=>'success','name'=>'UX/UI Design','progress'=>72,'students'=>987],
 *   ['icon'=>'bi-graph-up','variant'=>'warning','name'=>'Data Science','progress'=>64,'students'=>754],
 *   ['icon'=>'bi-phone','variant'=>'info','name'=>'Desenvolvimento Mobile','progress'=>58,'students'=>621],
 * ];
 * $quickActions = [
 *   ['href'=>route_to('admin.courses.new'), 'icon'=>'bi-plus-circle-fill',   'variant'=>'primary', 'label'=>'Adicionar Curso'],
 *   ['href'=>route_to('admin.users'),       'icon'=>'bi-people-fill',        'variant'=>'success', 'label'=>'Gerenciar Usuários'],
 *   ['href'=>route_to('admin.reports'),     'icon'=>'bi-graph-up-arrow',     'variant'=>'warning', 'label'=>'Ver Relatórios'],
 *   ['href'=>route_to('admin.settings'),    'icon'=>'bi-gear-fill',          'variant'=>'info',    'label'=>'Configurações'],
 * ];
 */
helper('number');

$userName      = $userName      ?? 'Administrador';
$periodoLabel  = $periodoLabel  ?? 'Últimos 30 dias';
$stats         = $stats         ?? [];
$activity      = $activity      ?? [];
$popularCourses= $popularCourses?? [];
$quickActions  = $quickActions  ?? [];
?>

<!-- Header -->
<header class="mb-4">
  <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
    <div>
      <h1 class="fw-normal mb-1">Olá, <?= esc($userName) ?>! 👋</h1>
      <p class="text-muted mb-0">Bem-vindo ao painel de controle administrativo</p>
    </div>

    <div class="d-flex align-items-center gap-2">
      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" id="timeRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-calendar-week me-2"></i><?= esc($periodoLabel) ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="timeRangeDropdown">
          <li><button class="dropdown-item" data-range="7">Últimos 7 dias</button></li>
          <li><button class="dropdown-item" data-range="30">Últimos 30 dias</button></li>
          <li><button class="dropdown-item" data-range="90">Últimos 90 dias</button></li>
          <li><button class="dropdown-item" data-range="year">Este ano</button></li>
        </ul>
      </div>

      <form action="<?= route_to('admin.reports.export') ?>" method="get" class="m-0">
        <?= csrf_field() ?>
        <button class="btn btn-primary">
          <i class="bi bi-download me-2" aria-hidden="true"></i><span>Exportar Relatório</span>
        </button>
      </form>
    </div>
  </div>
</header>

<!-- Stats -->
<div class="row g-3 mb-4">
  <?php foreach ($stats as $card): 
        $delta = (float)($card['delta'] ?? 0);
        $isPos = $delta >= 0;
        $value = is_numeric($card['value'] ?? null)
                  ? number_to_amount($card['value'], 0, 'pt_BR')
                  : ($card['value'] ?? '—');
        $prefix = $card['prefix'] ?? '';
  ?>
  <div class="col-12 col-md-6 col-lg-3">
    <section class="bg-card rounded-4 p-4 border border-custom-color h-100 position-relative">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div class="d-flex flex-column gap-2">
          <i class="bi <?= esc($card['icon'] ?? 'bi-info-circle') ?> fs-3 text-custom-secondary" aria-hidden="true"></i>
          <h2 class="h6 text-custom-secondary mb-0"><?= esc($card['label'] ?? '—') ?></h2>
        </div>
        <div class="dropstart">
          <button class="btn btn-sm btn-outline-secondary border-0" type="button" data-bs-toggle="dropdown" aria-label="Mais opções">
            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= route_to('admin.metrics.details') ?>">Ver detalhes</a></li>
            <li><a class="dropdown-item" href="<?= route_to('admin.metrics.export') ?>">Exportar dados</a></li>
          </ul>
        </div>
      </div>

      <div>
        <div class="display-6 fw-semibold lh-1"><?= $prefix . $value ?></div>
        <div class="mt-2 small d-flex align-items-center gap-1 <?= $isPos ? 'text-success' : 'text-danger' ?>">
          <i class="bi <?= $isPos ? 'bi-arrow-up' : 'bi-arrow-down' ?>" aria-hidden="true"></i>
          <span class="fw-medium"><?= number_format(abs($delta), 2, ',', '.') ?>%</span>
          <span class="text-muted">vs período anterior</span>
        </div>
      </div>
    </section>
  </div>
  <?php endforeach; ?>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <section class="bg-card rounded-4 p-4 border border-custom-color h-100">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="h5 mb-0">Visão Geral de Atividade</h3>
        <div class="btn-group btn-group-sm" role="group" aria-label="Tipo de gráfico">
          <input type="radio" class="btn-check" name="chartType" id="users" checked>
          <label class="btn btn-outline-primary" for="users">Usuários</label>
          <input type="radio" class="btn-check" name="chartType" id="revenue">
          <label class="btn btn-outline-primary" for="revenue">Receita</label>
          <input type="radio" class="btn-check" name="chartType" id="engagement">
          <label class="btn btn-outline-primary" for="engagement">Engajamento</label>
        </div>
      </div>

      <div class="ratio ratio-16x9 bg-light rounded-3 d-flex align-items-center justify-content-center">
        <div class="text-center text-muted">
          <i class="bi bi-bar-chart-line fs-1 d-block mb-2" aria-hidden="true"></i>
          <p class="mb-0">Gráfico de Atividade dos Usuários</p>
        </div>
      </div>
    </section>
  </div>

  <div class="col-lg-4">
    <section class="bg-card rounded-4 p-4 border border-custom-color h-100">
      <h3 class="h5 mb-4">Distribuição de Usuários</h3>

      <div class="ratio ratio-1x1 bg-light rounded-3 mb-3 d-flex align-items-center justify-content-center">
        <div class="text-center text-muted">
          <i class="bi bi-pie-chart fs-1 d-block mb-2" aria-hidden="true"></i>
          <p class="mb-0">Gráfico de Distribuição</p>
        </div>
      </div>

      <ul class="list-unstyled m-0">
        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <div class="d-flex align-items-center"><span class="badge bg-primary me-2">●</span>Estudantes Ativos</div><strong>68%</strong>
        </li>
        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <div class="d-flex align-items-center"><span class="badge bg-success me-2">●</span>Instrutores</div><strong>15%</strong>
        </li>
        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <div class="d-flex align-items-center"><span class="badge bg-warning me-2">●</span>Administradores</div><strong>5%</strong>
        </li>
        <li class="d-flex justify-content-between align-items-center py-2">
          <div class="d-flex align-items-center"><span class="badge bg-info me-2">●</span>Outros</div><strong>12%</strong>
        </li>
      </ul>
    </section>
  </div>
</div>

<!-- Activity & Top Courses -->
<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <section class="bg-card rounded-4 p-4 border border-custom-color h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h5 mb-0">Atividade Recente</h3>
        <a href="<?= route_to('admin.activity') ?>" class="btn btn-sm btn-outline-primary">Ver tudo</a>
      </div>

      <div class="list-group list-group-flush">
        <?php foreach ($activity as $i): ?>
          <div class="d-flex align-items-start py-3 border-bottom">
            <div class="rounded-circle p-2 me-3 bg-<?= esc($i['variant'] ?? 'secondary') ?> bg-opacity-10">
              <i class="bi <?= esc($i['icon'] ?? 'bi-info-circle') ?> text-<?= esc($i['variant'] ?? 'secondary') ?>" aria-hidden="true"></i>
            </div>
            <div class="flex-grow-1">
              <p class="mb-1 fw-semibold"><?= esc($i['title'] ?? '—') ?></p>
              <small class="text-muted"><?= esc($i['time'] ?? '') ?></small>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>

  <div class="col-lg-6">
    <section class="bg-card rounded-4 p-4 border border-custom-color h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h5 mb-0">Cursos Populares</h3>
        <a href="<?= route_to('admin.courses') ?>" class="btn btn-sm btn-outline-primary">Ver todos</a>
      </div>

      <?php foreach ($popularCourses as $c): ?>
        <div class="d-flex align-items-center py-3 border-bottom">
          <div class="rounded-3 p-3 me-3 bg-<?= esc($c['variant'] ?? 'secondary') ?> bg-opacity-10">
            <i class="bi <?= esc($c['icon'] ?? 'bi-book') ?> text-<?= esc($c['variant'] ?? 'secondary') ?> fs-4" aria-hidden="true"></i>
          </div>
          <div class="flex-grow-1">
            <h6 class="mb-1"><?= esc($c['name'] ?? '—') ?></h6>
            <div class="d-flex align-items-center">
              <div class="progress flex-grow-1 me-2" style="height:6px;">
                <div class="progress-bar bg-<?= esc($c['variant'] ?? 'secondary') ?>" role="progressbar"
                     style="width: <?= (int)($c['progress'] ?? 0) ?>%"
                     aria-valuenow="<?= (int)($c['progress'] ?? 0) ?>" aria-valuemin="0" aria-valuemax="100">
                </div>
              </div>
              <small class="text-muted"><?= (int)($c['progress'] ?? 0) ?>%</small>
            </div>
          </div>
          <div class="text-end">
            <div class="h6 mb-0"><?= number_to_amount((int)($c['students'] ?? 0), 0, 'pt_BR') ?></div>
            <small class="text-muted">estudantes</small>
          </div>
        </div>
      <?php endforeach; ?>
    </section>
  </div>
</div>

<!-- Quick Actions -->
<section class="bg-card rounded-4 p-4 border border-custom-color">
  <h3 class="h5 mb-4">Ações Rápidas</h3>
  <div class="row g-3">
    <?php foreach ($quickActions as $qa): ?>
      <div class="col-6 col-md-3">
        <a class="text-decoration-none" href="<?= esc($qa['href']) ?>">
          <div class="card h-100 text-center border-0 bg-<?= esc($qa['variant']) ?> bg-opacity-10">
            <div class="card-body">
              <i class="bi <?= esc($qa['icon']) ?> text-<?= esc($qa['variant']) ?> fs-2 mb-2" aria-hidden="true"></i>
              <h6 class="card-title mb-0"><?= esc($qa['label']) ?></h6>
            </div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- SCRIPTS (ex.: Chart.js) -->
<script defer>
  // Exemplo: trocar o período via dropdown (sem backend ainda)
  document.querySelectorAll('[data-range]').forEach(btn => {
    btn.addEventListener('click', () => {
      // Dispare um fetch para atualizar os dados conforme o período...
      // fetch('<?= route_to('admin.dashboard.data') ?>?range=' + btn.dataset.range)
      //   .then(res => res.json()).then(updateDashboard);
    });
  });
</script>

<?= $this->endSection() ?>
