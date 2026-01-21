<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Finanças<?= $this->endSection() ?>

<?= $this->section('financial') ?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<div class="min-h-screen bg-slate-50 dark:bg-slate-900">
  <div class="container mx-auto">

    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white mb-2">
        Financeiro
      </h1>
      <p class="text-slate-600 dark:text-slate-400 text-sm">
        Acompanhe sua receita e desempenho financeiro
      </p>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
      <!-- Receita Total -->
      <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-4 sm:p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div class="flex-1 min-w-0">
            <p class="text-blue-100 text-sm font-medium mb-1 truncate">
              Receita Total
            </p>
            <h3 class="text-2xl sm:text-3xl font-bold mb-1 truncate">
              0,00 MZN
            </h3>
            <div class="flex items-center gap-1">
              <i class="bi bi-arrow-up-short text-green-300 text-sm"></i>
              <span class="text-green-300 text-sm font-medium truncate">
                0,00 MZN este mês
              </span>
            </div>
          </div>
          <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
            <i class="bi bi-currency-dollar text-white text-lg"></i>
          </div>
        </div>
      </div>

      <!-- Próximo Pagamento -->
      <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-2xl p-4 sm:p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div class="flex-1 min-w-0">
            <p class="text-green-100 text-sm font-medium mb-1 truncate">
              Próximo Pagamento
            </p>
            <h3 class="text-2xl sm:text-3xl font-bold mb-1 truncate">
              0,00 MZN
            </h3>
            <div class="flex items-center gap-1">
              <i class="bi bi-calendar-check text-green-100 text-sm"></i>
              <span class="text-green-100 text-sm font-medium truncate">
                0,00 MZN em 5 dias
              </span>
            </div>
          </div>
          <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
            <i class="bi bi-clock text-white text-lg"></i>
          </div>
        </div>
      </div>

      <!-- Receita Média/Mês -->
      <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-2xl p-4 sm:p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div class="flex-1 min-w-0">
            <p class="text-purple-100 text-sm font-medium mb-1 truncate">
              Receita Média/Mês
            </p>
            <h3 class="text-2xl sm:text-3xl font-bold mb-1 truncate">
              0,00 MZN
            </h3>
            <div class="flex items-center gap-1">
              <i class="bi bi-graph-up text-green-300 text-sm"></i>
              <span class="text-green-300 text-sm font-medium truncate">
                0,00 MZN anual
              </span>
            </div>
          </div>
          <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
            <i class="bi bi-bar-chart text-white text-lg"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Gráficos e Transações -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Pagamentos aprovados por mes -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
          <h3 class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white">
            Pagamentos aprovados por mes
          </h3>
          <div class="flex items-center gap-2">
            <select class="text-xs sm:text-sm bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg px-2 sm:px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option>2024</option>
              <option>2023</option>
              <option>2022</option>
            </select>
          </div>
        </div>

        <div class="h-64 sm:h-72">
          <canvas id="instructor-finance-chart"></canvas>
        </div>

        <!-- Mini Stats abaixo do gr?fico -->
        <div class="grid grid-cols-3 gap-3 mt-4">
          <div class="text-center p-3 bg-slate-50 dark:bg-slate-900 rounded-lg">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Maior</p>
            <p class="text-sm font-bold text-slate-800 dark:text-white">0,00 MZN</p>
          </div>
          <div class="text-center p-3 bg-slate-50 dark:bg-slate-900 rounded-lg">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Média</p>
            <p class="text-sm font-bold text-slate-800 dark:text-white">0,00 MZN</p>
          </div>
          <div class="text-center p-3 bg-slate-50 dark:bg-slate-900 rounded-lg">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Cresc.</p>
            <p class="text-sm font-bold text-green-600">0,00 MZN</p>
          </div>
        </div>
      </div>

      <!-- Últimas Transações -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
          <h3 class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white">
            Últimas Transações
          </h3>
          <button class="text-xs sm:text-sm text-blue-600 dark:text-blue-400 font-medium hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
            Ver todas
          </button>
        </div>

        <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
          <!-- Transaction 1 -->
          <div class="flex items-center justify-between p-3 sm:p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-600">
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="bi bi-arrow-down-left text-green-600 dark:text-green-400 text-sm"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 dark:text-white text-sm sm:text-base truncate">
                  Pagamento - Dezembro
                </p>
                <p class="text-green-600 dark:text-green-400 font-semibold text-sm">
                  0,00 MZN
                </p>
              </div>
            </div>
            <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-slate-700 px-2 sm:px-3 py-1 rounded-lg whitespace-nowrap ml-2">
              15 Dez 2024
            </div>
          </div>

          <!-- Transaction 2 -->
          <div class="flex items-center justify-between p-3 sm:p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-600">
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="bi bi-arrow-down-left text-green-600 dark:text-green-400 text-sm"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 dark:text-white text-sm sm:text-base truncate">
                  Pagamento - Novembro
                </p>
                <p class="text-green-600 dark:text-green-400 font-semibold text-sm">
                  0,00 MZN
                </p>
              </div>
            </div>
            <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-slate-700 px-2 sm:px-3 py-1 rounded-lg whitespace-nowrap ml-2">
              15 Nov 2024
            </div>
          </div>

          <!-- Transaction 3 -->
          <div class="flex items-center justify-between p-3 sm:p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-600">
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="bi bi-arrow-up-right text-red-600 dark:text-red-400 text-sm"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 dark:text-white text-sm sm:text-base truncate">
                  Taxa de Plataforma
                </p>
                <p class="text-red-600 dark:text-red-400 font-semibold text-sm">
                  0,00 MZN
                </p>
              </div>
            </div>
            <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-slate-700 px-2 sm:px-3 py-1 rounded-lg whitespace-nowrap ml-2">
              10 Nov 2024
            </div>
          </div>

          <!-- Transaction 4 -->
          <div class="flex items-center justify-between p-3 sm:p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-600">
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="bi bi-arrow-down-left text-green-600 dark:text-green-400 text-sm"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 dark:text-white text-sm sm:text-base truncate">
                  Pagamento - Outubro
                </p>
                <p class="text-green-600 dark:text-green-400 font-semibold text-sm">
                  0,00 MZN
                </p>
              </div>
            </div>
            <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-slate-700 px-2 sm:px-3 py-1 rounded-lg whitespace-nowrap ml-2">
              15 Out 2024
            </div>
          </div>
        </div>

        <!-- Resumo do Mês -->
        <div class="mt-4 p-3 sm:p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
          <div class="flex justify-between items-center">
            <div>
              <p class="text-sm text-blue-800 dark:text-blue-200 font-medium">Saldo do Mês</p>
              <p class="text-lg font-bold text-blue-900 dark:text-white">0,00 MZN</p>
            </div>
            <div class="text-right">
              <p class="text-sm text-blue-800 dark:text-blue-200">Disponível</p>
              <p class="text-green-600 dark:text-green-400 font-semibold">0,00 MZN</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Métricas Adicionais -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mt-6">
      <!-- Cursos Mais Rentáveis -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
        <h4 class="font-bold text-slate-800 dark:text-white mb-4 text-sm sm:text-base">Cursos Mais Rentáveis</h4>
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-sm text-slate-600 dark:text-slate-400 truncate">JavaScript Avançado</span>
            <span class="text-sm font-semibold text-slate-800 dark:text-white">0,00 MZN</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm text-slate-600 dark:text-slate-400 truncate">React Completo</span>
            <span class="text-sm font-semibold text-slate-800 dark:text-white">0,00 MZN</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm text-slate-600 dark:text-slate-400 truncate">Node.js API</span>
            <span class="text-sm font-semibold text-slate-800 dark:text-white">0,00 MZN</span>
          </div>
        </div>
      </div>

      <!-- Próximas Metas -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
        <h4 class="font-bold text-slate-800 dark:text-white mb-4 text-sm sm:text-base">Próximas Metas</h4>
        <div class="space-y-3">
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span class="text-slate-600 dark:text-slate-400">Meta Mensal</span>
              <span class="font-semibold text-slate-800 dark:text-white">75%</span>
            </div>
            <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full">
              <div class="h-2 bg-green-500 rounded-full" style="width: 75%"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span class="text-slate-600 dark:text-slate-400">Alunos Ativos</span>
              <span class="font-semibold text-slate-800 dark:text-white">68%</span>
            </div>
            <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full">
              <div class="h-2 bg-blue-500 rounded-full" style="width: 68%"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ações Rápidas -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
        <h4 class="font-bold text-slate-800 dark:text-white mb-4 text-sm sm:text-base">Ações Rápidas</h4>
        <div class="space-y-2">
          <button class="w-full text-left px-3 py-2 text-sm bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors dark:text-white">
            <i class="bi bi-download mr-2"></i>Exportar Relatório
          </button>
          <button class="w-full text-left px-3 py-2 text-sm bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors dark:text-white">
            <i class="bi bi-receipt mr-2"></i>Ver Extrato
          </button>
          <button class="w-full text-left px-3 py-2 text-sm bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors dark:text-white">
            <i class="bi bi-gear mr-2"></i>Configurações
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  (function () {
    const yearSelect = document.querySelector('select');
    const ctx = document.getElementById('instructor-finance-chart').getContext('2d');
    let chart;

    const loadData = (year) => {
      const url = new URL(<?= json_encode(site_url('instructor/dashboard/financas/data')) ?>, window.location.origin);
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
      const year = yearSelect?.value || new Date().getFullYear();
      loadData(year).then(payload => {
        renderChart(payload.labels || [], payload.data || []);
      });
    };

    yearSelect?.addEventListener('change', refresh);
    refresh();
  })();
</script>

<?= $this->endSection() ?>
