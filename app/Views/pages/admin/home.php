<!-- app/Views/pages/home.php -->
<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Dashboard do Admin<?= $this->endSection() ?>

<?= $this->section('home_admin') ?>
<?php
helper('number');

$userName       = $userName       ?? 'Administrador';
$periodoLabel   = $periodoLabel   ?? 'Últimos 30 dias';
$stats          = $stats          ?? [];
$activity       = $activity       ?? [];
$popularCourses = $popularCourses ?? [];
$quickActions   = $quickActions   ?? [];
?>

<style>
  :root{
    --mx-surface:#0e1324;           /* base dark surface (ajuste se usar tema claro) */
    --mx-card:rgba(255,255,255,.06);/* glass */
    --mx-border:rgba(255,255,255,.12);
    --mx-muted:#9aa3b2;
    --mx-grad-1:#6ea8fe;
    --mx-grad-2:#9a6bff;
    --mx-grad-3:#22d3ee;
    --mx-success:#13c28a;
    --mx-danger:#ef476f;
  }
  .mx-hero{
    background: radial-gradient(1200px 400px at 10% -10%, rgba(110,168,254,.25), transparent 60%),
                radial-gradient(900px 400px at 90% -20%, rgba(154,107,255,.25), transparent 60%);
    border-radius: 24px;
    padding: 24px;
    border:1px solid var(--mx-border);
    backdrop-filter: blur(6px);
  }
  .mx-card{
    background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.05));
    border:1px solid var(--mx-border);
    border-radius: 20px;
  }
  .mx-kpi{
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .mx-kpi:hover{ transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,.25); }
  .mx-kpi__icon{
    width:44px;height:44px; display:grid; place-items:center; border-radius:12px;
    background: linear-gradient(135deg, var(--mx-grad-1), var(--mx-grad-2));
    color:#fff;
  }
  .mx-kpi__delta.up   { color: var(--mx-success); }
  .mx-kpi__delta.down { color: var(--mx-danger); }
  .mx-border{ border:1px solid var(--mx-border) !important; }
  .text-muted-2{ color: var(--mx-muted) !important; }

  .mx-badge-dot{
    display:inline-block; width:.7rem; height:.7rem; border-radius:50%;
  }

  .mx-timeline .item{ position:relative; padding-left:1.75rem; }
  .mx-timeline .item::before{
    content:""; position:absolute; left:.55rem; top:.35rem; width:.55rem; height:.55rem; border-radius:50%;
    background: var(--mx-grad-3);
    box-shadow: 0 0 0 4px rgba(34,211,238,.15);
  }
  .mx-timeline .item + .item{ border-top:1px dashed var(--mx-border); }

  .ratio.mx-skeleton{ background: repeating-linear-gradient(90deg, rgba(255,255,255,.06) 0, rgba(255,255,255,.06) 8px, rgba(255,255,255,.08) 8px, rgba(255,255,255,.08) 16px); border-radius:12px; }

  /* Quick Actions */
  .mx-qa{ transition: transform .12s ease; }
  .mx-qa:hover{ transform: translateY(-2px); }

  /* Preferências de movimento reduzido */
  @media (prefers-reduced-motion: reduce){
    .mx-kpi:hover,.mx-qa:hover{ transform:none; }
  }
</style>

<!-- HERO -->
<section class="mx-hero mb-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
      <h1 class="h3 mb-1">Olá, <?= esc($userName) ?>! 👋</h1>
      <p class="mb-0 text-muted-2">Bem-vindo ao painel administrativo. Aqui vai um panorama rápido do seu negócio.</p>
    </div>

    <div class="d-flex align-items-center gap-2">
      <div class="dropdown">
        <button class="btn btn-outline-light border mx-border dropdown-toggle" id="timeRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-calendar-week me-2"></i><span><?= esc($periodoLabel) ?></span>
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
          <i class="bi bi-download me-2" aria-hidden="true"></i>Exportar
        </button>
      </form>
    </div>
  </div>
</section>

<!-- KPIs -->
<div class="row g-3 mb-4">
  <?php if(empty($stats)): ?>
    <div class="col-12">
      <div class="mx-card p-4 text-center">
        <p class="text-muted-2 mb-0">Sem métricas para o período selecionado.</p>
      </div>
    </div>
  <?php else: ?>
    <?php foreach ($stats as $i => $card):
      $delta = (float)($card['delta'] ?? 0);
      $isPos = $delta >= 0;
      $rawVal = $card['value'] ?? '—';
      $value = is_numeric($rawVal) ? number_to_amount($rawVal, 0, 'pt_BR') : $rawVal;
      $prefix = $card['prefix'] ?? '';
      $icon   = $card['icon'] ?? 'bi-info-circle';
    ?>
      <div class="col-12 col-sm-6 col-lg-3">
        <section class="mx-card p-4 h-100 mx-kpi">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="mx-kpi__icon" aria-hidden="true"><i class="bi <?= esc($icon) ?>"></i></div>
            <button class="btn btn-sm btn-outline-light border-0" type="button" data-bs-toggle="dropdown" aria-label="Mais opções">
              <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="<?= route_to('admin.metrics.details') ?>">Ver detalhes</a></li>
              <li><a class="dropdown-item" href="<?= route_to('admin.metrics.export') ?>">Exportar dados</a></li>
            </ul>
          </div>

          <h2 class="h6 text-muted-2 mb-2"><?= esc($card['label'] ?? '—') ?></h2>
          <div class="display-6 fw-semibold"><?= $prefix . $value ?></div>
          <div class="mt-2 small d-flex align-items-center gap-1 mx-kpi__delta <?= $isPos ? 'up' : 'down' ?>">
            <i class="bi <?= $isPos ? 'bi-arrow-up-right' : 'bi-arrow-down-right' ?>"></i>
            <span class="fw-medium"><?= number_format(abs($delta), 2, ',', '.') ?>%</span>
            <span class="text-muted-2">vs período anterior</span>
          </div>

          <!-- sparkline -->
          <canvas id="spark-<?= $i ?>" height="48" class="mt-3" aria-hidden="true"></canvas>
        </section>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <section class="mx-card p-4 h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h5 mb-0">Visão Geral</h3>
        <div class="btn-group btn-group-sm" role="group" aria-label="Tipo de gráfico">
          <input type="radio" class="btn-check" name="chartType" id="ct-users" checked>
          <label class="btn btn-outline-primary" for="ct-users">Usuários</label>
          <input type="radio" class="btn-check" name="chartType" id="ct-revenue">
          <label class="btn btn-outline-primary" for="ct-revenue">Receita</label>
          <input type="radio" class="btn-check" name="chartType" id="ct-engagement">
          <label class="btn btn-outline-primary" for="ct-engagement">Engajamento</label>
        </div>
      </div>
      <div class="ratio ratio-16x9 mx-skeleton mb-2">
        <canvas id="mainChart" aria-label="Gráfico principal" role="img"></canvas>
      </div>
      <small class="text-muted-2">* Dados ilustrativos com base no período.</small>
    </section>
  </div>

  <div class="col-lg-4">
    <section class="mx-card p-4 h-100">
      <h3 class="h6 mb-3">Distribuição de Usuários</h3>
      <div class="ratio ratio-1x1 mx-skeleton mb-3">
        <canvas id="pieUsers" aria-label="Distribuição de usuários" role="img"></canvas>
      </div>
      <ul class="list-unstyled m-0">
        <li class="d-flex justify-content-between align-items-center py-2 border-top mx-border">
          <div class="d-flex align-items-center"><span class="mx-badge-dot me-2" style="background:#0d6efd;"></span>Estudantes Ativos</div><strong>68%</strong>
        </li>
        <li class="d-flex justify-content-between align-items-center py-2 border-top mx-border">
          <div class="d-flex align-items-center"><span class="mx-badge-dot me-2" style="background:#198754;"></span>Instrutores</div><strong>15%</strong>
        </li>
        <li class="d-flex justify-content-between align-items-center py-2 border-top mx-border">
          <div class="d-flex align-items-center"><span class="mx-badge-dot me-2" style="background:#ffc107;"></span>Administradores</div><strong>5%</strong>
        </li>
        <li class="d-flex justify-content-between align-items-center py-2 border-top mx-border">
          <div class="d-flex align-items-center"><span class="mx-badge-dot me-2" style="background:#0dcaf0;"></span>Outros</div><strong>12%</strong>
        </li>
      </ul>
    </section>
  </div>
</div>

<!-- Activity & Top Courses -->
<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <section class="mx-card p-4 h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h6 mb-0">Atividade Recente</h3>
        <a href="<?= route_to('admin.activity') ?>" class="btn btn-sm btn-outline-primary">Ver tudo</a>
      </div>

      <?php if(empty($activity)): ?>
        <div class="text-center text-muted-2 py-4">Nenhuma atividade registrada.</div>
      <?php else: ?>
        <div class="mx-timeline">
          <?php foreach ($activity as $i): ?>
            <div class="item py-3">
              <div class="d-flex align-items-start">
                <div class="rounded-circle p-2 me-3 bg-<?= esc($i['variant'] ?? 'secondary') ?> bg-opacity-10" aria-hidden="true">
                  <i class="bi <?= esc($i['icon'] ?? 'bi-info-circle') ?> text-<?= esc($i['variant'] ?? 'secondary') ?>"></i>
                </div>
                <div class="flex-grow-1">
                  <p class="mb-1 fw-semibold"><?= esc($i['title'] ?? '—') ?></p>
                  <small class="text-muted-2"><?= esc($i['time'] ?? '') ?></small>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </div>

  <div class="col-lg-6">
    <section class="mx-card p-4 h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h6 mb-0">Cursos Populares</h3>
        <a href="<?= route_to('admin.courses') ?>" class="btn btn-sm btn-outline-primary">Ver todos</a>
      </div>

      <?php if(empty($popularCourses)): ?>
        <div class="text-center text-muted-2 py-4">Sem cursos populares no período.</div>
      <?php else: ?>
        <?php foreach ($popularCourses as $c): ?>
          <div class="d-flex align-items-center py-3 border-top mx-border">
            <div class="rounded-3 p-3 me-3 bg-<?= esc($c['variant'] ?? 'secondary') ?> bg-opacity-10" aria-hidden="true">
              <i class="bi <?= esc($c['icon'] ?? 'bi-book') ?> text-<?= esc($c['variant'] ?? 'secondary') ?> fs-4"></i>
            </div>
            <div class="flex-grow-1">
              <h6 class="mb-1"><?= esc($c['name'] ?? '—') ?></h6>
              <div class="d-flex align-items-center">
                <div class="progress flex-grow-1 me-2" style="height:6px;">
                  <div class="progress-bar bg-<?= esc($c['variant'] ?? 'secondary') ?>"
                       role="progressbar"
                       style="width: <?= (int)($c['progress'] ?? 0) ?>%"
                       aria-valuenow="<?= (int)($c['progress'] ?? 0) ?>" aria-valuemin="0" aria-valuemax="100">
                  </div>
                </div>
                <small class="text-muted-2"><?= (int)($c['progress'] ?? 0) ?>%</small>
              </div>
            </div>
            <div class="text-end">
              <div class="h6 mb-0"><?= number_to_amount((int)($c['students'] ?? 0), 0, 'pt_BR') ?></div>
              <small class="text-muted-2">estudantes</small>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </div>
</div>

<!-- Quick Actions -->
<section class="mx-card p-4 mb-2">
  <h3 class="h6 mb-3">Ações Rápidas</h3>
  <div class="row g-3">
    <?php if(empty($quickActions)): ?>
      <div class="col-12 text-muted-2">Nenhuma ação rápida configurada.</div>
    <?php else: ?>
      <?php foreach ($quickActions as $qa): ?>
        <div class="col-6 col-md-3">
          <a class="text-decoration-none" href="<?= esc($qa['href']) ?>">
            <div class="card h-100 text-center border-0 mx-qa" style="background:linear-gradient(135deg, rgba(13,110,253,.15), rgba(154,107,255,.15)); border-radius:16px;">
              <div class="card-body">
                <i class="bi <?= esc($qa['icon']) ?> fs-2 mb-2"></i>
                <h6 class="card-title mb-0"><?= esc($qa['label']) ?></h6>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" integrity="sha384-JtE2lq8T6H7bU1Yz9Bz1r3H0j/kQy6n4oRk2YgB9z6WmGz4c2mB6pQm6mKx0mZ8u" crossorigin="anonymous"></script>

<script defer>
(function(){
  // Utilitário: tema atual
  const textMuted = getComputedStyle(document.documentElement).getPropertyValue('--bs-secondary-color') || '#9aa3b2';

  // Sementes aleatórias estáveis para demo
  function seededRand(seed){ let x=Math.sin(seed)*10000; return x-Math.floor(x); }
  function series(len, seed, base=100, amp=20){
    return Array.from({length:len}, (_,i)=> Math.max(0, base + Math.round((seededRand(seed+i)-.5)*2*amp)));
  }

  // --------- SPARKLINES ----------
  <?php foreach ($stats as $i => $card): ?>
    (function(){
      const ctx = document.getElementById('spark-<?= $i ?>');
      if(!ctx) return;
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: Array.from({length: 12}, (_,i)=> i+1),
          datasets: [{
            data: series(12, <?= $i+7 ?>, 100 + (<?= (int)($card['delta'] ?? 0) ?>*2), 25),
            borderWidth: 2,
            fill: true,
            tension: .35
          }]
        },
        options:{
          responsive:true,
          plugins:{ legend:{display:false}, tooltip:{enabled:false}},
          elements:{ point:{radius:0}},
          scales:{ x:{display:false}, y:{display:false}}
        }
      });
    })();
  <?php endforeach; ?>

  // --------- MAIN CHART ----------
  const mainCtx = document.getElementById('mainChart');
  if(mainCtx){
    const base = series(12, 42, 120, 40);
    const chart = new Chart(mainCtx, {
      type:'line',
      data:{
        labels:['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        datasets:[
          { label:'Usuários',   data: base,                          borderWidth:2, fill:true, tension:.35 },
          { label:'Receita',    data: base.map((v,i)=> v* (0.7 + (i%3)*0.1)), borderWidth:2, fill:true, tension:.35 },
          { label:'Engajamento',data: base.map((v,i)=> v* (0.5 + (i%4)*0.08)), borderWidth:2, fill:true, tension:.35 }
        ]
      },
      options:{
        plugins:{ legend:{ position:'bottom' } },
        scales:{
          y:{ grid:{ color:'rgba(255,255,255,.08)'} },
          x:{ grid:{ display:false } }
        }
      }
    }

    );
    // troca de dataset via radio
    document.getElementById('ct-users')?.addEventListener('change', ()=> { chart.setDatasetVisibility(0,true); chart.setDatasetVisibility(1,false); chart.setDatasetVisibility(2,false); chart.update(); });
    document.getElementById('ct-revenue')?.addEventListener('change', ()=> { chart.setDatasetVisibility(0,false); chart.setDatasetVisibility(1,true); chart.setDatasetVisibility(2,false); chart.update(); });
    document.getElementById('ct-engagement')?.addEventListener('change', ()=> { chart.setDatasetVisibility(0,false); chart.setDatasetVisibility(1,false); chart.setDatasetVisibility(2,true); chart.update(); });
  }

  // --------- PIE ----------
  const pieCtx = document.getElementById('pieUsers');
  if(pieCtx){
    new Chart(pieCtx, {
      type:'doughnut',
      data:{
        labels:['Estudantes Ativos','Instrutores','Administradores','Outros'],
        datasets:[{ data:[68,15,5,12] }]
      },
      options:{
        cutout:'60%',
        plugins:{ legend:{ display:false } }
      }
    });
  }

  // --------- Range dropdown (exemplo de hook) ----------
  document.querySelectorAll('[data-range]').forEach(btn => {
    btn.addEventListener('click', () => {
      // Exemplo de chamada: ajuste para seu endpoint real
      // fetch('<?= route_to('admin.dashboard.data') ?>?range=' + btn.dataset.range)
      //   .then(r=>r.json()).then(updateDashboardFromApi);
    });
  });

})();
</script>

<?= $this->endSection() ?>
