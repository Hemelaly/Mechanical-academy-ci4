<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Financas<?= $this->endSection() ?>

<?= $this->section('financial') ?>
<div class="min-h-screen bg-slate-50 dark:bg-slate-900">
  <div class="container mx-auto">
    <div class="mb-8">
      <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white mb-2">
        Financas
      </h1>
      <p class="text-slate-600 dark:text-slate-400 text-sm">
        Pagamentos aprovados por mes.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
      <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-4 sm:p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div class="flex-1 min-w-0">
            <p class="text-blue-100 text-sm font-medium mb-1 truncate">Receita Total</p>
            <h3 class="text-2xl sm:text-3xl font-bold mb-1 truncate">0,00 MZN</h3>
            <span class="text-blue-100 text-sm font-medium truncate">0,00 MZN este mes</span>
          </div>
          <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
            <i class="bi bi-currency-dollar text-white text-lg"></i>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-2xl p-4 sm:p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div class="flex-1 min-w-0">
            <p class="text-green-100 text-sm font-medium mb-1 truncate">Pagamentos aprovados</p>
            <h3 class="text-2xl sm:text-3xl font-bold mb-1 truncate">0,00 MZN</h3>
            <span class="text-green-100 text-sm font-medium truncate">0,00 MZN hoje</span>
          </div>
          <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
            <i class="bi bi-check2-circle text-white text-lg"></i>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-2xl p-4 sm:p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div class="flex-1 min-w-0">
            <p class="text-purple-100 text-sm font-medium mb-1 truncate">Media mensal</p>
            <h3 class="text-2xl sm:text-3xl font-bold mb-1 truncate">0,00 MZN</h3>
            <span class="text-purple-100 text-sm font-medium truncate">0,00 MZN anual</span>
          </div>
          <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
            <i class="bi bi-bar-chart text-white text-lg"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <h3 class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white">Pagamentos aprovados por mes</h3>
        <select id="admin-finance-year" class="text-xs sm:text-sm bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <?php $yearNow = (int) date('Y'); ?>
          <?php for ($y = $yearNow; $y >= $yearNow - 4; $y--): ?>
            <option value="<?= $y ?>"><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>

      <div class="h-64 sm:h-72">
        <canvas id="admin-finance-chart"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  (function () {
    const yearSelect = document.getElementById('admin-finance-year');
    const ctx = document.getElementById('admin-finance-chart').getContext('2d');
    let chart;

    const loadData = (year) => {
      const url = new URL(<?= json_encode(site_url('admin/dashboard/financas/data')) ?>, window.location.origin);
      url.searchParams.set('year', year);
      return fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json());
    };

    const renderChart = (labels, data) => {
      if (chart) {
        chart.data.labels = labels;
        chart.data.datasets[0].data = data;
        chart.update();
        return;
      }
      chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: 'Pagamentos aprovados',
            data,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.2)',
            tension: 0.35,
            fill: true,
            pointRadius: 3,
            pointBackgroundColor: '#2563eb'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: {
              ticks: {
                callback: (value) => `${Number(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MZN`
              }
            }
          }
        }
      });
    };

    const refresh = () => {
      loadData(yearSelect.value).then(payload => {
        renderChart(payload.labels || [], payload.data || []);
      });
    };

    yearSelect.addEventListener('change', refresh);
    refresh();
  })();
</script>
<?= $this->endSection() ?>
