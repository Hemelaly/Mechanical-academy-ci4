<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Analytics<?= $this->endSection() ?>

<?= $this->section('analytics') ?>

<?php
$a = $analytics ?? [];
$kpis = $a['kpis'] ?? [];
$days = (int) ($days ?? ($a['days'] ?? 30));
$chart = $a['chart'] ?? ['labels' => [], 'pageviews' => [], 'clicks' => []];
$byPersona = $a['by_persona'] ?? [];
$byDevice = $a['by_device'] ?? [];

$personaLabels = [
    'guest' => 'Visitante',
    'student' => 'Aluno',
    'instructor' => 'Instrutor',
    'admin' => 'Admin',
];
$deviceLabels = [
    'desktop' => 'Desktop',
    'mobile' => 'Mobile',
    'tablet' => 'Tablet',
    'unknown' => 'Outro',
];
$n = static fn ($v) => number_format((float) $v, 0, ',', '.');
$tableEndpoint = site_url('admin/dashboard/analytics/table');
?>

<div class="min-w-0 space-y-5" data-analytics-ignore>
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-xl font-semibold tracking-tight text-slate-900 dark:text-white sm:text-2xl">Analytics</h1>
      <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">Acessos, cliques, rotas e comportamento por persona.</p>
    </div>
    <form method="get" action="<?= esc(site_url('admin/dashboard/analytics')) ?>" class="flex flex-wrap items-center gap-2">
      <select name="days" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-[#0c1017] dark:text-white" onchange="this.form.submit()">
        <?php foreach ([7, 14, 30, 60, 90] as $opt): ?>
          <option value="<?= $opt ?>" <?= $days === $opt ? 'selected' : '' ?>><?= $opt ?> dias</option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <?php
  $kpiCards = [
      [
          'label' => 'Pageviews',
          'value' => $n($kpis['pageviews'] ?? 0),
          'icon'  => 'bi-eye',
          'tone'  => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'darkBg' => 'dark:bg-blue-900/40', 'darkText' => 'dark:text-blue-300'],
      ],
      [
          'label' => 'Cliques',
          'value' => $n($kpis['clicks'] ?? 0),
          'icon'  => 'bi-cursor',
          'tone'  => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'darkBg' => 'dark:bg-emerald-900/40', 'darkText' => 'dark:text-emerald-300'],
      ],
      [
          'label' => 'Visitantes',
          'value' => $n($kpis['visitors'] ?? 0),
          'icon'  => 'bi-people',
          'tone'  => ['bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'darkBg' => 'dark:bg-violet-900/40', 'darkText' => 'dark:text-violet-300'],
      ],
      [
          'label' => 'Sessões',
          'value' => $n($kpis['sessions'] ?? 0),
          'icon'  => 'bi-activity',
          'tone'  => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'darkBg' => 'dark:bg-amber-900/40', 'darkText' => 'dark:text-amber-300'],
      ],
      [
          'label' => 'Cliques / sessão',
          'value' => number_format((float) ($kpis['avg_clicks_per_session'] ?? 0), 2, ',', '.'),
          'icon'  => 'bi-speedometer2',
          'tone'  => ['bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'darkBg' => 'dark:bg-sky-900/40', 'darkText' => 'dark:text-sky-300'],
      ],
  ];
  ?>
  <div class="analytics-kpi-row">
    <?php foreach ($kpiCards as $card): ?>
      <div class="dash-card analytics-kpi-card flex min-w-0 items-center gap-2 sm:gap-3">
        <span class="analytics-kpi-icon flex shrink-0 items-center justify-center rounded-md <?= esc($card['tone']['bg']) ?> <?= esc($card['tone']['text']) ?> <?= esc($card['tone']['darkBg']) ?> <?= esc($card['tone']['darkText']) ?>">
          <i class="bi <?= esc($card['icon']) ?>"></i>
        </span>
        <div class="min-w-0">
          <p class="analytics-kpi-label truncate text-slate-500 dark:text-slate-400"><?= esc($card['label']) ?></p>
          <p class="analytics-kpi-value truncate font-semibold tabular-nums tracking-tight text-slate-900 dark:text-white"><?= esc($card['value']) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
    <div class="dash-card xl:col-span-2">
      <div class="mb-3 flex items-center justify-between gap-3">
        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Tráfego diário</h2>
        <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
          <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-blue-500"></span> Pageviews</span>
          <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Cliques</span>
        </div>
      </div>
      <div class="analytics-chart-wrap">
        <div id="admin-analytics-chart"></div>
      </div>
    </div>

    <div class="space-y-4">
      <div class="dash-card">
        <h3 class="mb-3 text-sm font-semibold text-slate-900 dark:text-white">Por persona</h3>
        <div id="admin-analytics-persona-chart"></div>
        <?php if (empty($byPersona)): ?>
          <p class="py-4 text-sm text-slate-500 dark:text-slate-400">Sem dados ainda.</p>
        <?php else: ?>
          <ul class="mt-2 space-y-2">
            <?php foreach ($byPersona as $row): ?>
              <li class="flex items-center justify-between gap-2 text-sm">
                <span class="text-slate-600 dark:text-slate-300"><?= esc($personaLabels[$row['persona'] ?? ''] ?? ($row['persona'] ?? '—')) ?></span>
                <span class="font-semibold tabular-nums text-slate-900 dark:text-white"><?= esc($n($row['total'] ?? 0)) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="dash-card">
        <h3 class="mb-3 text-sm font-semibold text-slate-900 dark:text-white">Dispositivos</h3>
        <?php if (empty($byDevice)): ?>
          <p class="py-2 text-sm text-slate-500 dark:text-slate-400">Sem dados ainda.</p>
        <?php else: ?>
          <ul class="space-y-2">
            <?php foreach ($byDevice as $row): ?>
              <li class="flex items-center justify-between gap-2 text-sm">
                <span class="text-slate-600 dark:text-slate-300"><?= esc($deviceLabels[$row['device'] ?? ''] ?? ($row['device'] ?? '—')) ?></span>
                <span class="font-semibold tabular-nums text-slate-900 dark:text-white"><?= esc($n($row['total'] ?? 0)) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div class="dash-card !p-0 overflow-hidden">
      <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-4 py-3 dark:border-white/10">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Rotas mais acedidas</h3>
        <select id="routes-per-page" class="rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-sm dark:border-white/10 dark:bg-[#0c1017] dark:text-white">
          <option value="5" selected>5</option>
          <option value="10">10</option>
          <option value="20">20</option>
        </select>
      </div>
      <div class="overflow-x-auto px-4">
        <table class="analytics-table w-full text-left text-sm">
          <thead>
            <tr>
              <th>Rota</th>
              <th class="text-right">Views</th>
            </tr>
          </thead>
          <tbody id="routes-body">
            <tr><td colspan="2" class="py-4 text-center text-slate-500 dark:text-slate-400">A carregar…</td></tr>
          </tbody>
        </table>
      </div>
      <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 text-sm text-slate-500 dark:border-white/10 dark:text-slate-400 sm:flex-row sm:items-center sm:justify-between">
        <div id="routes-summary">—</div>
        <div id="routes-pagination" class="flex flex-wrap gap-2"></div>
      </div>
    </div>

    <div class="dash-card !p-0 overflow-hidden">
      <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-4 py-3 dark:border-white/10">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Elementos mais clicados</h3>
        <select id="clicks-per-page" class="rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-sm dark:border-white/10 dark:bg-[#0c1017] dark:text-white">
          <option value="5" selected>5</option>
          <option value="10">10</option>
          <option value="20">20</option>
        </select>
      </div>
      <div class="overflow-x-auto px-4">
        <table class="analytics-table w-full text-left text-sm">
          <thead>
            <tr>
              <th>Elemento</th>
              <th class="text-right">Cliques</th>
            </tr>
          </thead>
          <tbody id="clicks-body">
            <tr><td colspan="2" class="py-4 text-center text-slate-500 dark:text-slate-400">A carregar…</td></tr>
          </tbody>
        </table>
      </div>
      <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 text-sm text-slate-500 dark:border-white/10 dark:text-slate-400 sm:flex-row sm:items-center sm:justify-between">
        <div id="clicks-summary">—</div>
        <div id="clicks-pagination" class="flex flex-wrap gap-2"></div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div class="dash-card !p-0 overflow-hidden">
      <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-4 py-3 dark:border-white/10">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Páginas de entrada</h3>
        <select id="entries-per-page" class="rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-sm dark:border-white/10 dark:bg-[#0c1017] dark:text-white">
          <option value="5" selected>5</option>
          <option value="10">10</option>
          <option value="20">20</option>
        </select>
      </div>
      <ul id="entries-body" class="px-4 py-2"></ul>
      <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 text-sm text-slate-500 dark:border-white/10 dark:text-slate-400 sm:flex-row sm:items-center sm:justify-between">
        <div id="entries-summary">—</div>
        <div id="entries-pagination" class="flex flex-wrap gap-2"></div>
      </div>
    </div>

    <div class="dash-card !p-0 overflow-hidden">
      <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-4 py-3 dark:border-white/10">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Actividade recente</h3>
        <select id="recent-per-page" class="rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-sm dark:border-white/10 dark:bg-[#0c1017] dark:text-white">
          <option value="5" selected>5</option>
          <option value="10">10</option>
          <option value="20">20</option>
        </select>
      </div>
      <ul id="recent-body" class="space-y-2 px-4 py-3"></ul>
      <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 text-sm text-slate-500 dark:border-white/10 dark:text-slate-400 sm:flex-row sm:items-center sm:justify-between">
        <div id="recent-summary">—</div>
        <div id="recent-pagination" class="flex flex-wrap gap-2"></div>
      </div>
    </div>
  </div>
</div>

<style>
  .analytics-chart-wrap { min-height: 280px; }
  .analytics-chart-wrap .apexcharts-canvas { max-width: 100% !important; }
  .analytics-table th {
    padding: 0.5rem 0.25rem;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: rgb(100 116 139);
    border-bottom: 1px solid rgba(148, 163, 184, 0.25);
  }
  .dark .analytics-table th { color: rgb(148 163 184); border-bottom-color: rgba(255,255,255,0.08); }
  .analytics-table td {
    padding: 0.65rem 0.25rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.15);
    vertical-align: top;
  }
  .dark .analytics-table td { border-bottom-color: rgba(255,255,255,0.06); }
  .analytics-table tbody tr:last-child td { border-bottom: 0; }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function () {
  const days = <?= (int) $days ?>;
  const tableEndpoint = <?= json_encode($tableEndpoint) ?>;
  const personaMap = <?= json_encode($personaLabels, JSON_UNESCAPED_UNICODE) ?>;
  const chartData = <?= json_encode($chart, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  const personaRows = <?= json_encode($byPersona, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  const isDark = document.documentElement.classList.contains('dark');

  const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

  const formatNum = (value) => Number(value || 0).toLocaleString('pt-PT');

  const formatWhen = (value) => {
    if (!value) return '—';
    const d = new Date(String(value).replace(' ', 'T'));
    if (Number.isNaN(d.getTime())) return escapeHtml(value);
    const dd = String(d.getDate()).padStart(2, '0');
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const hh = String(d.getHours()).padStart(2, '0');
    const mi = String(d.getMinutes()).padStart(2, '0');
    return `${dd}/${mm} ${hh}:${mi}`;
  };

  const renderPagination = (paginationData, container, onPage) => {
    const totalPages = paginationData.total_pages || 1;
    const currentPage = paginationData.page || 1;
    const pages = [];
    const maxButtons = 5;
    let start = Math.max(1, currentPage - 2);
    let end = Math.min(totalPages, start + maxButtons - 1);
    if (end - start < maxButtons - 1) start = Math.max(1, end - maxButtons + 1);
    for (let i = start; i <= end; i += 1) pages.push(i);

    const button = (label, page, disabled, active) => {
      const base = 'rounded-lg border px-3 py-1.5 text-sm font-medium transition';
      const activeClass = active
        ? 'bg-blue-600 text-white border-blue-600'
        : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-200 dark:border-slate-600';
      const disabledClass = disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
      return `<button type="button" class="${base} ${activeClass} ${disabledClass}" data-page="${page}" ${disabled ? 'disabled' : ''}>${label}</button>`;
    };

    container.innerHTML = [
      button('Anterior', currentPage - 1, currentPage <= 1, false),
      ...pages.map((page) => button(String(page), page, false, page === currentPage)),
      button('Seguinte', currentPage + 1, currentPage >= totalPages, false),
    ].join('');

    container.onclick = (event) => {
      const btn = event.target.closest('button[data-page]');
      if (!btn || btn.disabled) return;
      const nextPage = Number(btn.dataset.page);
      if (!Number.isFinite(nextPage) || nextPage < 1) return;
      onPage(nextPage);
    };
  };

  const renderSummary = (paginationData, el, label) => {
    const total = paginationData.total || 0;
    const page = paginationData.page || 1;
    const perPage = paginationData.per_page || 10;
    const start = total === 0 ? 0 : (page - 1) * perPage + 1;
    const end = Math.min(total, page * perPage);
    el.textContent = `A mostrar ${start}–${end} de ${total} ${label}`;
  };

  const createTableLoader = ({ section, bodyId, summaryId, paginationId, perPageId, emptyHtml, renderItems, label }) => {
    const body = document.getElementById(bodyId);
    const summary = document.getElementById(summaryId);
    const pagination = document.getElementById(paginationId);
    const perPageEl = document.getElementById(perPageId);
    const state = { page: 1, per_page: Number(perPageEl?.value || 5) };

    const load = async () => {
      if (!body) return;
      body.innerHTML = emptyHtml.loading;
      const url = new URL(tableEndpoint, window.location.origin);
      url.searchParams.set('section', section);
      url.searchParams.set('days', String(days));
      url.searchParams.set('page', String(state.page));
      url.searchParams.set('per_page', String(state.per_page));

      try {
        const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const payload = await res.json();
        if (!res.ok) throw new Error(payload.message || 'Erro');

        const items = payload.items || [];
        const paginationData = payload.pagination || { page: 1, per_page: state.per_page, total: 0, total_pages: 1 };
        state.page = paginationData.page || state.page;

        body.innerHTML = items.length ? renderItems(items) : emptyHtml.empty;
        renderSummary(paginationData, summary, label);
        renderPagination(paginationData, pagination, (next) => {
          state.page = next;
          load();
        });
      } catch (err) {
        body.innerHTML = emptyHtml.error;
        summary.textContent = 'Falha ao carregar';
        pagination.innerHTML = '';
      }
    };

    perPageEl?.addEventListener('change', () => {
      state.per_page = Number(perPageEl.value || 10);
      state.page = 1;
      load();
    });

    load();
    return load;
  };

  createTableLoader({
    section: 'routes',
    bodyId: 'routes-body',
    summaryId: 'routes-summary',
    paginationId: 'routes-pagination',
    perPageId: 'routes-per-page',
    label: 'rotas',
    emptyHtml: {
      loading: '<tr><td colspan="2" class="py-4 text-center text-slate-500 dark:text-slate-400">A carregar…</td></tr>',
      empty: '<tr><td colspan="2" class="py-4 text-center text-slate-500 dark:text-slate-400">Sem pageviews no período.</td></tr>',
      error: '<tr><td colspan="2" class="py-4 text-center text-rose-500">Não foi possível carregar.</td></tr>',
    },
    renderItems: (items) => items.map((row) => `
      <tr>
        <td>
          <div class="font-medium text-slate-900 dark:text-white">${escapeHtml(row.label || row.path || '—')}</div>
          <div class="text-xs text-slate-500 dark:text-slate-400">${escapeHtml(row.path || '')}</div>
        </td>
        <td class="text-right tabular-nums font-semibold text-slate-900 dark:text-white">${formatNum(row.total)}</td>
      </tr>
    `).join(''),
  });

  createTableLoader({
    section: 'clicks',
    bodyId: 'clicks-body',
    summaryId: 'clicks-summary',
    paginationId: 'clicks-pagination',
    perPageId: 'clicks-per-page',
    label: 'elementos',
    emptyHtml: {
      loading: '<tr><td colspan="2" class="py-4 text-center text-slate-500 dark:text-slate-400">A carregar…</td></tr>',
      empty: '<tr><td colspan="2" class="py-4 text-center text-slate-500 dark:text-slate-400">Sem cliques no período.</td></tr>',
      error: '<tr><td colspan="2" class="py-4 text-center text-rose-500">Não foi possível carregar.</td></tr>',
    },
    renderItems: (items) => items.map((row) => `
      <tr>
        <td>
          <div class="font-medium text-slate-900 dark:text-white">${escapeHtml(row.label || '—')}</div>
          <div class="text-xs text-slate-500 dark:text-slate-400">${escapeHtml(row.path || '')}</div>
        </td>
        <td class="text-right tabular-nums font-semibold text-slate-900 dark:text-white">${formatNum(row.total)}</td>
      </tr>
    `).join(''),
  });

  createTableLoader({
    section: 'entries',
    bodyId: 'entries-body',
    summaryId: 'entries-summary',
    paginationId: 'entries-pagination',
    perPageId: 'entries-per-page',
    label: 'páginas',
    emptyHtml: {
      loading: '<li class="py-3 text-sm text-slate-500 dark:text-slate-400">A carregar…</li>',
      empty: '<li class="py-3 text-sm text-slate-500 dark:text-slate-400">Sem dados.</li>',
      error: '<li class="py-3 text-sm text-rose-500">Não foi possível carregar.</li>',
    },
    renderItems: (items) => items.map((row) => `
      <li class="dash-rank-item">
        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-blue-500/10 text-blue-600 dark:text-blue-300"><i class="bi bi-door-open text-xs"></i></span>
        <div class="dash-rank-meta">
          <span class="dash-rank-title">${escapeHtml(row.label || row.path || '—')}</span>
          <span class="dash-rank-value">${formatNum(row.total)}</span>
        </div>
      </li>
    `).join(''),
  });

  createTableLoader({
    section: 'recent',
    bodyId: 'recent-body',
    summaryId: 'recent-summary',
    paginationId: 'recent-pagination',
    perPageId: 'recent-per-page',
    label: 'eventos',
    emptyHtml: {
      loading: '<li class="py-3 text-sm text-slate-500 dark:text-slate-400">A carregar…</li>',
      empty: '<li class="py-3 text-sm text-slate-500 dark:text-slate-400">Sem eventos ainda. Navegue no site para gerar dados.</li>',
      error: '<li class="py-3 text-sm text-rose-500">Não foi possível carregar.</li>',
    },
    renderItems: (items) => items.map((ev) => {
      const type = String(ev.event_type || '');
      const icon = type === 'click' ? 'bi-cursor' : 'bi-eye';
      const persona = personaMap[ev.persona] || ev.persona || 'guest';
      const title = type === 'click'
        ? (ev.element || 'clique')
        : (ev.route_label || ev.path || 'pageview');
      return `
        <li class="flex items-start gap-2 rounded-md bg-slate-50 px-2.5 py-2 text-sm dark:bg-white/[0.03]">
          <span class="mt-0.5 text-blue-600 dark:text-blue-300"><i class="bi ${icon}"></i></span>
          <div class="min-w-0 flex-1">
            <div class="truncate font-medium text-slate-900 dark:text-white">${escapeHtml(title)}</div>
            <div class="text-xs text-slate-500 dark:text-slate-400">
              ${escapeHtml(persona)} · ${escapeHtml(ev.path || '')} · ${formatWhen(ev.created_at)}
            </div>
          </div>
        </li>
      `;
    }).join(''),
  });

  const chartEl = document.getElementById('admin-analytics-chart');
  if (chartEl && window.ApexCharts) {
    new ApexCharts(chartEl, {
      chart: {
        type: 'area',
        height: 280,
        toolbar: { show: false },
        zoom: { enabled: false },
        background: 'transparent',
        fontFamily: 'Sora, system-ui, sans-serif'
      },
      series: [
        { name: 'Pageviews', data: chartData.pageviews || [] },
        { name: 'Cliques', data: chartData.clicks || [] }
      ],
      colors: ['#0d6efd', '#10b981'],
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth', width: 2 },
      fill: {
        type: 'gradient',
        gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] }
      },
      grid: {
        borderColor: isDark ? 'rgba(255,255,255,0.06)' : 'rgba(148,163,184,0.25)',
        strokeDashArray: 3
      },
      xaxis: {
        categories: chartData.labels || [],
        labels: { style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' } },
        axisBorder: { show: false },
        axisTicks: { show: false }
      },
      yaxis: {
        labels: {
          style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' },
          formatter: function (v) { return Math.round(v); }
        }
      },
      legend: { show: false },
      tooltip: { theme: isDark ? 'dark' : 'light' }
    }).render();
  }

  const personaEl = document.getElementById('admin-analytics-persona-chart');
  if (personaEl && window.ApexCharts && personaRows && personaRows.length) {
    new ApexCharts(personaEl, {
      chart: { type: 'donut', height: 180, background: 'transparent', fontFamily: 'Sora, system-ui, sans-serif' },
      series: personaRows.map(function (r) { return Number(r.total || 0); }),
      labels: personaRows.map(function (r) { return personaMap[r.persona] || r.persona || '—'; }),
      colors: ['#64748b', '#0d6efd', '#10b981', '#f59e0b'],
      legend: { show: false },
      dataLabels: { enabled: false },
      stroke: { width: 0 },
      plotOptions: { pie: { donut: { size: '68%' } } },
      tooltip: { theme: isDark ? 'dark' : 'light' }
    }).render();
  }
})();
</script>

<?= $this->endSection() ?>
