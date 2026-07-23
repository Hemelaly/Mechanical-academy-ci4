<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Finanças<?= $this->endSection() ?>

<?= $this->section('financial') ?>

<?php
$fmt = static function ($v): string {
    return number_format((float) $v, 2, ',', '.') . ' MZN';
};
$exportBase = site_url('admin/dashboard/financas/export');
?>

<div class="min-w-0 space-y-5">
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h1 class="text-xl font-semibold tracking-tight text-slate-900 dark:text-white sm:text-2xl">Finanças</h1>
    <form id="finance-export-form" class="flex flex-wrap items-center gap-2" method="get" action="<?= esc($exportBase) ?>">
      <select id="export-period" name="period" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-[#0c1017] dark:text-white">
        <option value="daily">Diário</option>
        <option value="monthly" selected>Mensal</option>
        <option value="annual">Anual</option>
        <option value="custom">Intervalo</option>
      </select>
      <div id="export-day-wrap" class="hidden">
        <input id="export-day" type="date" value="<?= date('Y-m-d') ?>" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-[#0c1017] dark:text-white">
      </div>
      <div id="export-month-wrap">
        <input id="export-month" type="month" name="month" value="<?= date('Y-m') ?>" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-[#0c1017] dark:text-white">
      </div>
      <div id="export-year-wrap" class="hidden">
        <select id="export-year" name="year" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-[#0c1017] dark:text-white">
          <?php $yNow = (int) date('Y'); for ($y = $yNow; $y >= $yNow - 5; $y--): ?>
            <option value="<?= $y ?>"><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div id="export-from-wrap" class="hidden">
        <input id="export-from" type="date" value="<?= date('Y-m-01') ?>" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-[#0c1017] dark:text-white">
      </div>
      <div id="export-to-wrap" class="hidden">
        <input id="export-to" type="date" name="date_to" value="<?= date('Y-m-d') ?>" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-[#0c1017] dark:text-white">
      </div>
      <select id="export-status" name="status" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-[#0c1017] dark:text-white">
        <option value="Aprovado" selected>Aprovados</option>
        <option value="Pendente">Pendentes</option>
        <option value="Rejeitado">Rejeitados</option>
        <option value="">Todos</option>
      </select>
      <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-500">
        <i class="bi bi-download"></i>
        <span>CSV</span>
      </button>
    </form>
  </div>

  <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
    <div class="dash-card !p-4">
      <p class="text-xs text-slate-500 dark:text-white/45">Receita total</p>
      <p class="mt-1 text-xl font-semibold text-slate-900 dark:text-white"><?= esc($fmt($totalRevenue ?? 0)) ?></p>
      <p class="mt-0.5 text-xs text-slate-500 dark:text-white/40"><?= esc($fmt($monthRevenue ?? 0)) ?> este mês</p>
    </div>
    <div class="dash-card !p-4">
      <p class="text-xs text-slate-500 dark:text-white/45">Pendentes</p>
      <p class="mt-1 text-xl font-semibold text-amber-600 dark:text-amber-400"><?= esc($fmt($nextPayment ?? 0)) ?></p>
    </div>
    <div class="dash-card !p-4">
      <p class="text-xs text-slate-500 dark:text-white/45">Média / mês</p>
      <p class="mt-1 text-xl font-semibold text-slate-900 dark:text-white"><?= esc($fmt($averageMonth ?? 0)) ?></p>
      <p class="mt-0.5 text-xs text-slate-500 dark:text-white/40"><?= esc($fmt($yearTotal ?? 0)) ?> no ano</p>
    </div>
  </div>

  <div class="finance-charts-grid grid grid-cols-1 gap-4 xl:grid-cols-3">
    <div class="dash-card finance-chart-card xl:col-span-2">
      <div class="mb-3 flex items-center justify-between gap-3">
        <h2 class="min-w-0 truncate text-sm font-semibold text-slate-900 dark:text-white">Receita mensal</h2>
        <select id="finance-year-select" class="w-auto shrink-0 rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-sm dark:border-white/10 dark:bg-[#0c1017] dark:text-white"></select>
      </div>

      <div class="mb-3 grid grid-cols-3 gap-2">
        <div class="rounded-md bg-slate-50 px-3 py-2 dark:bg-white/[0.03]">
          <p class="text-[11px] text-slate-500 dark:text-slate-400">Maior</p>
          <p id="finance-stat-max" class="mt-0.5 text-sm font-semibold tabular-nums text-slate-900 dark:text-white"><?= esc($fmt($chartMax ?? 0)) ?></p>
        </div>
        <div class="rounded-md bg-slate-50 px-3 py-2 dark:bg-white/[0.03]">
          <p class="text-[11px] text-slate-500 dark:text-slate-400">Média</p>
          <p id="finance-stat-avg" class="mt-0.5 text-sm font-semibold tabular-nums text-slate-900 dark:text-white"><?= esc($fmt($chartAvg ?? 0)) ?></p>
        </div>
        <div class="rounded-md bg-slate-50 px-3 py-2 dark:bg-white/[0.03]">
          <p class="text-[11px] text-slate-500 dark:text-slate-400">Cresc.</p>
          <p id="finance-stat-growth" class="mt-0.5 text-sm font-semibold tabular-nums <?= ((float) ($chartGrowth ?? 0)) >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>"><?= esc($fmt($chartGrowth ?? 0)) ?></p>
        </div>
      </div>

      <div id="admin-finance-chart-wrap" class="finance-chart-wrap">
        <div id="admin-finance-chart"></div>
      </div>
    </div>

    <div class="space-y-4">
      <div class="dash-card">
        <h3 class="mb-1 text-sm font-semibold text-slate-900 dark:text-white">Instrutores</h3>
        <ul>
          <?php if (! empty($topInstructors)): ?>
            <?php foreach ($topInstructors as $instructor): ?>
              <li class="dash-rank-item">
                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-blue-500/10 text-blue-600 dark:text-blue-300"><i class="bi bi-person text-xs"></i></span>
                <div class="dash-rank-meta">
                  <span class="dash-rank-title"><?= esc($instructor->instructor_name ?? 'Instrutor') ?></span>
                  <span class="dash-rank-value"><?= esc($fmt($instructor->total ?? 0)) ?></span>
                </div>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="py-4 text-sm text-slate-500 dark:text-slate-400">Sem dados.</li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="dash-card">
        <h3 class="mb-1 text-sm font-semibold text-slate-900 dark:text-white">Pagamentos</h3>
        <ul>
          <?php if (! empty($paymentMethods)): ?>
            <?php foreach ($paymentMethods as $method): ?>
              <?php
                $label = (string) ($method->method_payment_label ?? 'Nao informado');
                $icon = str_contains(strtolower($label), 'mpesa') || str_contains(strtolower($label), 'mola')
                  ? 'bi-phone'
                  : (str_contains(strtolower($label), 'paypal') ? 'bi-paypal' : (str_contains(strtolower($label), 'cart') ? 'bi-credit-card' : 'bi-cash'));
              ?>
              <li class="dash-rank-item">
                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-300"><i class="bi <?= $icon ?> text-xs"></i></span>
                <div class="dash-rank-meta">
                  <span class="dash-rank-title"><?= esc($label) ?></span>
                  <span class="text-[11px] text-slate-500 dark:text-slate-400"><?= (int) ($method->total_transactions ?? 0) ?> tx</span>
                </div>
                <span class="dash-rank-value"><?= esc($fmt($method->total_amount ?? 0)) ?></span>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="py-4 text-sm text-slate-500 dark:text-slate-400">Sem dados.</li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>

  <div class="dash-card">
    <h3 class="mb-3 text-sm font-semibold text-slate-900 dark:text-white">Top cursos</h3>
    <?php if (! empty($topCourses)): ?>
      <div class="finance-top-courses flex flex-wrap gap-3">
        <?php foreach ($topCourses as $i => $course): ?>
          <div class="finance-top-course-item flex min-w-0 flex-1 basis-[14rem] items-start gap-3 rounded-md border border-slate-200 bg-slate-50 px-3 py-3 dark:border-slate-700 dark:bg-white/[0.03]">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-slate-200 text-[11px] font-bold text-slate-700 dark:bg-white/10 dark:text-slate-200"><?= (int) $i + 1 ?></span>
            <div class="min-w-0 flex-1">
              <p class="truncate text-sm font-medium text-slate-800 dark:text-slate-100" title="<?= esc($course->title_course ?? 'Curso') ?>"><?= esc($course->title_course ?? 'Curso') ?></p>
              <p class="mt-0.5 text-xs font-semibold tabular-nums text-slate-600 dark:text-slate-300"><?= esc($fmt($course->total ?? 0)) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="py-2 text-sm text-slate-500 dark:text-slate-400">Sem dados.</p>
    <?php endif; ?>
  </div>

  <div class="dash-card !p-0 overflow-hidden">
    <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-3 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between sm:px-5">
      <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Transações</h2>
      <div class="flex flex-wrap items-center gap-2">
        <div class="relative">
          <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <input id="tx-search" type="search" placeholder="Buscar..." class="w-40 rounded-md border border-slate-300 bg-white py-1.5 pl-9 pr-3 text-sm dark:border-slate-600 dark:bg-[#0c1017] dark:text-white">
        </div>
        <select id="tx-status" class="rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-sm dark:border-slate-600 dark:bg-[#0c1017] dark:text-white">
          <option value="">Todos</option>
          <option value="Aprovado">Aprovados</option>
          <option value="Pendente">Pendentes</option>
          <option value="Rejeitado">Rejeitados</option>
        </select>
        <select id="tx-per-page" class="rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-sm dark:border-slate-600 dark:bg-[#0c1017] dark:text-white">
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
        </select>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="finance-tx-table min-w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-[#0c1017] dark:text-slate-400">
          <tr>
            <th class="px-4 py-3 font-medium">Data</th>
            <th class="px-4 py-3 font-medium">Comprador</th>
            <th class="px-4 py-3 font-medium">Curso</th>
            <th class="px-4 py-3 font-medium">Instrutor</th>
            <th class="px-4 py-3 font-medium">Método</th>
            <th class="px-4 py-3 font-medium">Estado</th>
            <th class="px-4 py-3 font-medium text-right">Valor</th>
          </tr>
        </thead>
        <tbody id="tx-table-body" class="finance-tx-body">
          <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">A carregar...</td></tr>
        </tbody>
      </table>
    </div>

    <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400 sm:flex-row sm:items-center sm:justify-between sm:px-5">
      <div id="tx-summary">—</div>
      <div id="tx-pagination" class="flex flex-wrap gap-2"></div>
    </div>
  </div>
</div>

<style>
  .finance-charts-grid {
    align-items: start;
  }
  .finance-chart-card {
    align-self: start;
    height: auto !important;
  }
  .finance-chart-wrap {
    position: relative;
    width: 100%;
    height: 240px;
    max-height: 240px;
    overflow: hidden;
  }
  .finance-chart-wrap .apexcharts-canvas,
  .finance-chart-wrap svg {
    max-height: 240px !important;
  }
  .finance-tx-table tbody tr + tr td {
    border-top: 1px solid #e2e8f0;
  }
  html.dark .finance-tx-table tbody tr + tr td {
    border-top-color: rgba(255, 255, 255, 0.08) !important;
  }
  html.dark .finance-tx-table thead th {
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }
  html:not(.dark) .finance-tx-table thead th {
    border-bottom: 1px solid #e2e8f0;
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function () {
  const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

  const formatMoney = (value) => `${Number(value || 0).toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })} MZN`;

  const formatDate = (value) => {
    if (!value) return '—';
    const d = new Date(String(value).replace(' ', 'T'));
    if (Number.isNaN(d.getTime())) return escapeHtml(value);
    return d.toLocaleString('pt-PT', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
  };

  // Export form
  const period = document.getElementById('export-period');
  const dayWrap = document.getElementById('export-day-wrap');
  const monthWrap = document.getElementById('export-month-wrap');
  const yearWrap = document.getElementById('export-year-wrap');
  const fromWrap = document.getElementById('export-from-wrap');
  const toWrap = document.getElementById('export-to-wrap');
  const dayInput = document.getElementById('export-day');
  const fromInput = document.getElementById('export-from');
  const form = document.getElementById('finance-export-form');

  const syncExportFields = () => {
    const value = period.value;
    dayWrap.classList.toggle('hidden', value !== 'daily');
    monthWrap.classList.toggle('hidden', value !== 'monthly');
    yearWrap.classList.toggle('hidden', value !== 'annual');
    fromWrap.classList.toggle('hidden', value !== 'custom');
    toWrap.classList.toggle('hidden', value !== 'custom');
    dayInput.name = value === 'daily' ? 'date_from' : '';
    fromInput.name = value === 'custom' ? 'date_from' : '';
  };
  period.addEventListener('change', syncExportFields);
  syncExportFields();
  form.addEventListener('submit', () => {
    if (period.value === 'custom') fromInput.name = 'date_from';
  });

  // Chart
  const yearSelect = document.getElementById('finance-year-select');
  const chartEl = document.getElementById('admin-finance-chart');
  const statMax = document.getElementById('finance-stat-max');
  const statAvg = document.getElementById('finance-stat-avg');
  const statGrowth = document.getElementById('finance-stat-growth');
  const currentYear = new Date().getFullYear();
  let chart = null;

  const isDarkMode = () => document.documentElement.classList.contains('dark');

  for (let i = 0; i < 5; i++) {
    const year = currentYear - i;
    const option = document.createElement('option');
    option.value = year;
    option.textContent = year;
    if (i === 0) option.selected = true;
    yearSelect.appendChild(option);
  }

  const getGrowth = (data) => {
    const valid = (data || []).map(Number);
    const idx = valid.reduce((last, v, i) => (v > 0 ? i : last), -1);
    if (idx <= 0) return 0;
    let prev = idx - 1;
    while (prev >= 0 && valid[prev] === 0) prev -= 1;
    if (prev < 0) return valid[idx];
    return valid[idx] - valid[prev];
  };

  const renderChart = async (year) => {
    const url = new URL(<?= json_encode(site_url('admin/dashboard/financas/data')) ?>, window.location.origin);
    url.searchParams.set('year', year);
    const response = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!response.ok) throw new Error('Falha ao carregar gráfico');
    const payload = await response.json();
    const data = (payload.data || []).map(Number);
    const growth = getGrowth(data);

    if (statMax) statMax.textContent = formatMoney(payload.max || 0);
    if (statAvg) statAvg.textContent = formatMoney(payload.avg || 0);
    if (statGrowth) {
      statGrowth.textContent = formatMoney(growth);
      statGrowth.classList.toggle('text-green-600', growth >= 0);
      statGrowth.classList.toggle('text-red-600', growth < 0);
    }

    const dark = isDarkMode();
    const chartHeight = 240;
    const options = {
      chart: {
        type: 'area',
        height: chartHeight,
        width: '100%',
        toolbar: { show: false },
        zoom: { enabled: false },
        background: 'transparent',
        parentHeightOffset: 0,
        animations: { enabled: false },
        redrawOnParentResize: true,
        redrawOnWindowResize: true,
      },
      series: [{ name: 'Receita', data }],
      xaxis: {
        categories: payload.labels || ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        labels: { style: { colors: dark ? '#94a3b8' : '#64748b', fontSize: '11px' } },
        axisBorder: { show: false },
        axisTicks: { show: false },
      },
      yaxis: {
        min: 0,
        labels: {
          style: { colors: dark ? '#94a3b8' : '#64748b', fontSize: '11px' },
          formatter: (v) => Number(v || 0).toLocaleString('pt-BR', { maximumFractionDigits: 0 }),
        },
      },
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth', width: 3 },
      markers: { size: 3, hover: { size: 5 } },
      fill: {
        type: 'gradient',
        gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.04, stops: [0, 90, 100] },
      },
      colors: ['#2563eb'],
      grid: {
        borderColor: dark ? 'rgba(148,163,184,0.12)' : 'rgba(148,163,184,0.25)',
        strokeDashArray: 4,
        padding: { top: 8, right: 8, bottom: 0, left: 8 },
      },
      tooltip: { theme: dark ? 'dark' : 'light', y: { formatter: (v) => formatMoney(v) } },
      legend: { show: false },
    };

    if (chart) {
      chart.destroy();
      chart = null;
    }
    chartEl.innerHTML = '';
    chart = new ApexCharts(chartEl, options);
    await chart.render();

    const wrap = document.getElementById('admin-finance-chart-wrap');
    if (wrap) {
      wrap.style.height = chartHeight + 'px';
      wrap.style.maxHeight = chartHeight + 'px';
    }
  };

  yearSelect.addEventListener('change', () => renderChart(yearSelect.value).catch(console.error));
  renderChart(yearSelect.value).catch(console.error);

  // Re-render only when dark class actually changes
  let lastDark = isDarkMode();
  const themeObserver = new MutationObserver(() => {
    const nextDark = isDarkMode();
    if (nextDark === lastDark) return;
    lastDark = nextDark;
    if (yearSelect?.value) {
      renderChart(yearSelect.value).catch(console.error);
    }
  });
  themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

  // Transactions table with pagination
  const txBody = document.getElementById('tx-table-body');
  const txSummary = document.getElementById('tx-summary');
  const txPagination = document.getElementById('tx-pagination');
  const txSearch = document.getElementById('tx-search');
  const txStatus = document.getElementById('tx-status');
  const txPerPage = document.getElementById('tx-per-page');
  let txPage = 1;
  let searchTimer = null;

  const statusBadge = (status) => {
    const map = {
      Aprovado: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
      Rejeitado: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
      Pendente: 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
    };
    const cls = map[status] || 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200';
    const icon = status === 'Aprovado' ? 'bi-check-circle' : (status === 'Rejeitado' ? 'bi-x-circle' : 'bi-clock');
    return `<span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium ${cls}"><i class="bi ${icon}"></i> ${escapeHtml(status || '—')}</span>`;
  };

  const methodIcon = (label) => {
    const l = String(label || '').toLowerCase();
    if (l.includes('mpesa') || l.includes('mola')) return 'bi-phone';
    if (l.includes('paypal')) return 'bi-paypal';
    if (l.includes('cart')) return 'bi-credit-card';
    return 'bi-cash-stack';
  };

  const loadTransactions = async () => {
    txBody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-slate-500"><i class="bi bi-arrow-repeat animate-spin mr-2"></i>A carregar...</td></tr>';
    const url = new URL(<?= json_encode(site_url('admin/dashboard/financas/transactions')) ?>, window.location.origin);
    url.searchParams.set('page', String(txPage));
    url.searchParams.set('per_page', txPerPage.value);
    if (txStatus.value) url.searchParams.set('status', txStatus.value);
    if (txSearch.value.trim()) url.searchParams.set('q', txSearch.value.trim());

    try {
      const response = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const payload = await response.json();
      if (!response.ok) throw new Error(payload.message || 'Erro');

      const items = payload.items || [];
      const pagination = payload.pagination || { page: 1, per_page: 10, total: 0, total_pages: 1 };

      if (!items.length) {
        txBody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-slate-500"><i class="bi bi-inbox mr-2"></i>Nenhuma transação encontrada.</td></tr>';
      } else {
        txBody.innerHTML = items.map((item) => `
          <tr class="align-top hover:bg-slate-50 dark:hover:bg-white/[0.03]">
            <td class="whitespace-nowrap px-4 py-3 text-slate-600 dark:text-slate-300">
              <span class="inline-flex items-center gap-1"><i class="bi bi-calendar3 text-slate-400"></i>${formatDate(item.created_at)}</span>
            </td>
            <td class="px-4 py-3">
              <div class="flex items-start gap-2">
                <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-slate-100 text-slate-500 dark:bg-slate-700"><i class="bi bi-person"></i></span>
                <div class="min-w-0">
                  <div class="max-w-[14rem] break-words font-medium text-slate-900 dark:text-white">${escapeHtml(item.buyer_label)}</div>
                  ${item.buyer_email ? `<div class="max-w-[14rem] break-all text-xs text-slate-500">${escapeHtml(item.buyer_email)}</div>` : ''}
                </div>
              </div>
            </td>
            <td class="px-4 py-3">
              <div class="flex items-start gap-2">
                <span class="mt-0.5 text-blue-500"><i class="bi bi-book"></i></span>
                <div class="max-w-[16rem] break-words text-slate-700 dark:text-slate-200">${escapeHtml(item.title_course)}</div>
              </div>
            </td>
            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
              <span class="inline-flex items-center gap-1"><i class="bi bi-person-badge text-slate-400"></i>${escapeHtml(item.instructor_name)}</span>
            </td>
            <td class="whitespace-nowrap px-4 py-3 text-slate-600 dark:text-slate-300">
              <span class="inline-flex items-center gap-1"><i class="bi ${methodIcon(item.method_payment_label)}"></i>${escapeHtml(item.method_payment_label)}</span>
            </td>
            <td class="whitespace-nowrap px-4 py-3">${statusBadge(item.status_payment)}</td>
            <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">${formatMoney(item.amount_payment)}</td>
          </tr>
        `).join('');
      }

      const from = pagination.total === 0 ? 0 : ((pagination.page - 1) * pagination.per_page) + 1;
      const to = Math.min(pagination.page * pagination.per_page, pagination.total);
      txSummary.textContent = `A mostrar ${from}–${to} de ${pagination.total}`;

      const pages = [];
      const totalPages = pagination.total_pages || 1;
      const makeBtn = (label, page, disabled = false, active = false) => {
        const cls = active
          ? 'bg-blue-600 text-white'
          : 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600';
        return `<button type="button" data-page="${page}" class="rounded-md px-3 py-1.5 text-xs font-medium ${cls}" ${disabled ? 'disabled' : ''}>${label}</button>`;
      };
      pages.push(makeBtn('Anterior', Math.max(1, pagination.page - 1), pagination.page <= 1));
      const start = Math.max(1, pagination.page - 2);
      const end = Math.min(totalPages, start + 4);
      for (let p = start; p <= end; p++) pages.push(makeBtn(String(p), p, false, p === pagination.page));
      pages.push(makeBtn('Seguinte', Math.min(totalPages, pagination.page + 1), pagination.page >= totalPages));
      txPagination.innerHTML = pages.join('');
      txPagination.querySelectorAll('button[data-page]').forEach((btn) => {
        btn.addEventListener('click', () => {
          txPage = Number(btn.getAttribute('data-page') || 1);
          loadTransactions();
        });
      });
    } catch (e) {
      console.error(e);
      txBody.innerHTML = `<tr><td colspan="7" class="px-4 py-8 text-center text-red-500"><i class="bi bi-exclamation-triangle mr-2"></i>${escapeHtml(e.message || 'Erro ao carregar')}</td></tr>`;
      txSummary.textContent = 'Erro';
      txPagination.innerHTML = '';
    }
  };

  txStatus.addEventListener('change', () => { txPage = 1; loadTransactions(); });
  txPerPage.addEventListener('change', () => { txPage = 1; loadTransactions(); });
  txSearch.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => { txPage = 1; loadTransactions(); }, 300);
  });

  loadTransactions();
})();
</script>

<?= $this->endSection() ?>
