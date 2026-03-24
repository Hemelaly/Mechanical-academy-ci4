<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Finanças<?= $this->endSection() ?>

<?= $this->section('financial') ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<div class="min-h-screen bg-slate-50 dark:bg-slate-900 pb-10">
  <div class="container mx-auto">

    <div class="mb-8">
      <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white mb-2">
        Financeiro
      </h1>
      <p class="text-slate-600 dark:text-slate-400 text-sm">
        Acompanhe sua receita e desempenho financeiro
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
      <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-4 sm:p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div class="flex-1 min-w-0">
            <p class="text-blue-100 text-sm font-medium mb-1 truncate">
              Receita Total
            </p>
            <h3 class="text-2xl sm:text-3xl font-bold mb-1 truncate">
              <?= number_format((float) ($totalRevenue ?? 0), 2, ',', '.') ?> MZN
            </h3>
            <div class="flex items-center gap-1">
              <i class="bi bi-arrow-up-short text-green-300 text-sm"></i>
              <span class="text-green-300 text-sm font-medium truncate">
                <?= number_format((float) ($monthRevenue ?? 0), 2, ',', '.') ?> MZN este mês
              </span>
            </div>
          </div>
          <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
            <i class="bi bi-currency-dollar text-white text-lg"></i>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-2xl p-4 sm:p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div class="flex-1 min-w-0">
            <p class="text-green-100 text-sm font-medium mb-1 truncate">
              Próximo Pagamento
            </p>
            <h3 class="text-2xl sm:text-3xl font-bold mb-1 truncate">
              <?= number_format((float) ($nextPayment ?? 0), 2, ',', '.') ?> MZN
            </h3>
            <div class="flex items-center gap-1">
              <i class="bi bi-calendar-check text-green-100 text-sm"></i>
              <span class="text-green-100 text-sm font-medium truncate">
                Valor pendente de liberação
              </span>
            </div>
          </div>
          <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
            <i class="bi bi-clock text-white text-lg"></i>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-2xl p-4 sm:p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div class="flex-1 min-w-0">
            <p class="text-purple-100 text-sm font-medium mb-1 truncate">
              Receita Média/Mês
            </p>
            <h3 class="text-2xl sm:text-3xl font-bold mb-1 truncate">
              <?= number_format((float) ($averageMonth ?? 0), 2, ',', '.') ?> MZN
            </h3>
            <div class="flex items-center gap-1">
              <i class="bi bi-graph-up text-green-300 text-sm"></i>
              <span class="text-green-300 text-sm font-medium truncate">
                <?= number_format((float) ($yearTotal ?? 0), 2, ',', '.') ?> MZN anual
              </span>
            </div>
          </div>
          <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
            <i class="bi bi-bar-chart text-white text-lg"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
          <div>
            <h3 class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white">
              Pagamentos aprovados por mês
            </h3>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
              Comparação mensal do ano selecionado
            </p>
          </div>

          <div class="flex items-center gap-2">
            <select id="finance-year-select"
              class="text-xs sm:text-sm bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg px-2 sm:px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </select>
          </div>
        </div>

        <div id="instructor-finance-chart" class="h-72 sm:h-80"></div>

        <div class="grid grid-cols-3 gap-3 mt-4">
          <div class="text-center p-3 bg-slate-50 dark:bg-slate-900 rounded-lg">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Maior</p>
            <p id="finance-stat-max" class="text-sm font-bold text-slate-800 dark:text-white">
              <?= number_format((float) ($chartMax ?? 0), 2, ',', '.') ?> MZN
            </p>
          </div>
          <div class="text-center p-3 bg-slate-50 dark:bg-slate-900 rounded-lg">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Média</p>
            <p id="finance-stat-avg" class="text-sm font-bold text-slate-800 dark:text-white">
              <?= number_format((float) ($chartAvg ?? 0), 2, ',', '.') ?> MZN
            </p>
          </div>
          <div class="text-center p-3 bg-slate-50 dark:bg-slate-900 rounded-lg">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Cresc.</p>
            <p id="finance-stat-growth" class="text-sm font-bold text-green-600">
              <?= number_format((float) ($chartGrowth ?? 0), 2, ',', '.') ?> MZN
            </p>
          </div>
        </div>
      </div>

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
          <?php if (!empty($latestTransactions)): ?>
            <?php foreach ($latestTransactions as $transaction): ?>
              <?php
              $isApproved = ($transaction->status_payment ?? '') === 'Aprovado';
              $iconBg = $isApproved
                ? 'bg-green-100 dark:bg-green-900'
                : (($transaction->status_payment ?? '') === 'Rejeitado'
                  ? 'bg-red-100 dark:bg-red-900'
                  : 'bg-yellow-100 dark:bg-yellow-900');

              $iconColor = $isApproved
                ? 'text-green-600 dark:text-green-400'
                : (($transaction->status_payment ?? '') === 'Rejeitado'
                  ? 'text-red-600 dark:text-red-400'
                  : 'text-yellow-600 dark:text-yellow-400');

              $amountColor = $isApproved
                ? 'text-green-600 dark:text-green-400'
                : (($transaction->status_payment ?? '') === 'Rejeitado'
                  ? 'text-red-600 dark:text-red-400'
                  : 'text-yellow-600 dark:text-yellow-400');
              ?>
              <div class="flex items-center justify-between p-3 sm:p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-600">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                  <div class="w-8 h-8 sm:w-10 sm:h-10 <?= $iconBg ?> rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="bi <?= $isApproved ? 'bi-arrow-down-left' : 'bi-arrow-up-right' ?> <?= $iconColor ?> text-sm"></i>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-800 dark:text-white text-sm sm:text-base truncate">
                      <?= esc($transaction->title_course ?? 'Curso') ?>
                    </p>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                      <p class="<?= $amountColor ?> font-semibold text-sm">
                        <?= number_format((float) ($transaction->amount_payment ?? 0), 2, ',', '.') ?> MZN
                        - <?= esc($transaction->status_payment ?? '') ?>
                      </p>
                      <span class="inline-flex items-center rounded-full bg-slate-200 px-2 py-0.5 text-[11px] font-medium text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                        <?= esc($transaction->method_payment_label ?? 'Nao informado') ?>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-slate-700 px-2 sm:px-3 py-1 rounded-lg whitespace-nowrap ml-2">
                  <?= !empty($transaction->created_at) ? date('d M Y', strtotime($transaction->created_at)) : '--' ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="p-4 text-sm text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900 rounded-xl">
              Ainda não existem transações registadas.
            </div>
          <?php endif; ?>
        </div>

        <div class="mt-4 p-3 sm:p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
          <div class="flex justify-between items-center">
            <div>
              <p class="text-sm text-blue-800 dark:text-blue-200 font-medium">Saldo do Mês</p>
              <p class="text-lg font-bold text-blue-900 dark:text-white">
                <?= number_format((float) ($monthRevenue ?? 0), 2, ',', '.') ?> MZN
              </p>
            </div>
            <div class="text-right">
              <p class="text-sm text-blue-800 dark:text-blue-200">Disponível</p>
              <p class="text-green-600 dark:text-green-400 font-semibold">
                <?= number_format((float) ($monthRevenue ?? 0), 2, ',', '.') ?> MZN
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mt-6">
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
        <h4 class="font-bold text-slate-800 dark:text-white mb-4 text-sm sm:text-base">Cursos Mais Rentáveis</h4>
        <div class="space-y-3">
          <?php if (!empty($topCourses)): ?>
            <?php foreach ($topCourses as $course): ?>
              <div class="flex justify-between items-center">
                <span class="text-sm text-slate-600 dark:text-slate-400 truncate">
                  <?= esc($course->title_course ?? 'Curso sem título') ?>
                </span>
                <span class="text-sm font-semibold text-slate-800 dark:text-white">
                  <?= number_format((float) ($course->total ?? 0), 2, ',', '.') ?> MZN
                </span>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-sm text-slate-500 dark:text-slate-400">
              Ainda não há cursos com receita aprovada.
            </p>
          <?php endif; ?>
        </div>
      </div>

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

      <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
        <h4 class="font-bold text-slate-800 dark:text-white mb-4 text-sm sm:text-base">Metodos de Pagamento</h4>
        <div class="space-y-2">
          <?php if (!empty($paymentMethods)): ?>
            <?php foreach ($paymentMethods as $method): ?>
              <div class="flex items-center justify-between px-3 py-2 text-sm bg-slate-100 dark:bg-slate-700 rounded-lg dark:text-white">
                <div>
                  <p class="font-medium"><?= esc($method->method_payment_label ?? 'Nao informado') ?></p>
                  <p class="text-xs text-slate-500 dark:text-slate-400"><?= (int) ($method->total_transactions ?? 0) ?> transacoes</p>
                </div>
                <span class="font-semibold text-slate-800 dark:text-white">
                  <?= number_format((float) ($method->total_amount ?? 0), 2, ',', '.') ?> MZN
                </span>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-sm text-slate-500 dark:text-slate-400">
              Ainda nao ha metodos de pagamento registados.
            </p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  (function () {
    const yearSelect = document.getElementById('finance-year-select');
    const chartEl = document.getElementById('instructor-finance-chart');

    const statMax = document.getElementById('finance-stat-max');
    const statAvg = document.getElementById('finance-stat-avg');
    const statGrowth = document.getElementById('finance-stat-growth');

    const currentYear = new Date().getFullYear();
    let chart = null;

    const formatMoney = (value) => {
      return `${Number(value || 0).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      })} MZN`;
    };

    const isDarkMode = () => document.documentElement.classList.contains('dark');

    const buildYearOptions = () => {
      yearSelect.innerHTML = '';

      for (let i = 0; i < 3; i++) {
        const year = currentYear - i;
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        if (i === 0) option.selected = true;
        yearSelect.appendChild(option);
      }
    };

    const loadData = async (year) => {
      const url = new URL(<?= json_encode(site_url('instructor/dashboard/financas/data')) ?>, window.location.origin);
      url.searchParams.set('year', year);

      const response = await fetch(url.toString(), {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      if (!response.ok) {
        throw new Error('Falha ao carregar dados financeiros.');
      }

      return response.json();
    };

    const getGrowth = (data) => {
      const valid = (data || []).filter(v => Number(v) > 0);
      if (valid.length < 2) return 0;
      return Number(valid[valid.length - 1]) - Number(valid[valid.length - 2]);
    };

    const updateStats = (payload) => {
      const data = payload.data || [];
      const max = payload.max || 0;
      const avg = payload.avg || 0;
      const growth = getGrowth(data);

      if (statMax) statMax.textContent = formatMoney(max);
      if (statAvg) statAvg.textContent = formatMoney(avg);
      if (statGrowth) {
        statGrowth.textContent = formatMoney(growth);
        statGrowth.classList.remove('text-green-600', 'text-red-600');
        statGrowth.classList.add(growth >= 0 ? 'text-green-600' : 'text-red-600');
      }
    };

    const renderChart = (payload, year) => {
      const labels = payload.labels || [];
      const data = payload.data || [];

      const dark = isDarkMode();
      const labelColor = dark ? '#cbd5e1' : '#475569';
      const gridColor = dark ? 'rgba(148, 163, 184, 0.12)' : 'rgba(148, 163, 184, 0.18)';

      const options = {
        chart: {
          type: 'area',
          height: 320,
          toolbar: {
            show: false
          },
          zoom: {
            enabled: false
          },
          animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 700
          },
          fontFamily: 'inherit',
          foreColor: labelColor
        },
        series: [{
          name: `Receita ${year}`,
          data: data
        }],
        xaxis: {
          categories: labels,
          labels: {
            style: {
              colors: labelColor
            }
          },
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          }
        },
        yaxis: {
          labels: {
            style: {
              colors: labelColor
            },
            formatter: function (value) {
              return formatMoney(value);
            }
          }
        },
        stroke: {
          curve: 'smooth',
          width: 4
        },
        fill: {
          type: 'gradient',
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.38,
            opacityTo: 0.05,
            stops: [0, 90, 100]
          }
        },
        dataLabels: {
          enabled: false
        },
        markers: {
          size: 5,
          strokeWidth: 0,
          hover: {
            size: 7
          }
        },
        colors: ['#2563eb'],
        grid: {
          borderColor: gridColor,
          strokeDashArray: 5,
          padding: {
            left: 10,
            right: 10
          }
        },
        tooltip: {
          theme: dark ? 'dark' : 'light',
          y: {
            formatter: function (value) {
              return formatMoney(value);
            }
          }
        },
        legend: {
          show: false
        },
        noData: {
          text: 'Sem dados financeiros'
        }
      };

      if (chart) {
        chart.updateOptions({
          xaxis: options.xaxis,
          yaxis: options.yaxis,
          grid: options.grid,
          tooltip: options.tooltip,
          chart: options.chart
        });
        chart.updateSeries(options.series);
        return;
      }

      chart = new ApexCharts(chartEl, options);
      chart.render();
    };

    const refresh = async () => {
      try {
        const year = yearSelect.value || currentYear;
        const payload = await loadData(year);
        renderChart(payload, year);
        updateStats(payload);
      } catch (error) {
        console.error('Erro ao carregar gráfico financeiro:', error);
      }
    };

    buildYearOptions();
    yearSelect.addEventListener('change', refresh);

    const observer = new MutationObserver(() => {
      if (!chart) return;
      refresh();
    });

    observer.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ['class']
    });

    refresh();
  })();
</script>

<?= $this->endSection() ?>
